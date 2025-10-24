import { Block, BlockHeader, BlockHeaderTitle, FlexContainer } from "../components";
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

type RelatedPlugin = {
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

const RelatedPlugins = () => {
    const { httpClient } = useGlobalContext();
    const { isLoading, error, data: relatedPlugins = {} } = useQuery({
        queryKey: ["other_plugins_data"],
        queryFn: () => httpClient?.setRoute("related_plugins_data").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): Record<string, RelatedPlugin> => {
            return data.data.plugins;
        }
    });

    const { mutate: runPluginAction } = useMutation({
        mutationFn: async ({ slug, action, key }: {
            slug: string,
            action: string,
            key: string,
        }) => {
            if (action === "download" || action === "activate") {
                const currentRelatedPluginsData: {
                    data: { plugins: Record<string, RelatedPlugin> },
                } = queryClient.getQueryData(["other_plugins_data"]) ?? { data: { plugins: {} } };
                const newPluginData = relatedPlugins;
                newPluginData[key] = {
                    ...newPluginData[key],
                    action: action === "download" ? "downloading" : action === "activate" ? "activating" : ""
                };
                currentRelatedPluginsData.data.plugins = newPluginData;
                queryClient.setQueryData(["other_plugins_data"], { ...currentRelatedPluginsData });
            }

            const updatedPluginItemResponse = await httpClient?.setRoute("do_plugin_action").setPayload({
                "slug": slug,
                "action": action,
            }).post();

            const updatedPluginItem = updatedPluginItemResponse?.data?.plugin;

            if (!updatedPluginItem) {
                console.error("Error fetching updated plugin item: ", updatedPluginItemResponse?.message);
                return;
            }

            return updatedPluginItem;
        },
        onSuccess: (data, variables) => {
            const currentRelatedPluginsData: {
                data: { plugins: Record<string, RelatedPlugin> },
            } = queryClient.getQueryData(["other_plugins_data"]) ?? { data: { plugins: {} } };
            const newPluginData = relatedPlugins;
            newPluginData[variables.key] = data;
            currentRelatedPluginsData.data.plugins = newPluginData;
            queryClient.setQueryData(["other_plugins_data"], { ...currentRelatedPluginsData });
            if (data.action === "activate") {
                runPluginAction({ slug: data.slug, action: data.action, key: variables.key })
            }
        }
    });

    const getRelatedPluginAction = (plugin: RelatedPlugin, pluginKey: string) => {
        switch (plugin.action) {
            case "upgrade-to-premium": {
                return () => {
                    window.open(plugin.upgrade_url, "_blank");
                    window.focus();
                }
            }
            case "installed": {
                return undefined;
            }
            default: {
                return () => runPluginAction({
                    slug: plugin.slug,
                    action: plugin.action,
                    key: pluginKey,
                })
            }

        }
    }

    return (
        <Block variant={"transparent"}>
            <BlockHeader>
                <BlockHeaderTitle>{__("Related Plugins", "metricool")}</BlockHeaderTitle>
            </BlockHeader>
            <FlexContainer direction={"column"} className={"!gap-2"}>
                {isLoading && (
                    <div>LOADING</div>
                )}
                {relatedPlugins && Object.entries(relatedPlugins).map(([pluginKey, pluginData]) => (
                    <ListItem
                        icon={"circle"}
                        iconColor={pluginData.options_prefix.split("_")[0]}
                        iconPosition={"left"}
                        action={getRelatedPluginAction(pluginData, pluginKey)}
                        actionText={pluginStatuses[pluginData.action]}
                        className={"font-semibold"}
                    >
                        {pluginData.title}
                    </ListItem>
                ))}
                {error && (
                    <FlexContainer direction={"row"} className={"justify-center items-center"}>
                        {__("There was an error fetching the data.", "metricool")}
                    </FlexContainer>
                )}
            </FlexContainer>
        </Block>
    );
};

export default RelatedPlugins;