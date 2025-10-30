import { Block, BlockHeader, Button, FlexContainer, type IconProps } from "../components";
import { __ } from "@wordpress/i18n";
import AccountTile from "./AccountTile.tsx";
import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";

type ConnectedAccount = {
    label: string,
    icon: IconProps["icon"],
    connectedClasses: string,
    unconnectedClasses: string,
    upsell: boolean,
    userName?: string,
};

const ConnectedAccounts = () => {
    const { httpClient, metricool } = useGlobalContext();
    const metricoolSSOLink = `https://app.metricool.com/evolution/settings/connections?blogId=${metricool.blogId}&userId=${metricool.userId}`;
    const { data: connectedAccountsData, isLoading, error } = useQuery({
        queryKey: ["connected", "accounts"],
        queryFn: () => httpClient?.setRoute("connected_networks").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data): ConnectedAccount[] => {
            return ([
                {
                    label: "Web",
                    icon: "web",
                    connectedClasses: "text-[#5c90a8]",
                    unconnectedClasses: "bg-[#5c90a8] border-[#5c90a8]",
                    upsell: false,
                    ...(data.data.web && data.data.web.url && { userName: data.data.web.url }),
                },
                {
                    label: "Twitter / X",
                    icon: "twitter",
                    connectedClasses: "text-black",
                    unconnectedClasses: "bg-black border-black",
                    upsell: true,
                    ...(data.data.twitter && { userName: data.data.twitter.username }),
                },
                {
                    label: "YouTube",
                    icon: "youtube",
                    connectedClasses: "text-youtube",
                    unconnectedClasses: "bg-youtube border-youtube",
                    upsell: false,
                    ...(data.data.youtube && { userName: data.data.youtube.username }),
                },
                {
                    label: "LinkedIn",
                    icon: "linkedIn",
                    connectedClasses: "text-linkedin",
                    unconnectedClasses: "bg-linkedin border-linkedin",
                    upsell: true,
                    ...(data.data.linkedin && { userName: data.data.linkedin.username }),
                },
            ]);
        }
    });

    return (
        <Block>
            <BlockHeader title={__("Connected Accounts", "metricool")}/>
            {isLoading && (
                <div>LOADING</div>
            )}
            {connectedAccountsData && (
                <div className={"grid grid-cols-1 xl:grid-cols-2 gap-2"}>
                    {connectedAccountsData.map((account) => (
                        <AccountTile {...account} link={metricoolSSOLink} />
                    ))}
                </div>
            )}
            {error && (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching your connected accounts.", "metricool")}
                </FlexContainer>
            )}
            <Button
                variant={"primary-gradient-ghost"}
                icon={"external-link"}
                iconPosition={"right"}
                iconClass={"svg-gradient"}
                link={metricoolSSOLink}>
                {__("Connected Accounts", "metricool")}
            </Button>
        </Block>
    );
};

export default ConnectedAccounts;