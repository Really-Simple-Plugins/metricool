import type { Notice } from "@/components/custom";
import { queryClient } from "../../main.tsx";

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