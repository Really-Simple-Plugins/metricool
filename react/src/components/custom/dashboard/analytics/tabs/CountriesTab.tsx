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
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";
import { useCountriesAnalyticsData } from "@/hooks/analytics/useCountriesAnalyticsData.tsx";

type CountriesDataTableColumns = { country: string, visitors: number, percentage: number };

const columns = [
    {
        accessorKey: "country",
        header: ({ column }: { column: Column<CountriesDataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Country", "metricool")}/>),
    },
    {
        accessorKey: "visitors",
        header: ({ column }: { column: Column<CountriesDataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Visitors", "metricool")}/>),
    },
    {
        accessorKey: "percentage",
        header: ({ column }: { column: Column<CountriesDataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Percent", "metricool")}/>),
    },
];

const CountriesTab = () => {
    const { metricoolDynamicUrl, metricool } = useGlobalContext();

    const {
        countriesDataQuery: {
            data: countryData,
            isLoading,
            error,
            refetch,
            errorUpdateCount
        }
    } = useCountriesAnalyticsData();

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

export { CountriesTab, type CountriesDataTableColumns };