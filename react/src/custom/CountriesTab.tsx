import { Button, type Column, DataTable, DataTableColumnHeader, FlexContainer, Icon } from "../components";
import { Chart } from "react-google-charts";
import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";
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
    const { httpClient, metricool } = useGlobalContext();
    const { data: countryData, isLoading, error } = useQuery({
        queryKey: ["analytics", "countries"],
        queryFn: () => httpClient?.setRoute("distribution/countries").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): { tableData: DataTableColumns[], chartData: string[][] } => data.data,
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
            {isLoading ? (
                <FlexContainer direction={"row"} className={"justify-center items-center w-full h-full"}>
                    <Icon icon={"loading"} className={"size-5"}/>
                </FlexContainer>
            ) : error ? (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching the data", "metricool")}
                </FlexContainer>
            ) : countryData && (
                <FlexContainer direction={"column"} className={"!gap-2"}>
                    <FlexContainer direction={"column"} className={"rounded-md overflow-hidden"}>
                        <div className={"min-h-[185px]"}>
                            <Chart
                                data={countryData.chartData}
                                chartType="GeoChart"
                                options={geochartOptions}
                                height={"185px"}
                                width={"100%"}
                            />
                        </div>
                    </FlexContainer>
                    <DataTable columns={columns} data={countryData.tableData} tableSettings={{ pageSize: 3 }}/>
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"w-full justify-end items-center"}>
                <Button
                    variant={"primary-gradient-ghost"}
                    icon={"external-link"}
                    iconPosition={"right"}
                    iconClass={"svg-gradient"}
                    link={`https://app.metricool.com/evolution/web?blogId=${metricool.blogId}&userId=${metricool.userId}`}
                >
                    {__("View Analytics", "metricool")}
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export default CountriesTab;