import {
    Button,
    type Column,
    DataTable,
    DataTableColumnHeader,
    FlexContainer,
    Icon,
    LoadingAndErrorState,
} from "@/components/shared";
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
            {!trafficData ? (
                <LoadingAndErrorState
                    error={error}
                    isLoading={isLoading}
                    errorUpdateCount={errorUpdateCount}
                    refetch={refetch}
                    supportTicketLink={metricool.trusted_urls.new_support_ticket}
                />
            ) : (
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

export { TrafficTab };