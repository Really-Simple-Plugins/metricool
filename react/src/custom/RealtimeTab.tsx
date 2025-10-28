import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";
import { Button, FlexContainer, LineChart } from "../components";
import MetricTile from "./MetricTile.tsx";

const RealtimeTab = () => {
    const { httpClient } = useGlobalContext();
    const lineChartXAxisDataKey = "label";

    const { data: realTimeData, isLoading, error } = useQuery({
        queryKey: ["analytics", "realtime"],
        queryFn: () => httpClient?.setRoute("realtime").get(),
        staleTime: 1000 * 60, // 1 minute
        refetchInterval: 1000 * 60, // 1 minute
        select: (data) => data.data,
    });

    const chartConfig = {
        pageViews: {
            label: __("Page Views", "metricool"),
            color: "tertiary",
        },
    };

    return (
        <FlexContainer direction={"column"} className={"justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {realTimeData && (
                <FlexContainer direction={"column"} className={"rounded-md bg-gray-50"}>
                    <FlexContainer direction={"row"} className={"justify-between pt-2 pl-2"}>
                        <div className={"text-md font-semibold"}>{__("Last 30 Minutes", "metricool")}</div>
                        <FlexContainer direction={"row"}>
                            <MetricTile metric={realTimeData.totals.pageViews.totalAmount} variant={"tertiary"}>
                                {__("Page Views", "metricool")}
                            </MetricTile>
                            <MetricTile metric={realTimeData.totals.visitors.totalAmount} variant={"primary"}>
                                {__("Visitors", "metricool")}
                            </MetricTile>
                        </FlexContainer>
                    </FlexContainer>
                    <hr/>
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

export default RealtimeTab;