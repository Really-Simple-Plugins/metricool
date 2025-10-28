import {
    Block,
    BlockDescription,
    BlockHeader,
    BlockHeaderTitle,
    Button,
    FlexContainer,
    type IconProps
} from "../components";
import { __ } from "@wordpress/i18n";
import AccountTile from "./AccountTile.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";

type ConnectedAccount = {
    label: string,
    icon: IconProps["icon"],
    connectedClasses: string,
    unconnectedClasses: string,
    upsell: boolean,
    userName?: string,
};

const ConnectionsSettings = () => {
    const { httpClient, metricool } = useGlobalContext();
    const { data: connectedAccountsData, isLoading, error } = useQuery({
        enabled: !!httpClient,
        queryKey: ["connected", "accounts"],
        queryFn: () => httpClient?.setRoute("connected_brands").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data): ConnectedAccount[] => {
            return ([
                {
                    label: "Web",
                    icon: "web",
                    connectedClasses: "text-web",
                    unconnectedClasses: "bg-web border-web",
                    upsell: false,
                    ...(data.data.data.networksData.webData && data.data.data.networksData.webData.url && { userName: data.data.data.networksData.webData.url }),
                },
                {
                    label: "Blog",
                    icon: "web",
                    connectedClasses: "text-blog",
                    unconnectedClasses: "bg-blog border-blog",
                    upsell: false,
                    ...(data.data.data.networksData.webData && data.data.data.networksData.webData.feedRss && { userName: data.data.data.networksData.webData.feedRss }),
                },
                {
                    label: "Facebook",
                    icon: "facebook",
                    connectedClasses: "text-facebook",
                    unconnectedClasses: "bg-facebook border-facebook",
                    upsell: false,
                    ...(data.data.data.networksData.facebookData && { userName: data.data.data.networksData.facebookData.username }),
                },
                {
                    label: "Instagram",
                    icon: "instagram",
                    connectedClasses: "text-instagram",
                    unconnectedClasses: "bg-instagram border-instagram",
                    upsell: false,
                    ...(data.data.data.networksData.instagramData && { userName: data.data.data.networksData.instagramData.username }),
                },
                {
                    label: "Threads",
                    icon: "threads",
                    connectedClasses: "text-threads",
                    unconnectedClasses: "bg-threads border-threads",
                    upsell: false,
                    ...(data.data.data.networksData.threadsData && { userName: data.data.data.networksData.threadsData.username }),
                },
                {
                    label: "Twitter / X",
                    icon: "twitter",
                    connectedClasses: "text-x",
                    unconnectedClasses: "bg-x border-x",
                    upsell: true,
                    ...(data.data.data.networksData.twitterData && { userName: data.data.data.networksData.twitterData.username }),
                },
                {
                    label: "Bluesky",
                    icon: "bluesky",
                    connectedClasses: "text-bluesky",
                    unconnectedClasses: "bg-bluesky border-bluesky",
                    upsell: false,
                    ...(data.data.data.networksData.blueskyData && { userName: data.data.data.networksData.blueskyData.username }),
                },
                {
                    label: "LinkedIn",
                    icon: "linkedIn",
                    connectedClasses: "text-linkedin",
                    unconnectedClasses: "bg-linkedin border-linkedin",
                    upsell: true,
                    ...(data.data.data.networksData.linkedinData && { userName: data.data.data.networksData.linkedinData.username }),
                },
                {
                    label: "Pinterest",
                    icon: "pinterest",
                    connectedClasses: "text-pinterest",
                    unconnectedClasses: "bg-pinterest border-pinterest",
                    upsell: false,
                    ...(data.data.data.networksData.pinterestData && { userName: data.data.data.networksData.pinterestData.username }),
                },
                {
                    label: `TikTok ${data.data.data.networksData.tiktokData ? data.data.data.networksData.tiktokData.accountType.toLowerCase() : ""}`,
                    icon: "tiktok",
                    connectedClasses: "text-tiktok",
                    unconnectedClasses: "bg-tiktok border-tiktok",
                    upsell: false,
                    ...(data.data.data.networksData.tiktokData && { userName: data.data.data.networksData.tiktokData.username }),
                },
                {
                    label: "Google Business Profile",
                    icon: "gbp",
                    connectedClasses: "text-gbp",
                    unconnectedClasses: "bg-gbp border-gbp",
                    upsell: false,
                    ...(data.data.data.networksData.gbpData && { userName: data.data.data.networksData.gbpData.username }),
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
                    label: "Twitch",
                    icon: "twitch",
                    connectedClasses: "text-twitch",
                    unconnectedClasses: "bg-twitch border-twitch",
                    upsell: false,
                    ...(data.data.data.networksData.twitchData && { userName: data.data.data.networksData.twitchData.username }),
                },
                {
                    label: "Meta Ads",
                    icon: "meta",
                    connectedClasses: "text-facebook",
                    unconnectedClasses: "bg-facebook border-facebook",
                    upsell: false,
                    ...(data.data.data.networksData.facebookAdsData && { userName: data.data.data.networksData.facebookAdsData.username }),
                },
                {
                    label: "Google Ads",
                    icon: "googleAds",
                    connectedClasses: "text-ga",
                    unconnectedClasses: "bg-ga border-ga",
                    upsell: false,
                    ...(data.data.data.networksData.googleAdsData && { userName: data.data.data.networksData.googleAdsData.providerUserId }),
                },
                {
                    label: "TikTok Ads",
                    icon: "tiktok",
                    connectedClasses: "text-tiktok",
                    unconnectedClasses: "bg-tiktok border-tiktok",
                    upsell: false,
                    ...(data.data.data.networksData.tiktokAdsData && { userName: data.data.data.networksData.tiktokAdsData.username }),
                },
            ]);
        }
    });

    return (
        <div className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Block>
                    <BlockHeader>
                        <BlockHeaderTitle>
                            {__("Connections", "metricool")}
                        </BlockHeaderTitle>
                        <BlockDescription>
                            {__("The accounts that are connected to Metricool", "metricool")}
                        </BlockDescription>
                    </BlockHeader>
                    <FlexContainer direction={"column"}>
                        {isLoading && (
                            <div>LOADING</div>
                        )}
                        {connectedAccountsData && (
                            <div className={"grid grid-cols-1 xl:grid-cols-2 gap-2"}>
                                {connectedAccountsData.map((account) => (
                                    <AccountTile {...account} />
                                ))}

                            </div>
                        )}
                        {error && (
                            <FlexContainer direction={"row"} className={"justify-center items-center"}>
                                {__("There was an error fetching your connected accounts.", "metricool")}
                            </FlexContainer>
                        )}
                    </FlexContainer>
                    <FlexContainer direction={"row"} className={"justify-end"}>
                        <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"} onClick={() => {
                            window.open(` https://app.metricool.com/evolution/settings/connections?blogId=${metricool.blogId}&userId=${metricool.userId}`, "_blank");
                            window.focus();
                        }}>
                            {__("Connected Accounts", "metricool")}
                        </Button>
                    </FlexContainer>
                </Block>
            </FlexContainer>
        </div>
    );
};

export default ConnectionsSettings;