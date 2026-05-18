import {
    Button,
    type Column,
    DataTable,
    DataTableColumnHeader,
    FlexContainer,
    Icon,
    LoadingAndErrorState,
} from "@/components/shared";
import { Chart } from "react-google-charts";
import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";

type DataTableColumns = { country: string, visitors: number, percentage: number };

const columns = [
    {
        accessorKey: "country",
        header: ({ column }: { column: Column<DataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Country", "metricool")}/>),
    },
    {
        accessorKey: "visitors",
        header: ({ column }: { column: Column<DataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Visitors", "metricool")}/>),
    },
    {
        accessorKey: "percentage",
        header: ({ column }: { column: Column<DataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Percent", "metricool")}/>),
    },
];

const CountriesTab = () => {
    const { httpClient, metricoolDynamicUrl, metricool } = useGlobalContext();
    const { data: countryData, isLoading, error, refetch, errorUpdateCount } = useQuery({
        queryKey: ["analytics", "countries"],
        queryFn: () => httpClient.setRoute("distribution/countries").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): { tableData: DataTableColumns[], chartData: string[][] } => {
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

    const geochartOptions = {
        datalessRegionColor: "white",
        backgroundColor: "#A6CEE3",
        colorAxis: { colors: ["white", "#E6A735", "#E6A735", "#E6A735", "#E6A735", "#E6A735", "#E6A735", "#E6A735", "#E6A735"] },
        defaultColor: "white",
        legend: "none",
    };

    return (
        <FlexContainer direction={"column"} className={"justify-between grow !gap-2"}>
            {!countryData ? (
                <LoadingAndErrorState
                    error={error}
                    isLoading={isLoading}
                    errorUpdateCount={errorUpdateCount}
                    refetch={refetch}
                    supportTicketLink={metricool.trusted_urls.new_support_ticket}
                />
            ) : (
                <FlexContainer direction={"column"} className={"!gap-2"}>
                    <FlexContainer direction={"column"} className={"rounded-md overflow-hidden"}>
                        <div className={"min-h-[185px]"}>
                            <Chart
                                data={countryData.chartData}
                                chartType={"GeoChart"}
                                options={geochartOptions}
                                height={"185px"}
                                width={"100%"}
                                chartVersion={"51"}
                            />
                        </div>
                    </FlexContainer>
                    <DataTable
                        columns={columns}
                        data={countryData.tableData}
                        tableSettings={{ pageSize: 3 }}
                    />
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"w-full justify-end items-center"}>
                <Button
                    variant={"primary-gradient-ghost"}
                    link={metricoolDynamicUrl.withPath("evolution/web")}
                >
                    <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                        {__("View Analytics", "metricool")}
                        <Icon icon={"external-link"} className={"svg-gradient"}/>
                    </FlexContainer>
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export { CountriesTab };