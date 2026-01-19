import { Block, BlockHeader, Button, FlexContainer, Icon, type IconProps } from "../components";
import { __ } from "@wordpress/i18n";
import AccountTile from "./AccountTile.tsx";
import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";

export type ConnectedAccount = {
    label: string,
    icon: IconProps["icon"],
    connectedClasses: string,
    unconnectedClasses: string,
    upsell: boolean,
    userName?: string,
    link: string,
    metricoolWebsitePath: string,
    isConnected: boolean,
};

const ConnectedAccounts = () => {
    const { httpClient, metricoolDynamicUrl } = useGlobalContext();
    const { data: connectedAccountsData, isLoading, error } = useQuery({
        queryKey: ["connected", "accounts"],
        queryFn: () => httpClient.setRoute("connected_networks").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (response): ConnectedAccount[] => {
            return ([
                {
                    label: "Web",
                    icon: "web",
                    connectedClasses: "text-web",
                    unconnectedClasses: "bg-web border-web hover:bg-transparent hover:**:data-content:text-web",
                    upsell: false,
                    metricoolWebsitePath: "evolution/web",
                    isConnected: !!response.data.web?.url,
                    ...(response.data.web && response.data.web.url && { userName: response.data.web.url }),
                },
                {
                    label: "Twitter / X",
                    icon: "twitter",
                    connectedClasses: "text-x",
                    unconnectedClasses: "bg-x border-x hover:bg-transparent hover:**:data-content:text-x",
                    upsell: true,
                    metricoolWebsitePath: "evolution/twitter",
                    isConnected: !!response.data.twitter?.username,
                    ...(response.data.twitter && { userName: response.data.twitter.username }),
                },
                {
                    label: "YouTube",
                    icon: "youtube",
                    connectedClasses: "text-youtube",
                    unconnectedClasses: "bg-youtube border-youtube hover:bg-transparent hover:**:data-content:text-youtube",
                    upsell: false,
                    metricoolWebsitePath: "evolution/youtube",
                    isConnected: !!response.data.youtube?.username,
                    ...(response.data.youtube && { userName: response.data.youtube.username }),
                },
                {
                    label: "LinkedIn",
                    icon: "linkedIn",
                    connectedClasses: "text-linkedin",
                    unconnectedClasses: "bg-linkedin border-linkedin hover:bg-transparent hover:**:data-content:text-linkedin",
                    upsell: true,
                    metricoolWebsitePath: "/evolution/linkedin",
                    isConnected: !!response.data.linkedin?.username,
                    ...(response.data.linkedin && { userName: response.data.linkedin.username }),
                },
            ]);
        }
    });

    return (
        <Block className={"xl:min-h-58 xl:max-h-58"}>
            <BlockHeader title={__("Connected Accounts", "metricool")}/>
            <FlexContainer direction={"column"} className={"w-full h-full justify-between"}>
                {isLoading ? (
                    <FlexContainer direction={"row"} className={"justify-center items-center w-full h-full"}>
                        <Icon icon={"loading"} className={"size-5"}/>
                    </FlexContainer>
                ) : error ? (
                    <FlexContainer direction={"row"} className={"justify-center items-center"}>
                        {__("There was an error fetching your connected accounts.", "metricool")}
                    </FlexContainer>
                ) : connectedAccountsData && (
                    <div className={"grid grid-cols-1 xl:grid-cols-2 gap-2"}>
                        {connectedAccountsData.map((account) => (
                            <AccountTile {...account} link={metricoolDynamicUrl.setPath(account.metricoolWebsitePath).toString()}/>
                        ))}
                    </div>
                )}
                <Button
                    variant={"primary-gradient-ghost"}
                    icon={"external-link"}
                    iconPosition={"right"}
                    iconClass={"svg-gradient"}
                    link={metricoolDynamicUrl.setPath("evolution/settings/connections").toString()}>
                    {__("Connected Accounts", "metricool")}
                </Button>
            </FlexContainer>
        </Block>
    );
};

export default ConnectedAccounts;