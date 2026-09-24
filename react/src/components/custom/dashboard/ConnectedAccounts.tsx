import { Block, BlockHeader, FlexContainer, Icon, Link, LoadingAndErrorState, } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { AccountTile } from "@/components/custom/general/AccountTile.tsx";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useConnectedAccountsData } from "@/hooks/useConnectedAccountsData.tsx";

/**
 * The ConnectedAccounts block used in {@link DashboardLayout}.
 *
 * Retrieves all data from {@link useConnectedAccountsData}.
 *
 * Maps over this array to render a {@link AccountTile} for each of the selected
 * networks.
 *
 * Displays everything in a {@link Block} with a fixed height (14.5rem)
 */
const ConnectedAccounts = () => {
    const { metricoolDynamicUrl, metricool } = useGlobalContext();

    const {
        connectedAccountsQuery: {
            data: connectedAccountsData,
            isLoading,
            error,
            refetch,
            errorUpdateCount
        }
    } = useConnectedAccountsData({ useLimitedDashboardList: true });

    return (
        <Block className={"xl:min-h-58 xl:max-h-58"}>
            <BlockHeader title={__("Connected Accounts", "metricool")}/>
            <FlexContainer direction={"column"} className={"w-full h-full justify-between"}>
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
                <Link
                    variant={"primary-gradient-ghost"}
                    href={metricoolDynamicUrl.withPath("evolution/settings/connections")}
                    className={"gap-2! items-center"}
                >
                    {__("Connected Accounts", "metricool")}
                    <Icon icon={"external-link"} className={"svg-gradient"}/>
                </Link>
            </FlexContainer>
        </Block>
    );
};

export { ConnectedAccounts };