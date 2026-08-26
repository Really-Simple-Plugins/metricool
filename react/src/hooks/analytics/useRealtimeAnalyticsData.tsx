import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import type { MetricData } from "@/hooks/analytics/useWebAnalyticsData.tsx";

type TimelineData = {
    timestamp: number,
    label: string,
    pageViews: number,
}[];

/**
 * Hook to retrieve Realtime Analytics data.
 *
 */
const useRealtimeAnalyticsData = () => {
    const { httpClient } = useGlobalContext();

    const realtimeDataQuery = useQuery({
        queryKey: ["analytics", "realtime"],
        queryFn: () => httpClient.setRoute("realtime").get(),
        staleTime: 1000 * 60, // 1 minute
        refetchInterval: 1000 * 60, // 1 minute
        select: (response): { totals: Record<string, Omit<MetricData, "trend">>, timelineData: TimelineData } => response.data,
    });

    return {
        realtimeDataQuery: realtimeDataQuery,
    };
};

export { useRealtimeAnalyticsData };