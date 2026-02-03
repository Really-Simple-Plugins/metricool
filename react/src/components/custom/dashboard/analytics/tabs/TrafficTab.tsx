import { Button, type Column, DataTable, DataTableColumnHeader, FetchingErrorAlert, FlexContainer, Icon } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
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
    const { httpClient, metricoolDynamicUrl, metricool } = useGlobalContext();
    const { data: trafficData, isLoading, error, refetch, errorUpdateCount } = useQuery({
        queryKey: ["analytics", "traffic"],
        queryFn: () => httpClient.setRoute("distribution/referers").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): { tableData: DataTableColumns[] } => data.data,
    });

    return (
        <FlexContainer direction={"column"} className={"justify-between grow"}>
            {isLoading ? (
                <FlexContainer direction={"row"} className={"justify-center items-center w-full grow"}>
                    <Icon icon={"loading"} className={"size-5"}/>
                </FlexContainer>
            ) : error ? (
                <FetchingErrorAlert errorUpdateCount={errorUpdateCount} refetch={refetch} supportTicketLink={metricool.trusted_urls.new_support_ticket}/>
            ) : trafficData && (
                <FlexContainer direction={"column"}>
                    <DataTable
                        data={trafficData.tableData}
                        columns={columns}
                        tableSettings={{ pageSize: 8 }}
                    />
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"w-full justify-end items-center"}>
                <Button
                    variant={"primary-gradient-ghost"}
                    icon={"external-link"}
                    iconPosition={"right"}
                    iconClass={"svg-gradient"}
                    link={metricoolDynamicUrl.withPath("evolution/web")}
                >
                    {__("View Analytics", "metricool")}
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export default TrafficTab;