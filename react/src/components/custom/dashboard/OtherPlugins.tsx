import {
    Block,
    BlockHeader,
    Button,
    FlexContainer,
    ListItem,
    LoadingAndErrorState,
    showToast
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useMutation, useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { queryClient } from "@/main.tsx";

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
 * The OtherPlugins block used in {@link DashboardLayout}.
 *
 * Contains a {@link useQuery} which fetches the other plugins data, an object
 * containing a {@link OtherPlugin} object for each plugin.
 *
 * Maps over this data to render a {@link ListItem} for each plugin.
 *
 * Contains a {@link useMutation} which runs the available action for that
 * plugin.
 *
 * Displays everything in a {@link Block} with a fixed height (14.5rem)
 */
const OtherPlugins = () => {
    const { httpClient, metricool } = useGlobalContext();
    const { isLoading, error, data: otherPlugins = {}, refetch, errorUpdateCount } = useQuery({
        queryKey: ["other_plugins_data"],
        queryFn: () => httpClient.setRoute("other_plugins_data").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): Record<string, OtherPlugin> => {
            return data.data.plugins;
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
    const { mutate: runPluginAction } = useMutation({
        onMutate: ({ action, key }: PluginActionArguments) => {
            if (action === "download" || action === "activate") {
                const currentOtherPluginsData: {
                    data: { plugins: Record<string, OtherPlugin> },
                } | undefined = queryClient.getQueryData(["other_plugins_data"]);

                if (!currentOtherPluginsData) {
                    return;
                } // abort - should never trigger as this mutation is not callable by users without other_plugins_data available but appeases TS

                const newPluginData = otherPlugins;
                newPluginData[key] = {
                    ...newPluginData[key],
                    action: action === "download" ? "downloading" : action === "activate" ? "activating" : ""
                };
                currentOtherPluginsData.data.plugins = newPluginData;
                queryClient.setQueryData(["other_plugins_data"], { ...currentOtherPluginsData });
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

            const newPluginData = otherPlugins;
            newPluginData[variables.key] = response.data.plugin;
            currentOtherPluginsData.data.plugins = newPluginData;
            queryClient.setQueryData(["other_plugins_data"], { ...currentOtherPluginsData });
            if (response.data.plugin.action === "activate") {
                runPluginAction({
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
                        onClick={() => runPluginAction({
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

    return (
        <Block variant={"transparent"} className={"xl:min-h-58 xl:max-h-58"}>
            <BlockHeader
                title={__("Other Plugins", "metricool")}
                action={(
                    <img
                        className={"h-3.5"}
                        src={`${metricool.assets_url}img/really-simple-plugins-logo.svg`}
                        alt={__("Really Simple Plugins logo", "metricool")}
                    />
                )}
            />
            <FlexContainer direction={"column"} className={"w-full h-full justify-between"}>
                {!otherPlugins ? (
                    <LoadingAndErrorState
                        error={error}
                        isLoading={isLoading}
                        errorUpdateCount={errorUpdateCount}
                        refetch={refetch}
                        supportTicketLink={metricool.trusted_urls.new_support_ticket}
                    />
                ) : (
                    <FlexContainer direction={"column"} className={"!gap-2"}>
                        {Object.entries(otherPlugins).map(([pluginKey, pluginData]) => (
                            <ListItem
                                icon={"circle"}
                                iconColor={pluginData.options_prefix.split("_")[0]}
                                iconPosition={"left"}
                                action={getOtherPluginAction(pluginData, pluginKey)}
                                className={"font-semibold"}
                            >
                                {pluginData.title}
                            </ListItem>
                        ))}
                    </FlexContainer>
                )}
            </FlexContainer>
        </Block>
    );
};

export { OtherPlugins };