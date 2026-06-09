import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

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
        select: (response) => response.data,
    });

    return {
        realtimeDataQuery: realtimeDataQuery,
    };
};

export { useRealtimeAnalyticsData };