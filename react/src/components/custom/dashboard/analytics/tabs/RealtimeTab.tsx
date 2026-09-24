import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";
import { FlexContainer, Icon, LineChart, Link, EmptyDataState } from "@/components/shared";
import { MetricTile } from "@/components/custom/dashboard/analytics/MetricTile.tsx";
import { useRealtimeAnalyticsData } from "@/hooks/analytics/useRealtimeAnalyticsData.tsx";
import { isEmpty } from "@/support/functions/utils.ts";

const RealtimeTab = () => {
    const { metricoolDynamicUrl, metricool } = useGlobalContext();
    const lineChartXAxisDataKey = "label";

    const {
        realtimeDataQuery: {
            data: realTimeData,
            isLoading,
            error,
            refetch,
            errorUpdateCount
        }
    } = useRealtimeAnalyticsData();

    const chartConfig = {
        pageViews: {
            label: __("Page Views", "metricool"),
            color: "tertiary",
        },
    };

    return (
        <FlexContainer direction={"column"} className={"justify-between grow gap-0!"}>
            {(!realTimeData || isEmpty(realTimeData.timelineData)) ? (
                <EmptyDataState
                    error={error}
                    isLoading={isLoading}
                    errorUpdateCount={errorUpdateCount}
                    refetch={refetch}
                    supportTicketLink={metricool.trusted_urls.new_support_ticket}
                    emptyStateTitle={__("No Real-time data", "metricool")}
                    emptyStateDescription={__("There appears to be no Real-time analytics data available right now.", "metricool")}
                />
            ) : (
                <FlexContainer direction={"column"} className={"rounded-md bg-gray-50 gap-2! p-2"}>
                    <FlexContainer direction={"row"} className={"justify-between"}>
                        <div className={"text-md font-semibold"}>{__("Last 30 Minutes", "metricool")}</div>
                        <FlexContainer direction={"row"} className={"gap-2! min-h-14"}>
                            <MetricTile
                                disabled={true}
                                metric={realTimeData.totals.pageViews.totalAmount}
                                variant={"tertiary"}
                                className={"opacity-100!"}
                            >
                                {realTimeData.totals.pageViews.label}
                            </MetricTile>
                            <MetricTile
                                disabled={true}
                                metric={realTimeData.totals.visitors.totalAmount}
                                variant={"primary"}
                                className={"opacity-100!"}
                            >
                                {realTimeData.totals.visitors.label}
                            </MetricTile>
                        </FlexContainer>
                    </FlexContainer>
                    <hr className={"-mx-2"}/>
                    <LineChart
                        chartSettings={{
                            xAxisKey: lineChartXAxisDataKey,
                        }}
                        maxHeightClass={"max-h-72.5"}
                        chartConfig={chartConfig}
                        chartData={realTimeData.timelineData}
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

export { RealtimeTab };