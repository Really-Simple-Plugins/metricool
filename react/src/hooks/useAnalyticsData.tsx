import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { type CountriesDataTableColumns, type TrafficDataTableColumns, periodFilterOptions, defaultPeriodFilter } from "@/components/custom/";

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
    tab: "analytics" | "realtime" | "countries" | "traffic",
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
const useAnalyticsData = ({ tab, selectedAnalyticsPeriod = defaultPeriodFilter.option }: UseAnalyticsDataProps) => {
    const { httpClient } = useGlobalContext();

    const analyticsDataQuery = useQuery({
        enabled: tab === "analytics",
        queryKey: ["analytics", selectedAnalyticsPeriod],
        queryFn: () => httpClient.setRoute("analytics").setFilters({ period: selectedAnalyticsPeriod }).get(),
        staleTime: 1000 * 60 * 60 * 12, // 12 hours
        gcTime: 1000 * 60 * 60 * 12, // 12 hours
        select: (data): { totals: Record<string, MetricData>, timelineData: TimelineData } => data.data,
    });

    const realtimeDataQuery = useQuery({
        enabled: tab === "realtime",
        queryKey: ["analytics", "realtime"],
        queryFn: () => httpClient.setRoute("realtime").get(),
        staleTime: 1000 * 60, // 1 minute
        refetchInterval: 1000 * 60, // 1 minute
        select: (data) => data.data,
    });

    const countriesDataQuery = useQuery({
        enabled: tab === "countries",
        queryKey: ["analytics", "countries"],
        queryFn: () => httpClient.setRoute("distribution/countries").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): { tableData: CountriesDataTableColumns[], chartData: string[][] } => {
            if (data.data.chartData.length === 0) {
                /**
                 * Google Geochart requires the data array to always have headers,
                 * else it throws an error. It also requires the headers array
                 * to always have a length of 2 if there is no further data, or
                 * it throws a different error. Therefor we return this custom
                 * array if the backend returns an empty chartData array.
                 */
                return {
                    ...data.data,
                    chartData: [["", ""]]
                };
            }
            return data.data;
        },
    });

    const trafficDataQuery = useQuery({
        enabled: tab === "traffic",
        queryKey: ["analytics", "traffic"],
        queryFn: () => httpClient.setRoute("distribution/referers").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): { tableData: TrafficDataTableColumns[] } => data.data,
    });

    return {
        analyticsDataQuery: analyticsDataQuery,
        realtimeDataQuery: realtimeDataQuery,
        countriesDataQuery: countriesDataQuery,
        trafficDataQuery: trafficDataQuery,
    };
};

export { useAnalyticsData };