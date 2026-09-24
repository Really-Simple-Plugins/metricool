import {
    type Column,
    DataTable,
    DataTableColumnHeader,
    EmptyDataState,
    FlexContainer,
    Icon,
    Link,
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useTrafficAnalyticsData } from "@/hooks/analytics/useTrafficAnalyticsData.tsx";
import { isEmpty } from "@/support/functions/utils.ts";

type TrafficDataTableColumns = { url: string, pageViews: number, percentage: number };

const columns = [
    {
        accessorKey: "url",
        header: ({ column }: { column: Column<TrafficDataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("URL", "metricool")}/>),
    },
    {
        accessorKey: "pageViews",
        header: ({ column }: { column: Column<TrafficDataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Page Views", "metricool")}/>),
    },
    {
        accessorKey: "percentage",
        header: ({ column }: { column: Column<TrafficDataTableColumns> }) => (
            <DataTableColumnHeader column={column} title={__("Percent", "metricool")}/>),
    },
];

const TrafficTab = () => {
    const { metricoolDynamicUrl, metricool } = useGlobalContext();

    const {
        trafficDataQuery: {
            data: trafficData,
            isLoading,
            error,
            refetch,
            errorUpdateCount
        }
    } = useTrafficAnalyticsData();

    return (
        <FlexContainer direction={"column"} className={"justify-between grow"}>
            {(!trafficData || isEmpty(trafficData))? (
                <EmptyDataState
                    error={error}
                    isLoading={isLoading}
                    errorUpdateCount={errorUpdateCount}
                    refetch={refetch}
                    supportTicketLink={metricool.trusted_urls.new_support_ticket}
                    emptyStateTitle={__("No Traffic data", "metricool")}
                    emptyStateDescription={__("There appears to be no Traffic data available right now.", "metricool")}
                />
            ) : (
                <FlexContainer direction={"column"} className={"grow"}>
                    <DataTable
                        data={trafficData.tableData}
                        columns={columns}
                        tableSettings={{
                            pageSize: 8,
                            roundedTopCornersTableHeader: true
                        }}
                    />
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"w-full justify-end items-center"}>
                <Link
                    variant={"primary-gradient-ghost"}
                    href={metricoolDynamicUrl.withPath("evolution/web")}
                    className={"gap-2! items-center"}
                >
                    {__("View Analytics", "metricool")}
                    <Icon icon={"external-link"} className={"svg-gradient"}/>
                </Link>
            </FlexContainer>
        </FlexContainer>
    );
};

export { TrafficTab, type TrafficDataTableColumns };