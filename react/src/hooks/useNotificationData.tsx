import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";
import { useLocation } from "@tanstack/react-router";

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

/**
 * Hook for retrieving Notification data.
 *
 * Contains a {@link useQuery} which fetches the notifications and filters which
 * are visible based on the current pathname using {@link useLocation}.
 */
const useNotificationData = () => {
    const { httpClient } = useGlobalContext();

    const pathname = useLocation({
        select: (location) => location.pathname.split("/").at(-1),
    });

    const notificationsDataQuery = useQuery({
        queryKey: ["notices"],
        queryFn: () => httpClient.setRoute("get_notices").get(),
        staleTime: Infinity, // never stale unless manually invalidated
        gcTime: Infinity, // data is never garbage collected
        select: (response): Record<string, Notice[]> => {
            const noticesWithVisibility = response.data.map((notice: Notice) => ({
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

    return {
        notificationsDataQuery: notificationsDataQuery,
    }
};

export { useNotificationData };