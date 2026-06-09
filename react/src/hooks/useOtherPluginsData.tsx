import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation, useQuery } from "@tanstack/react-query";
import { __ } from "@wordpress/i18n";
import { queryClient } from "@/main.tsx";
import { Button, showToast } from "@/components/shared";

const pluginStatuses: Record<string, string> = {
    installed: __("Installed", "metricool"),
    download: __("Install", "metricool"),
    activate: __("Activate", "metricool"),
    activating: __("Activating...", "metricool"),
    downloading: __("Downloading...", "metricool"),
    "upgrade-to-premium": __("Upgrade", "metricool"),
};

type OtherPlugin = {
    action: keyof typeof pluginStatuses,
    activation_slug: string,
    constant_free: string,
    constant_premium: string,
    create: string,
    options_prefix: string,
    slug: string,
    title: string,
    upgrade_url: string,
    url: string,
}

type PluginActionArguments = {
    slug: string,
    action: string,
    key: string,
}

/**
 * Hook for retrieving Other Plugins data.
 *
 * Contains a {@link useMutation} which runs the available action for that
 * plugin.
 *
 * Contains a {@link useQuery} which fetches the other plugins data, an object
 * containing a {@link OtherPlugin} object for each plugin.
 */
const useOtherPluginsData = () => {
    const { httpClient } = useGlobalContext();

    const otherPluginsDataQuery = useQuery({
        queryKey: ["other_plugins_data"],
        queryFn: () => httpClient.setRoute("other_plugins_data").get(),
        staleTime: Infinity, // never stale unless manually invalidated
        gcTime: Infinity, // data is never garbage collected
        select: (response): Record<string, OtherPlugin> => {
            return response.data.plugins;
        }
    });

    /**
     * onMutate - is run before the `mutationFn`, changes the `action` string
     * of the plugin and saves it in the queryContext. Used to give feedback
     * to the user.
     *
     * mutationFn - sends a POST request to the backend with the action to
     * execute.
     *
     * onSuccess - saves the updated state of the plugin returned by the POST
     * request in the queryContext. Recursively runs the mutationFn again if the
     * updated action is "activate" to immediately activate the plugin.
     *
     * onError - shows a toast with an error message to the user and
     * console.error()s the returned message.
     *
     * todo: NL14RSP4-135 (onError, undo `action` string change from onMutate)
     */
    const pluginActionMutation = useMutation({
        onMutate: ({ action, key }: PluginActionArguments) => {
            if (action === "download" || action === "activate") {
                const currentOtherPluginsData: {
                    data: { plugins: Record<string, OtherPlugin> },
                } | undefined = queryClient.getQueryData(["other_plugins_data"]);

                if (!currentOtherPluginsData) {
                    return;
                } // abort - should never trigger as this mutation is not callable by users without other_plugins_data available but appeases TS

                const newPluginData = otherPluginsDataQuery.data;
                if (newPluginData) {
                    newPluginData[key] = {
                        ...newPluginData[key],
                        action: action === "download" ? "downloading" : action === "activate" ? "activating" : ""
                    };
                    currentOtherPluginsData.data.plugins = newPluginData;
                    queryClient.setQueryData(["other_plugins_data"], { ...currentOtherPluginsData });
                }
            }
        },
        mutationFn: async ({ slug, action }: PluginActionArguments) => {
            return httpClient.setRoute("do_plugin_action").setPayload({
                "slug": slug,
                "action": action,
            }).post();
        },
        onSuccess: (response, variables) => {
            const currentOtherPluginsData: {
                data: { plugins: Record<string, OtherPlugin> },
            } | undefined = queryClient.getQueryData(["other_plugins_data"]);

            if (!currentOtherPluginsData) {
                return;
            } // abort - should never trigger as this mutation is not callable by users without other_plugins_data available but appeases TS

            const newPluginData = otherPluginsDataQuery.data;
            if (newPluginData) {
                newPluginData[variables.key] = response.data.plugin;
                currentOtherPluginsData.data.plugins = newPluginData;
                queryClient.setQueryData(["other_plugins_data"], { ...currentOtherPluginsData });
            }
            if (response.data.plugin.action === "activate") {
                pluginActionMutation.mutate({
                    slug: response.data.plugin.slug,
                    action: response.data.plugin.action,
                    key: variables.key,
                });
            }
        },
        onError: (error) => {
            showToast.error(__("There was an unexpected error executing the action for the selected plugin", "metricool"));
            console.error(error.message);
        }
    });

    const getOtherPluginAction = (plugin: OtherPlugin, pluginKey: string) => {
        switch (plugin.action) {
            case "upgrade-to-premium": {
                return (
                    <Button
                        className={"text-sm"}
                        variant={"link"}
                        link={plugin.upgrade_url}
                    >
                        {pluginStatuses[plugin.action]}
                    </Button>
                );
            }
            case "installed":
            case "downloading":
            case "activating": {
                return (<span className={"text-sm font-normal"}>{pluginStatuses[plugin.action]}</span>);
            }
            default: {
                return (
                    <Button
                        className={"text-sm"}
                        variant={"link"}
                        onClick={() => pluginActionMutation.mutate({
                            slug: plugin.slug,
                            action: plugin.action,
                            key: pluginKey,
                        })}
                    >
                        {pluginStatuses[plugin.action]}
                    </Button>
                );
            }

        }
    };

    return {
        otherPluginsDataQuery: otherPluginsDataQuery,
        pluginActionMutation: pluginActionMutation,
        getOtherPluginAction,
    };
};

export { useOtherPluginsData };