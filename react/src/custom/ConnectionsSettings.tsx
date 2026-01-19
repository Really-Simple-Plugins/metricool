import {
    Block,
    BlockHeader,
    Button,
    FlexContainer,
    Icon,
} from "../components";
import { __ } from "@wordpress/i18n";
import AccountTile from "./AccountTile.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";
import type { ConnectedAccount } from "./ConnectedAccounts.tsx";
import FetchingErrorFeedbackNotice from "./FetchingErrorFeedbackNotice.tsx";

const ConnectionsSettings = () => {
    const { httpClient, metricoolDynamicUrl } = useGlobalContext();
    const { data: connectedAccountsData, isLoading, error, refetch, errorUpdateCount } = useQuery({
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
                    label: "Blog",
                    icon: "web",
                    connectedClasses: "text-blog",
                    unconnectedClasses: "bg-blog border-blog hover:bg-transparent hover:**:data-content:text-blog",
                    upsell: false,
                    metricoolWebsitePath: "evolution/web",
                    isConnected: !!response.data.web?.feedRss,
                    ...(response.data.web && response.data.web.feedRss && { userName: response.data.web.feedRss }),
                },
                {
                    label: "Facebook",
                    icon: "facebook",
                    connectedClasses: "text-facebook",
                    unconnectedClasses: "bg-facebook border-facebook hover:bg-transparent hover:**:data-content:text-facebook",
                    upsell: false,
                    metricoolWebsitePath: "evolution/facebookPage",
                    isConnected: !!response.data.facebook?.username,
                    ...(response.data.facebook && { userName: response.data.facebook.username }),
                },
                {
                    label: "Instagram",
                    icon: "instagram",
                    connectedClasses: "text-instagram",
                    unconnectedClasses: "bg-instagram border-instagram hover:bg-transparent hover:**:data-content:text-instagram",
                    upsell: false,
                    metricoolWebsitePath: "evolution/instagram",
                    isConnected: !!response.data.instagram?.username,
                    ...(response.data.instagram && { userName: response.data.instagram.username }),
                },
                {
                    label: "Threads",
                    icon: "threads",
                    connectedClasses: "text-threads",
                    unconnectedClasses: "bg-threads border-threads hover:bg-transparent hover:**:data-content:text-threads",
                    upsell: false,
                    metricoolWebsitePath: "evolution/threads",
                    isConnected: !!response.data.threads?.username,
                    ...(response.data.threads && { userName: response.data.threads.username }),
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
                    label: "Bluesky",
                    icon: "bluesky",
                    connectedClasses: "text-bluesky",
                    unconnectedClasses: "bg-bluesky border-bluesky hover:bg-transparent hover:**:data-content:text-bluesky",
                    upsell: false,
                    metricoolWebsitePath: "evolution/bluesky",
                    isConnected: !!response.data.bluesky?.username,
                    ...(response.data.bluesky && { userName: response.data.bluesky.username }),
                },
                {
                    label: "LinkedIn",
                    icon: "linkedIn",
                    connectedClasses: "text-linkedin",
                    unconnectedClasses: "bg-linkedin border-linkedin hover:bg-transparent hover:**:data-content:text-linkedin",
                    upsell: true,
                    metricoolWebsitePath: "evolution/linkedin",
                    isConnected: !!response.data.linkedin?.username,
                    ...(response.data.linkedin && { userName: response.data.linkedin.username }),
                },
                {
                    label: "Pinterest",
                    icon: "pinterest",
                    connectedClasses: "text-pinterest",
                    unconnectedClasses: "bg-pinterest border-pinterest hover:bg-transparent hover:**:data-content:text-pinterest",
                    upsell: false,
                    metricoolWebsitePath: "evolution/pinterest",
                    isConnected: !!response.data.pinterest?.username,
                    ...(response.data.pinterest && { userName: response.data.pinterest.username }),
                },
                {
                    label: `TikTok ${response.data.tiktok ? response.data.tiktok.accountType.toLowerCase() : ""}`,
                    icon: "tiktok",
                    connectedClasses: "text-tiktok",
                    unconnectedClasses: "bg-tiktok border-tiktok hover:bg-transparent hover:**:data-content:text-tiktok",
                    upsell: false,
                    metricoolWebsitePath: "evolution/tiktok",
                    isConnected: !!response.data.tiktok?.username,
                    ...(response.data.tiktok && { userName: response.data.tiktok.username }),
                },
                {
                    label: "Google Business Profile",
                    icon: "gbp",
                    connectedClasses: "text-gbp",
                    unconnectedClasses: "bg-gbp border-gbp hover:bg-transparent hover:**:data-content:text-gbp",
                    upsell: false,
                    metricoolWebsitePath: "evolution/gmb",
                    isConnected: !!response.data.gbp?.username,
                    ...(response.data.gbp && { userName: response.data.gbp.username }),
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
                    label: "Twitch",
                    icon: "twitch",
                    connectedClasses: "text-twitch",
                    unconnectedClasses: "bg-twitch border-twitch hover:bg-transparent hover:**:data-content:text-twitch",
                    upsell: false,
                    metricoolWebsitePath: "evolution/twitch",
                    isConnected: !!response.data.twitch?.username,
                    ...(response.data.twitch && { userName: response.data.twitch.username }),
                },
                {
                    label: "Meta Ads",
                    icon: "meta",
                    connectedClasses: "text-facebook",
                    unconnectedClasses: "bg-facebook border-facebook hover:bg-transparent hover:**:data-content:text-facebook",
                    upsell: false,
                    metricoolWebsitePath: "evolution/facebookAds",
                    isConnected: !!response.data.facebookAds?.username,
                    ...(response.data.facebookAds && { userName: response.data.facebookAds.username }),
                },
                {
                    label: "Google Ads",
                    icon: "googleAds",
                    connectedClasses: "text-ga",
                    unconnectedClasses: "bg-ga border-ga hover:bg-transparent hover:**:data-content:text-ga",
                    upsell: false,
                    metricoolWebsitePath: "evolution/googleAds",
                    isConnected: !!response.data.googleAds?.providerUserId,
                    ...(response.data.googleAds && { userName: response.data.googleAds.providerUserId }),
                },
                {
                    label: "TikTok Ads",
                    icon: "tiktok",
                    connectedClasses: "text-tiktok",
                    unconnectedClasses: "bg-tiktok border-tiktok hover:bg-transparent hover:**:data-content:text-tiktok",
                    upsell: false,
                    metricoolWebsitePath: "evolution/tiktokAds",
                    isConnected: !!response.data.tiktokAds?.username,
                    ...(response.data.tiktokAds && { userName: response.data.tiktokAds.username }),
                },
            ]);
        }
    });

    return (
        <div className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Block className={"justify-between min-h-58"}>
                    <BlockHeader
                        title={__("Connections", "metricool")}
                        description={__("The accounts that are connected to Metricool", "metricool")}
                    />
                    <FlexContainer direction={"column"}>
                        {isLoading ? (
                            <FlexContainer direction={"row"} className={"justify-center items-center w-full grow"}>
                                <Icon icon={"loading"} className={"size-5"}/>
                            </FlexContainer>
                        ) : error ? (
                            <FetchingErrorFeedbackNotice errorUpdateCount={errorUpdateCount} refetch={refetch}/>
                        ) : connectedAccountsData && (
                            <div className={"grid grid-cols-1 xl:grid-cols-2 gap-2"}>
                                {connectedAccountsData.map((account) => (
                                    <AccountTile {...account} link={metricoolDynamicUrl.setPath(account.metricoolWebsitePath).toString()}/>
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
                            link={metricoolDynamicUrl.setPath("evolution/settings/connections").toString()}
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