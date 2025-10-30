import { Block, BlockHeader, BlockHeaderTitle, Alert, FlexContainer } from "../components";
import { __ } from "@wordpress/i18n";
import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useEffect } from "react";

type Notice = {
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
    type: "info" | "warning",
};

const NotificationsSidebar = () => {
    const { httpClient } = useGlobalContext();
    const { data: noticeData, isLoading, error } = useQuery({
        enabled: !!httpClient,
        queryKey: ["notices"],
        queryFn: () => httpClient?.setRoute("get_notices").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data): { allNotifications: Notice[], activeNotifications: Notice[] } => {
            console.log(data);
            return {
                allNotifications: data.data,
                activeNotifications: data.data.filter((notice: Notice) => notice.active),
            };
        },
    });

    useEffect(() => {
        console.log(noticeData, isLoading, error,);
    }, [noticeData, isLoading, error,]);

    return (
        <Block variant={"transparent"} className={"px-0"}>
            <BlockHeader className={"!gap-3"}>
                <BlockHeaderTitle>
                    {__("Notifications", "metricool")}
                </BlockHeaderTitle>
                <hr/>
            </BlockHeader>
            {noticeData?.activeNotifications && noticeData?.activeNotifications?.length > 0 ? (
                noticeData?.activeNotifications.map((notice) => (
                    <Alert title={notice.title} variant={notice.type}>
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
                    </Alert>))
            ) : (isLoading ? (
                    <div>LOADING</div>
                ) : (
                    <div className={"text-gray-400 italic"}>
                        {__("You currently have no notifications.", "metricool")}
                    </div>
                )
            )}
        </Block>
    );
};

export default NotificationsSidebar;