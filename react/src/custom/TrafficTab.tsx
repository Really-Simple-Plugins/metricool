import { Button, type Column, DataTable, DataTableColumnHeader, FlexContainer } from "../components";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";

type DataTableColumns = { url: string, pageViews: number, percentage: number };

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
        accessorKey: "percentage",
        header: ({ column }: { column: Column<DataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Percent", "metricool")}/>),
    },
];

const TrafficTab = () => {
    const { httpClient, metricool } = useGlobalContext();
    const { data: trafficData, isLoading, error } = useQuery({
        queryKey: ["analytics", "traffic"],
        queryFn: () => httpClient?.setRoute("distribution/referers").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): { tableData: DataTableColumns[] } => data.data,
    });

    return (
        <FlexContainer direction={"column"} className={"min-h-[290px] justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {trafficData && (
                <FlexContainer direction={"column"}>
                    <DataTable data={trafficData.tableData} columns={columns} tableSettings={{ pageSize: 7 }}/>
                </FlexContainer>
            )}
            {error && (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching the data.", "metricool")}
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

export default TrafficTab;