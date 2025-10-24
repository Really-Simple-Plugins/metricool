import { Button, type Column, DataTable, DataTableColumnHeader, FlexContainer } from "../components";
import { Chart } from "react-google-charts";
import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";

type DataTableColumns = { country: string, visitors: unknown, percent: string };

const columns = [
    {
        accessorKey: "country",
        header: ({ column }: { column: Column<DataTableColumns> }) => (<DataTableColumnHeader column={column} title={__("Country", "metricool")}/>),
    },
    {
        accessorKey: "visitors",
        header: ({ column }: { column: Column<DataTableColumns> }) => (<DataTableColumnHeader column={column} title={__("Visitors", "metricool")}/>),
    },
    {
        accessorKey: "percent",
        header: ({ column }: { column: Column<DataTableColumns> }) => (<DataTableColumnHeader column={column} title={__("Percent", "metricool")}/>),
    },
];

const CountriesTab = () => {
    const { metricool, httpClient } = useGlobalContext();
    const localizeCountryNames = new Intl.DisplayNames(metricool.locale, { type: "region" });
    const numberFormatter = Intl.NumberFormat(metricool.locale, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 3,
    });
    const { data: countryData, isLoading, error } = useQuery({
        queryKey: ["analytics", "countries"],
        queryFn: () => httpClient?.setRoute("statistics/countries").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data) => {
            const totalVisitors = Object.values(data.data).reduce((previous, current) => Number(previous) + Number(current));
            const countriesArray = Object.entries(data.data)
                .map(([countryCode, visitorAmount]) => [countryCode, localizeCountryNames.of(countryCode), visitorAmount]);
            return {
                chartData: [["value", "Country", __("Visitors", "metricool")], ...countriesArray],
                tableData: Object.entries(data.data).map(([countryCode, visitorAmount]) => ({ country: localizeCountryNames.of(countryCode) ?? "", visitors: visitorAmount, percent: `${numberFormatter.format((Number(visitorAmount) * 100) / Number(totalVisitors))}%` })),
            };
        }
    });

    const geochartOptions = {
        datalessRegionColor: "white",
        backgroundColor: "#A6CEE3",
        colorAxis: { colors: ["white", "#E6A735", "#E6A735", "#E6A735", "#E6A735", "#E6A735", "#E6A735", "#E6A735", "#E6A735"] },
        defaultColor: "white",
        legend: "none",
    };

    return (
        <FlexContainer direction={"column"} className={"min-h-[290px] justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {countryData && (
                <FlexContainer direction={"column"}>
                    <FlexContainer direction={"column"} className={"rounded-md overflow-hidden"}>
                        <Chart
                            data={countryData.chartData}
                            chartType="GeoChart"
                            options={geochartOptions}
                            height={"200px"}
                            width={"100%"}
                        />
                    </FlexContainer>
                    <DataTable columns={columns} data={countryData.tableData} tableSettings={{ pageSize: 5 }}/>
                </FlexContainer>
            )}
            {error && (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching the data.", "metricool")}
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"w-full justify-end items-center"}>
                <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"}>
                    {__("View Analytics", "metricool")}
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export default CountriesTab;