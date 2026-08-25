import {
    Block,
    BlockHeader,
    FlexContainer,
    LoadingAndErrorState,
    Notification
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useNotificationData } from "@/hooks/useNotificationData.tsx";

const NotificationsSidebar = () => {
    const { metricool } = useGlobalContext();

    const {
        notificationsDataQuery: { data: noticeData, isLoading, error, isFetched, errorUpdateCount, refetch }
    } = useNotificationData()

    return (
        <Block variant={"transparent"} className={"px-0"}>
            <BlockHeader title={__("Notifications", "metricool")} separator={true}/>
            {!noticeData ? (
                <LoadingAndErrorState
                    error={error}
                    isLoading={isLoading}
                    errorUpdateCount={errorUpdateCount}
                    refetch={refetch}
                    supportTicketLink={metricool.trusted_urls.new_support_ticket}
                />
            ) : noticeData.visibleNotifications && noticeData.visibleNotifications?.length > 0 && (
                noticeData.visibleNotifications.map((notice) => (
                    <Notification key={notice.id} title={notice.title} variant={notice.type}>
                        <FlexContainer direction={"column"} className={"gap-2!"}>
                            <div>{notice.text}</div>
                            {notice.action && (
                                <div className={"p-0.5"}>
                                    <a href={notice.action.link} target={notice.action.target} className={"underline"}>
                                        {notice.action.text}
                                    </a>
                                </div>
                            )}
                        </FlexContainer>
                    </Notification>
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

export { NotificationsSidebar };