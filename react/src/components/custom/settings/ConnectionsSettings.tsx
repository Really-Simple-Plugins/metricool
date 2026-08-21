import { Block, BlockHeader, Button, FlexContainer, Icon, LoadingAndErrorState, } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { AccountTile } from "@/components/custom/general/AccountTile.tsx";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useConnectedAccountsData } from "@/hooks/useConnectedAccountsData.tsx";

const ConnectionsSettings = () => {
    const { metricoolDynamicUrl, metricool } = useGlobalContext();

    const {
        connectedAccountsQuery: {
            data: connectedAccountsData,
            isLoading,
            error,
            refetch,
            errorUpdateCount
        }
    } = useConnectedAccountsData();

    return (
        <div className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Block className={"justify-between min-h-58"}>
                    <BlockHeader
                        title={__("Connections", "metricool")}
                        description={__("The accounts that are connected to Metricool", "metricool")}
                    />
                    <FlexContainer direction={"column"}>
                        {!connectedAccountsData ? (
                            <LoadingAndErrorState
                                error={error}
                                isLoading={isLoading}
                                errorUpdateCount={errorUpdateCount}
                                refetch={refetch}
                                supportTicketLink={metricool.trusted_urls.new_support_ticket}
                            />
                        ) : (
                            <div className={"grid grid-cols-1 xl:grid-cols-2 gap-2"}>
                                {connectedAccountsData.map((account) => (
                                    <AccountTile {...account} link={metricoolDynamicUrl.withPath(account.metricoolWebsitePath)}/>
                                ))}
                            </div>
                        )}
                    </FlexContainer>
                    <FlexContainer direction={"row"} className={"justify-end"}>
                        <Button
                            variant={"primary-gradient-ghost"}
                            link={metricoolDynamicUrl.withPath("evolution/settings/connections")}
                        >
                            <FlexContainer direction={"row"} className={"gap-2! items-center"}>
                                {__("Connected Accounts", "metricool")}
                                <Icon icon={"external-link"} className={"svg-gradient"}/>
                            </FlexContainer>

                        </Button>
                    </FlexContainer>
                </Block>
            </FlexContainer>
        </div>
    );
};

export { ConnectionsSettings };