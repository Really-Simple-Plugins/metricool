import { Block, BlockHeader, Button, FlexContainer, Icon, type IconProps } from "../components";
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
    link: string,
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
                    link: `https://app.metricool.com/evolution/web?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.web && data.data.web.url && { userName: data.data.web.url }),
                },
                {
                    label: "Twitter / X",
                    icon: "twitter",
                    connectedClasses: "text-black",
                    unconnectedClasses: "bg-black border-black",
                    upsell: true,
                    link: `https://app.metricool.com/evolution/twitter?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.twitter && { userName: data.data.twitter.username }),
                },
                {
                    label: "YouTube",
                    icon: "youtube",
                    connectedClasses: "text-youtube",
                    unconnectedClasses: "bg-youtube border-youtube",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/youtube?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.youtube && { userName: data.data.youtube.username }),
                },
                {
                    label: "LinkedIn",
                    icon: "linkedIn",
                    connectedClasses: "text-linkedin",
                    unconnectedClasses: "bg-linkedin border-linkedin",
                    upsell: true,
                    link: `https://app.metricool.com/evolution/linkedin?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.linkedin && { userName: data.data.linkedin.username }),
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
                            <AccountTile {...account} />
                        ))}
                    </div>
                )}
                <Button
                    variant={"primary-gradient-ghost"}
                    icon={"external-link"}
                    iconPosition={"right"}
                    iconClass={"svg-gradient"}
                    link={metricoolSSOLink}>
                    {__("Connected Accounts", "metricool")}
                </Button>
            </FlexContainer>
        </Block>
    );
};

export default ConnectedAccounts;