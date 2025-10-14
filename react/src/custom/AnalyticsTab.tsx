import FlexContainer from "./FlexContainer.tsx";
import { Button, type ChartConfig, LineChart, Select, SelectOption } from "../components";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";
import { useEffect, useState } from "react";
import MetricTile from "./MetricTile.tsx";
import { clsx } from "clsx";
import Icon from "../components/src/components/Icon.tsx";

const formatTimelineDataIntoChartData = (timelineData: string[][], dataKey: string, locale: string) => {
    return timelineData.map(([timeStamp, amount]) => ({
        date: new Date(Number(timeStamp)).toLocaleDateString(locale, { month: "short", day: "numeric" }),
        [dataKey]: Number(amount),
    }));
};

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
    const lineChartXAxisDataKey = "date";

    const { data: analyticsData, isLoading, error } = useQuery({
        queryKey: ["analytics"],
        queryFn: () => httpClient?.setRoute("analytics").setFilters({ start: startDate, end: endDate }).get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data) => {
            console.log(data);
            const formattedPageViews = formatTimelineDataIntoChartData(data.data.pageViews.data, "pageViews", metricool.locale);
            const formattedVisits = formatTimelineDataIntoChartData(data.data.visits.data, "visits", metricool.locale);
            const formattedVisitors = formatTimelineDataIntoChartData(data.data.visitors.data, "visitors", metricool.locale);
            const formattedPosts = formatTimelineDataIntoChartData(data.data.posts.data, "posts", metricool.locale);
            const formattedComments = formatTimelineDataIntoChartData(data.data.comments.data, "comments", metricool.locale);
            return {
                totals: {
                    pageViews: {
                        totalAmount: data.data.pageViews.data.reduce((accumulated: number, [, amount]: string[]) => (Number(accumulated) + Number(amount)), 0),
                        trend: data.data.pageViews.trend,
                    },
                    visits: {
                        totalAmount: data.data.visits.data.reduce((accumulated: number, [, amount]: string[]) => (Number(accumulated) + Number(amount)), 0),
                        trend: data.data.visits.trend,
                    },
                    visitors: {
                        totalAmount: data.data.visitors.data.reduce((accumulated: number, [, amount]: string[]) => (Number(accumulated) + Number(amount)), 0),
                        trend: data.data.visitors.trend,
                    },
                    posts: {
                        totalAmount: data.data.posts.data.reduce((accumulated: number, [, amount]: string[]) => (Number(accumulated) + Number(amount)), 0),
                        trend: data.data.posts.trend,
                    },
                    comments: {
                        totalAmount: data.data.comments.data.reduce((accumulated: number, [, amount]: string[]) => (Number(accumulated) + Number(amount)), 0),
                        trend: data.data.comments.trend,
                    },
                },
                timelineData: formattedPageViews.map((pageViewChartData, index) => ({
                    ...pageViewChartData,
                    visits: formattedVisits[index].visits,
                    visitors: formattedVisitors[index].visitors,
                    posts: formattedPosts[index] ? formattedPosts[index].posts : 0,
                    comments: formattedComments[index] ? formattedComments[index].comments : 0,
                }))
            };
        }
    });

    useEffect(() => {
        console.log("analytics");
        console.log(analyticsData, isLoading, error);

        console.log("formatted", startDate, endDate);

        console.log(Object.entries(chartConfig));
    }, [error, isLoading, analyticsData]);

    const toggleMetric = (dataKey: string) => {
        setChartConfig((prevState) => ({
            ...prevState,
            [dataKey]: { ...prevState[dataKey], hidden: !prevState[dataKey].hidden }
        }));
    };

    return (
        <FlexContainer direction={"column"} className={"min-h-[290px] justify-between grow"}>
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
                    <LineChart chartConfig={chartConfig} chartSettings={{ xAxisKey: lineChartXAxisDataKey }} chartData={analyticsData.timelineData} linesSettings={{ type: "linear" }}/>
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
                    <Button variant={"upsell"} icon={"file"} iconPosition={"left"}>
                        {__("Generate Report", "metricool")}
                    </Button>
                    <Button variant={"upsell"} icon={"download"} iconPosition={"left"}>
                        {__("Download CSV", "metricool")}
                    </Button>
                </FlexContainer>
                <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"}>
                    {__("View Analytics", "metricool")}
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export default AnalyticsTab;