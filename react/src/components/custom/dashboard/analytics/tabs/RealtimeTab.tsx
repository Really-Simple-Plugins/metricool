import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";
import { Button, FlexContainer, Icon, LineChart, LoadingAndErrorState } from "@/components/shared";
import { MetricTile } from "@/components/custom/dashboard/analytics/MetricTile.tsx";
import { useAnalyticsData } from "@/hooks/useAnalyticsData.tsx";

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
    } = useAnalyticsData({ tab: "realtime" });

    const chartConfig = {
        pageViews: {
            label: __("Page Views", "metricool"),
            color: "tertiary",
        },
    };

    return (
        <FlexContainer direction={"column"} className={"justify-between grow"}>
            {!realTimeData ? (
                <LoadingAndErrorState
                    error={error}
                    isLoading={isLoading}
                    errorUpdateCount={errorUpdateCount}
                    refetch={refetch}
                    supportTicketLink={metricool.trusted_urls.new_support_ticket}
                />
            ) : (
                <FlexContainer direction={"column"} className={"rounded-md bg-gray-50 !gap-2 p-2"}>
                    <FlexContainer direction={"row"} className={"justify-between"}>
                        <div className={"text-md font-semibold"}>{__("Last 30 Minutes", "metricool")}</div>
                        <FlexContainer direction={"row"} className={"!gap-2"}>
                            <MetricTile metric={realTimeData.totals.pageViews.totalAmount} variant={"tertiary"}>
                                {__("Page Views", "metricool")}
                            </MetricTile>
                            <MetricTile metric={realTimeData.totals.visitors.totalAmount} variant={"primary"}>
                                {__("Visitors", "metricool")}
                            </MetricTile>
                        </FlexContainer>
                    </FlexContainer>
                    <hr className={"-mx-2"}/>
                    <LineChart
                        chartSettings={{
                            xAxisKey: lineChartXAxisDataKey,
                            general: { height: 290 },
                        }}
                        chartConfig={chartConfig}
                        chartData={realTimeData.timelineData}
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

export { RealtimeTab };