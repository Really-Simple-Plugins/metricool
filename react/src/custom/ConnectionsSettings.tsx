import {
    Block,
    BlockHeader,
    Button,
    FlexContainer,
    Icon,
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
        queryFn: () => httpClient?.setRoute("connected_networks").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data): ConnectedAccount[] => {
            return ([
                {
                    label: "Web",
                    icon: "web",
                    connectedClasses: "text-web",
                    unconnectedClasses: "bg-web border-web",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/web?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.web && data.data.web.url && { userName: data.data.web.url }),
                },
                {
                    label: "Blog",
                    icon: "web",
                    connectedClasses: "text-blog",
                    unconnectedClasses: "bg-blog border-blog",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/web?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.web && data.data.web.feedRss && { userName: data.data.web.feedRss }),
                },
                {
                    label: "Facebook",
                    icon: "facebook",
                    connectedClasses: "text-facebook",
                    unconnectedClasses: "bg-facebook border-facebook",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/facebookPage?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.facebook && { userName: data.data.facebook.username }),
                },
                {
                    label: "Instagram",
                    icon: "instagram",
                    connectedClasses: "text-instagram",
                    unconnectedClasses: "bg-instagram border-instagram",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/instagram?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.instagram && { userName: data.data.instagram.username }),
                },
                {
                    label: "Threads",
                    icon: "threads",
                    connectedClasses: "text-threads",
                    unconnectedClasses: "bg-threads border-threads",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/threads?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.threads && { userName: data.data.threads.username }),
                },
                {
                    label: "Twitter / X",
                    icon: "twitter",
                    connectedClasses: "text-x",
                    unconnectedClasses: "bg-x border-x",
                    upsell: true,
                    link: `https://app.metricool.com/evolution/twitter?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.twitter && { userName: data.data.twitter.username }),
                },
                {
                    label: "Bluesky",
                    icon: "bluesky",
                    connectedClasses: "text-bluesky",
                    unconnectedClasses: "bg-bluesky border-bluesky",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/bluesky?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.bluesky && { userName: data.data.bluesky.username }),
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
                {
                    label: "Pinterest",
                    icon: "pinterest",
                    connectedClasses: "text-pinterest",
                    unconnectedClasses: "bg-pinterest border-pinterest",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/pinterest?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.pinterest && { userName: data.data.pinterest.username }),
                },
                {
                    label: `TikTok ${data.data.tiktok ? data.data.tiktok.accountType.toLowerCase() : ""}`,
                    icon: "tiktok",
                    connectedClasses: "text-tiktok",
                    unconnectedClasses: "bg-tiktok border-tiktok",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/tiktok?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.tiktok && { userName: data.data.tiktok.username }),
                },
                {
                    label: "Google Business Profile",
                    icon: "gbp",
                    connectedClasses: "text-gbp",
                    unconnectedClasses: "bg-gbp border-gbp",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/gmb?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.gbp && { userName: data.data.gbp.username }),
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
                    label: "Twitch",
                    icon: "twitch",
                    connectedClasses: "text-twitch",
                    unconnectedClasses: "bg-twitch border-twitch",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/twitch?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.twitch && { userName: data.data.twitch.username }),
                },
                {
                    label: "Meta Ads",
                    icon: "meta",
                    connectedClasses: "text-facebook",
                    unconnectedClasses: "bg-facebook border-facebook",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/facebookAds?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.facebookAds && { userName: data.data.facebookAds.username }),
                },
                {
                    label: "Google Ads",
                    icon: "googleAds",
                    connectedClasses: "text-ga",
                    unconnectedClasses: "bg-ga border-ga",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/googleAds?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.googleAds && { userName: data.data.googleAds.providerUserId }),
                },
                {
                    label: "TikTok Ads",
                    icon: "tiktok",
                    connectedClasses: "text-tiktok",
                    unconnectedClasses: "bg-tiktok border-tiktok",
                    upsell: false,
                    link: `https://app.metricool.com/evolution/tiktokAds?blogId=${metricool.blogId}&userId=${metricool.userId}`,
                    ...(data.data.tiktokAds && { userName: data.data.tiktokAds.username }),
                },
            ]);
        }
    });

    return (
        <div className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Block>
                    <BlockHeader
                        title={__("Connections", "metricool")}
                        description={__("The accounts that are connected to Metricool", "metricool")}
                    />
                    <FlexContainer direction={"column"}>
                        {isLoading ? (
                            <FlexContainer direction={"row"} className={"justify-center items-center w-full h-full"}>
                                <Icon icon={"loading"} iconClass={"size-5"}/>
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
                    </FlexContainer>
                    <FlexContainer direction={"row"} className={"justify-end"}>
                        <Button
                            variant={"primary-gradient-ghost"}
                            icon={"external-link"}
                            iconPosition={"right"}
                            iconClass={"svg-gradient"}
                            link={`https://app.metricool.com/evolution/settings/connections?blogId=${metricool.blogId}&userId=${metricool.userId}`}
                        >
                            {__("Connected Accounts", "metricool")}
                        </Button>
                    </FlexContainer>
                </Block>
            </FlexContainer>
        </div>
    );
};

export default ConnectionsSettings;