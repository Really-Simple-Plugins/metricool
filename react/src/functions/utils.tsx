import { queryClient } from "../main.tsx";
import type { Notice } from "../custom/NotificationsSidebar.tsx";

export const capitalizeFirstCharacter = (string: string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
};

export const getScrollProgressPercent = () => {
    const totalScrollableHeightInPixels =
        document.documentElement.scrollHeight - window.innerHeight;
    const roundedScrollPercentage =
        Math.round((window.scrollY / totalScrollableHeightInPixels) * 100);
    return roundedScrollPercentage;
};

export const setNoticeToVisible = (noticeId: string) => {
    const noticeData: { data: Notice[] } | undefined = queryClient.getQueryData(["notices"]);

    if (!noticeData) {
        return;
    } // abort - should never get triggered but appeases TS

    const updatedNoticeArray = noticeData.data.map((notice) => {
        if (notice.id === noticeId) {
            return {...notice, visible: true}
        }
        return notice;
    });
    queryClient.setQueryData(["notices"], { ...noticeData, data: [...updatedNoticeArray] });
}