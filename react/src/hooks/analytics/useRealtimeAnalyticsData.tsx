import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

type MetricData = {
    label: string,
    totalAmount: number,
}

type TimelineData = {
    timestamp: number,
    pageViews: number,
    label: string,
}[]

/**
 * Hook to retrieve Realtime Analytics data.
 *
 */
const useRealtimeAnalyticsData = () => {
    const { httpClient } = useGlobalContext();

    const realtimeDataQuery = useQuery({
        queryKey: ["analytics", "realtime"],
        queryFn: () => httpClient.get("realtime"),
        staleTime: 1000 * 60, // 1 minute
        refetchInterval: 1000 * 60, // 1 minute
        select: (response): { totals: Record<string, MetricData>, timelineData: TimelineData } => response.data,
    });

    return {
        realtimeDataQuery: realtimeDataQuery,
    };
};

export { useRealtimeAnalyticsData };