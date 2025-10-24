import { Button, type Column, DataTable, DataTableColumnHeader, FlexContainer } from "../components";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";
import { useEffect } from "react";

type DataTableColumns = { url: string, pageViews: unknown, percent: string };

const columns = [
    {
        accessorKey: "url",
        header: ({ column }: { column: Column<DataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("URL", "metricool")}/>),
    },
    {
        accessorKey: "pageViews",
        header: ({ column }: { column: Column<DataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Page Views", "metricool")}/>),
    },
    {
        accessorKey: "percent",
        header: ({ column }: { column: Column<DataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Percent", "metricool")}/>),
    },
];

const TrafficTab = () => {
    const { httpClient, metricool } = useGlobalContext();
    const numberFormatter = Intl.NumberFormat(metricool.locale, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 3,
    });
    const { data: trafficData, isLoading, error } = useQuery({
        queryKey: ["analytics", "traffic"],
        queryFn: () => httpClient?.setRoute("statistics/referers").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data) => {
            const totalPageViews = Object.values(data.data).reduce((previous, current) => Number(previous) + Number(current));
            console.log(totalPageViews);
            return (Object.entries(data.data).map(([url, pageViews]) => ({
                url: url,
                pageViews: pageViews,
                percent: `${numberFormatter.format((Number(pageViews) * 100) / Number(totalPageViews))}%`
            })));
        },
    });

    useEffect(() => {
        console.log("analytics");
        console.log(trafficData, isLoading, error);
    }, [error, isLoading, trafficData]);

    return (
        <FlexContainer direction={"column"} className={"min-h-[290px] justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {trafficData && (
                <FlexContainer direction={"column"}>
                    <DataTable data={trafficData} columns={columns}/>
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

export default TrafficTab;