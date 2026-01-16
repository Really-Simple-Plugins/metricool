import { Alert, Block, BlockHeader, FlexContainer, Icon } from "../components";
import { __ } from "@wordpress/i18n";
import { useQuery } from "@tanstack/react-query";
import { useLocation } from "@tanstack/react-router";
import { useGlobalContext } from "../context/GlobalContext.tsx";

export type Notice = {
    action: {
        text: string,
        link: string,
        target: string,
    },
    active: boolean,
    id: string,
    premium: boolean,
    route: string,
    text: string,
    title: string,
    visible: boolean,
    type: "info" | "warning",
};

const NotificationsSidebar = () => {
    const { httpClient } = useGlobalContext();
    const pathname = useLocation({
        select: (location) => location.pathname.split("/").at(-1),
    });
    const { data: noticeData, isLoading, error, isFetched } = useQuery({
        queryKey: ["notices"],
        queryFn: () => httpClient.setRoute("get_notices").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data): Record<string, Notice[]> => {
            const noticesWithVisibility = data.data.map((notice: Notice) => ({
                ...notice,
                visible: notice.visible ? notice.visible : notice.active && (notice.route === "general" || notice.route === pathname)
            }));
            return {
                allNotifications: noticesWithVisibility,
                activeNotifications: noticesWithVisibility.filter((notice: Notice) => notice.active),
                visibleNotifications: noticesWithVisibility.filter((notice: Notice) => notice.visible),
            };
        },
    });

    return (
        <Block variant={"transparent"} className={"px-0"}>
            <BlockHeader title={__("Notifications", "metricool")} separator={true}/>
            {isLoading ? (
                <FlexContainer direction={"row"} className={"justify-center items-center w-full h-full"}>
                    <Icon icon={"loading"} className={"size-5"}/>
                </FlexContainer>
            ) : error ? (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching your Notifications", "metricool")}
                </FlexContainer>
            ) : noticeData?.visibleNotifications && noticeData?.visibleNotifications?.length > 0 && (
                noticeData.visibleNotifications.map((notice) => (
                    <Alert key={notice.id} title={notice.title} variant={notice.type}>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <div>{notice.text}</div>
                            {notice.action && (
                                <div className={"p-0.5"}>
                                    <a href={notice.action.link} target={notice.action.target} className={"underline"}>
                                        {notice.action.text}
                                    </a>
                                </div>
                            )}
                        </FlexContainer>
                    </Alert>
                ))
            )}
            {isFetched && noticeData?.visibleNotifications?.length === 0 && (
                <div className={"text-gray-400 italic"}>
                    {__("You currently have no notifications.", "metricool")}
                </div>
            )}
        </Block>
    );
};

export default NotificationsSidebar;