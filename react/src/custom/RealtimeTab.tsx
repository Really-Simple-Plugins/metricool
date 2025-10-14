import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";
import { Button, LineChart } from "../components";
import FlexContainer from "./FlexContainer.tsx";
import MetricTile from "./MetricTile.tsx";

const formatTimelineDataIntoChartData = (timelineData: object, dataKey: string, locale: string) => {
    return Object.entries(timelineData).sort().map(([pageViewTimeStamp, pageViewAmount]) => ({
        [dataKey]: new Date(Number(pageViewTimeStamp)).toLocaleTimeString(locale, { timeStyle: "short" }).toLowerCase().replace(" ", ""),
        pageViews: pageViewAmount
    }));
};

const RealtimeTab = () => {
    const { httpClient, metricool } = useGlobalContext();
    const lineChartXAxisDataKey = "timestamp";

    const { data: realTimeData, isLoading, error } = useQuery({
        queryKey: ["analytics", "realtime"],
        queryFn: () => httpClient?.setRoute("realtime/sessions").get(),
        staleTime: 1000 * 60, // 1 minute
        refetchInterval: 1000 * 60, // 1 minute
        select: (data) => ({
            realTimeChartData: formatTimelineDataIntoChartData(data.data.timeline, lineChartXAxisDataKey, metricool.locale),
            totalPageViews: Object.values<number>(data.data.timeline).reduce((accumulatedPageViews, currentPageViews) => accumulatedPageViews + currentPageViews),
            totalVisitors: data.data.sessions.length,
        }),
    });

    const chartConfig = {
        pageViews: {
            label: __("Page Views", "metricool"),
            color: "var(--color-tertiary)",
        },
    };

    return (
        <FlexContainer direction={"column"} className={"min-h-[290px] justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {realTimeData && (
                <FlexContainer direction={"column"} className={"rounded-md bg-gray-50"}>
                    <FlexContainer direction={"row"} className={"justify-between pt-2 pl-2"}>
                        <div className={"text-md font-semibold"}>{__("Last 30 Minutes", "metricool")}</div>
                        <FlexContainer direction={"row"}>
                            <MetricTile metric={realTimeData.totalPageViews} variant={"tertiary"}>
                                {__("Page Views", "metricool")}
                            </MetricTile>
                            <MetricTile metric={realTimeData.totalVisitors} variant={"primary"}>
                                {__("Visitors", "metricool")}
                            </MetricTile>
                        </FlexContainer>
                    </FlexContainer>
                    <hr/>
                    <LineChart chartConfig={chartConfig} chartData={realTimeData.realTimeChartData} xAxisKey={lineChartXAxisDataKey}/>
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