import {
    Block,
    BlockHeader,
    EmptyDataState,
    FlexContainer,
    ListItem,
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useOtherPluginsData } from "@/hooks/useOtherPluginsData.tsx";

/**
 * The OtherPlugins block used in {@link DashboardLayout}.
 *
 * Maps over this data to render a {@link ListItem} for each plugin.
 *
 * Displays everything in a {@link Block} with a fixed height (14.5rem)
 */
const OtherPlugins = () => {
    const { metricool } = useGlobalContext();

    const {
        otherPluginsDataQuery: {
            isLoading,
            error,
            data: otherPluginsData,
            refetch,
            errorUpdateCount
        },
        getOtherPluginAction,
    } = useOtherPluginsData();

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
                {!otherPluginsData ? (
                    <EmptyDataState
                        error={error}
                        isLoading={isLoading}
                        errorUpdateCount={errorUpdateCount}
                        refetch={refetch}
                        supportTicketLink={metricool.trusted_urls.new_support_ticket}
                        emptyStateTitle={__("No Other Plugins", "metricool")}
                        emptyStateDescription={__("There appear to be no Other Plugins available.", "metricool")}
                    />
                ) : (
                    <FlexContainer direction={"column"} className={"gap-2!"}>
                        {Object.entries(otherPluginsData).map(([pluginKey, pluginData]) => (
                            <ListItem
                                iconProps={{
                                    icon: "circle",
                                    iconColor: pluginData.options_prefix.split("_")[0],
                                    iconPosition: "left",
                                }}
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