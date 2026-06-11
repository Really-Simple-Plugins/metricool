import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { type CountriesDataTableColumns } from "@/components/custom";

/**
 * Hook to retrieve Countries Analytics data.
 *
 */
const useCountriesAnalyticsData = () => {
    const { httpClient } = useGlobalContext();

    const countriesDataQuery = useQuery({
        queryKey: ["analytics", "countries"],
        queryFn: () => httpClient.setRoute("distribution/countries").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (response): { tableData: CountriesDataTableColumns[], chartData: string[][] } => {
            return response.data;
        },
    });

    return {
        countriesDataQuery: countriesDataQuery,
    };
};

export { useCountriesAnalyticsData };