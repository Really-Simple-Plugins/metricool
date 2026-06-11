import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { type TrafficDataTableColumns } from "@/components/custom";

/**
 * Hook to retrieve Traffic Analytics data.
 *
 */
const useTrafficAnalyticsData = () => {
    const { httpClient } = useGlobalContext();

    const trafficDataQuery = useQuery({
        queryKey: ["analytics", "traffic"],
        queryFn: () => httpClient.setRoute("distribution/referers").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (response): { tableData: TrafficDataTableColumns[] } => response.data,
    });

    return {
        trafficDataQuery: trafficDataQuery,
    };
};

export { useTrafficAnalyticsData };