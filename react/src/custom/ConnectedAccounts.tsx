import { Block, BlockHeader, BlockHeaderTitle, Button, FlexContainer, type IconProps } from "../components";
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
        queryFn: () => httpClient?.setRoute("connected_brands").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data): ConnectedAccount[] => {
            return [
                {
                    label: "Web",
                    icon: "web",
                    connectedClasses: "text-[#5c90a8]",
                    unconnectedClasses: "bg-[#5c90a8] border-[#5c90a8]",
                    upsell: false,
                    ...(data.data.data.networksData.webData && data.data.data.networksData.webData.url && { userName: data.data.data.networksData.webData.url }),
                },
                {
                    label: "Twitter / X",
                    icon: "twitter",
                    connectedClasses: "text-black",
                    unconnectedClasses: "bg-black border-black",
                    upsell: true,
                    ...(data.data.data.networksData.twitterData && { userName: data.data.data.networksData.twitterData.username }),
                },
                {
                    label: "YouTube",
                    icon: "youtube",
                    connectedClasses: "text-youtube",
                    unconnectedClasses: "bg-youtube border-youtube",
                    upsell: false,
                    ...(data.data.data.networksData.youtubeData && { userName: data.data.data.networksData.youtubeData.username }),
                },
                {
                    label: "LinkedIn",
                    icon: "linkedIn",
                    connectedClasses: "text-linkedin",
                    unconnectedClasses: "bg-linkedin border-linkedin",
                    upsell: true,
                    ...(data.data.data.networksData.linkedinData && { userName: data.data.data.networksData.linkedinData.username }),
                },
            ];
        }
    });

    return (
        <Block>
            <BlockHeader>
                <BlockHeaderTitle>{__("Connected Accounts", "metricool")}</BlockHeaderTitle>
            </BlockHeader>
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
            <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"} onClick={() => {
                window.open(metricoolSSOLink, "_blank");
                window.focus();
            }}>
                {__("Connected Accounts", "metricool")}
            </Button>
        </Block>
    );
};

export default ConnectedAccounts;