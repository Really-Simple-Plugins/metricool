import { Block, BlockHeader, FlexContainer, Icon, showToast } from "../components";
import { __ } from "@wordpress/i18n";
import ListItem from "./ListItem.tsx";
import { useMutation, useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { queryClient } from "../main.tsx";

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

const OtherPlugins = () => {
    const { httpClient, metricool } = useGlobalContext();
    const { isLoading, error, data: otherPlugins = {} } = useQuery({
        queryKey: ["other_plugins_data"],
        queryFn: () => httpClient.setRoute("other_plugins_data").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): Record<string, OtherPlugin> => {
            return data.data.plugins;
        }
    });

    const { mutate: runPluginAction } = useMutation({
        onMutate: ({ action, key }: {
            action: string,
            key: string,
        }) => {
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
        mutationFn: async ({ slug, action }: {
            slug: string,
            action: string,
            key: string,
        }) => {
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
                runPluginAction({ slug: response.data.plugin.slug, action: response.data.plugin.action, key: variables.key });
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
                return () => {
                    window.open(plugin.upgrade_url, "_blank");
                    window.focus();
                };
            }
            case "installed": {
                return undefined;
            }
            default: {
                return () => runPluginAction({
                    slug: plugin.slug,
                    action: plugin.action,
                    key: pluginKey,
                });
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
                {isLoading ? (
                    <FlexContainer direction={"row"} className={"justify-center items-center w-full grow"}>
                        <Icon icon={"loading"} className={"size-5"}/>
                    </FlexContainer>
                ) : error ? (
                    <FlexContainer direction={"row"} className={"justify-center items-center"}>
                        {__("There was an error fetching other plugin data.", "metricool")}
                    </FlexContainer>
                ) : otherPlugins && (
                    <FlexContainer direction={"column"} className={"!gap-2"}>
                        {Object.entries(otherPlugins).map(([pluginKey, pluginData]) => (
                            <ListItem
                                icon={"circle"}
                                iconColor={pluginData.options_prefix.split("_")[0]}
                                iconPosition={"left"}
                                action={getOtherPluginAction(pluginData, pluginKey)}
                                actionText={pluginStatuses[pluginData.action]}
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

export default OtherPlugins;