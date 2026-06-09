import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { periodFilterOptions, defaultPeriodFilter } from "@/components/custom";

type MetricData = {
    label: string,
    totalAmount: number,
    trend: "stable" | "up" | "down",
}

type TimelineData = {
    date: string,
    pageViews: number,
    comments: number,
    posts: number,
    visits: number,
    visitors: number,
}[]

type UseAnalyticsDataProps = {
    selectedAnalyticsPeriod?: keyof typeof periodFilterOptions,
};
/**
 * Hook to retrieve Analytics data.
 *
 * Receives the `tab` making the request as a string, which ensures that only
 * that request is enabled and fetched, not all.
 *
 * By passing in the `selectedAnalyticsPeriod` as a second arg in the `analytics`
 * query key, each period has its own query data stored in the `queryCache`. This
 * means we don't need a mutation to update the data, simply changing the
 * `periodFilter` state in the {@link AnalyticsTab} is enough for React to know
 * which data to display and only fetch it if the `queryCache` does not have
 * any data for that period yet.
 */
const useWebAnalyticsData = ({ selectedAnalyticsPeriod = defaultPeriodFilter.option }: UseAnalyticsDataProps) => {
    const { httpClient } = useGlobalContext();

    const webAnalyticsDataQuery = useQuery({
        queryKey: ["analytics", selectedAnalyticsPeriod],
        queryFn: () => httpClient.setRoute("analytics").setFilters({ period: selectedAnalyticsPeriod }).get(),
        staleTime: 1000 * 60 * 60 * 12, // 12 hours
        gcTime: 1000 * 60 * 60 * 12, // 12 hours
        select: (response): { totals: Record<string, MetricData>, timelineData: TimelineData } => response.data,
    });

    return {
        webAnalyticsDataQuery: webAnalyticsDataQuery,
    };
};

export { useWebAnalyticsData };