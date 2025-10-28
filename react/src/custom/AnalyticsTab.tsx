import { Button, type ChartConfig, FlexContainer, LineChart, Select, SelectOption } from "../components";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";
import { useState } from "react";
import MetricTile from "./MetricTile.tsx";
import { clsx } from "clsx";
import Icon from "../components/src/components/Icon.tsx";

type MetricData = {
    label: string,
    totalAmount: number,
    trend: "stable" | "up" | "down",
}

type TimelineData = {
    date: string,
    pageViews: number,
    comments: number,
    posts: number,
    visits: number,
    visitors: number,
}[]

const dateFilterOptions = [
    {
        label: __("Yesterday", "metricool"),
        option: "yesterday",
        isUpsell: false,
    },
    {
        label: __("Last week", "metricool"),
        option: "lastWeek",
        isUpsell: false,
    },
    {
        label: __("Current month", "metricool"),
        option: "currentMonth",
        isUpsell: false,
    },
    {
        label: __("Last 30 days", "metricool"),
        option: "last30Days",
        isUpsell: false,
    },
    {
        label: __("Previous month", "metricool"),
        option: "previousMonth",
        isUpsell: false,
    },
    {
        label: __("Last 3 months", "metricool"),
        option: "last3Months",
        isUpsell: false,
    },
    {
        label: __("Last 6 months", "metricool"),
        option: "last6Months",
        isUpsell: true,
    },
    {
        label: __("Last 12 months", "metricool"),
        option: "last12Months",
        isUpsell: true,
    },
];

const AnalyticsTab = () => {
    const { httpClient, metricool } = useGlobalContext();
    const numberFormatter = Intl.NumberFormat(metricool.locale, {
        notation: "compact",
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    });
    const dateFormatter = Intl.DateTimeFormat("en-CA", { year: "numeric", month: "2-digit", day: "2-digit" });
    const dayInMilliSeconds = 1000 * 60 * 60 * 24;
    const startDate = dateFormatter.format(new Date(new Date().getTime() - dayInMilliSeconds * 30)).replaceAll("-", "");
    const endDate = dateFormatter.format(new Date()).replaceAll("-", "");
    const [periodFilter, setPeriodFilter] = useState(dateFilterOptions[3].option);
    const [chartConfig, setChartConfig] = useState<ChartConfig>({
        pageViews: {
            label: __("Page Views", "metricool"),
            color: "tertiary",
            hidden: false,
        },
        visits: {
            label: __("Visits", "metricool"),
            color: "light-green",
            hidden: false,
        },
        visitors: {
            label: __("Visitors", "metricool"),
            color: "primary",
            hidden: false,
        },
        posts: {
            label: __("Posts", "metricool"),
            color: "secondary",
            hidden: false,
        },
        comments: {
            label: __("Comments", "metricool"),
            color: "primary-dark",
            hidden: false,
        },
    });
    const lineChartXAxisDataKey = "label";

    const { data: analyticsData, isLoading, error } = useQuery({
        queryKey: ["analytics"],
        queryFn: () => httpClient?.setRoute("analytics").setFilters({ start: startDate, end: endDate }).get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): { totals: Record<string, MetricData>, timelineData: TimelineData } => data.data,
    });

    const toggleMetric = (dataKey: string) => {
        setChartConfig((prevState) => ({
            ...prevState,
            [dataKey]: { ...prevState[dataKey], hidden: !prevState[dataKey].hidden }
        }));
    };

    return (
        <FlexContainer direction={"column"} className={"justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {analyticsData && (
                <FlexContainer direction={"column"} className={"rounded-md bg-gray-50"}>
                    <FlexContainer direction={"row"} className={"justify-between p-2 flex-wrap"}>
                        <div className={"text-md font-semibold"}>{__("Website", "metricool")}</div>
                        <FlexContainer direction={"row"} className={"flex-wrap justify-end w-full"}>
                            {Object.entries(analyticsData.totals).map(([metricKey, metricData]) => (
                                <MetricTile
                                    onClick={() => toggleMetric(metricKey)}
                                    metric={numberFormatter.format(metricData.totalAmount)}
                                    trend={metricData.trend}
                                    variant={chartConfig[metricKey].color}
                                    inactive={chartConfig[metricKey].hidden}
                                    disabled={metricData.totalAmount === 0}
                                >
                                    {chartConfig[metricKey].label}
                                </MetricTile>
                            ))}
                        </FlexContainer>
                    </FlexContainer>
                    <hr/>
                    <LineChart
                        chartConfig={chartConfig}
                        chartSettings={{
                            xAxisKey: lineChartXAxisDataKey,
                            general: { height: 290 },
                        }}
                        chartData={analyticsData.timelineData}
                        linesSettings={{ type: "monotone" }}/>
                </FlexContainer>
            )}
            {error && (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching the data.", "metricool")}
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"justify-between items-center"}>
                <FlexContainer direction={"row"} className={"sm:flex-col xl:flex-row flex-wrap"}>
                    <Select
                        defaultValue={"last30Days"}
                        icon={{ icon: "upsell", iconClass: "bg-upsell size-2.5 p-0.5 text-black rounded-full" }}
                        inputSize={"sm"}
                        className={"border-neutral-200 font-semibold !text-black max-w-fit flex-row-reverse"}
                        onValueChange={(value) => setPeriodFilter(value)}
                        placeholder={dateFilterOptions.find((filterOption) => filterOption.option === periodFilter)?.label}
                    >
                        {dateFilterOptions.map((filterOption) => filterOption.isUpsell ? (
                            <FlexContainer
                                direction={"row"}
                                className={clsx("items-center rounded-xs py-1.5 pr-8 pl-2 !gap-2 text-sm outline-hidden select-none font-semibold bg-secondary-light hover:bg-upsell focus:bg-upsell")}
                            >
                                <span className="flex size-3.5 items-center justify-center">
                                    <Icon icon={"upsell"} className={"bg-upsell rounded-full text-black size-2.5 p-0.5"}/>
                                </span>
                                {filterOption.label}
                            </FlexContainer>
                            ) : (
                                <SelectOption
                                    value={filterOption.option}
                                    className={clsx("font-semibold hover:bg-primary-light/50 focus:bg-primary-light/50")}
                                >
                                    {filterOption.label}
                                </SelectOption>
                            )
                        )}
                    </Select>
                    <Button variant={"upsell"} size={"sm"} icon={"file"} iconPosition={"left"}>
                        {__("Report", "metricool")}
                    </Button>
                </FlexContainer>
                <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"} onClick={() => {
                    window.open(`https://app.metricool.com/evolution/web?blogId=${metricool.blogId}&userId=${metricool.userId}`, "_blank");
                    window.focus();
                }}>
                    {__("View Analytics", "metricool")}
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export default AnalyticsTab;