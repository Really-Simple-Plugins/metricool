import {
    Button,
    type ChartConfig,
    DisabledSelectOption,
    FlexContainer,
    LineChart,
    Select,
    SelectOption,
    showToast,
} from "../components";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useMutation, useQuery } from "@tanstack/react-query";
import { useState } from "react";
import MetricTile from "./MetricTile.tsx";
import { clsx } from "clsx";
import Icon from "../components/src/components/Icon.tsx";
import { queryClient } from "../main.tsx";

type MetricData = {
    label: string,
    totalAmount: number,
    trend: "stable" | "up" | "down",
}

export type PeriodFilterOption = {
    label: string,
    option: string,
    isUpsell: boolean,
    xAxisInterval: number,
}

type TimelineData = {
    date: string,
    pageViews: number,
    comments: number,
    posts: number,
    visits: number,
    visitors: number,
}[]

const periodFilterOptions: Record<string, PeriodFilterOption> = {
    lastWeek: {
        label: __("Last week", "metricool"),
        option: "lastweek",
        isUpsell: false,
        xAxisInterval: 0,
    },
    currentMonth: {
        label: __("Current month", "metricool"),
        option: "currentmonth",
        isUpsell: false,
        xAxisInterval: 0,
    },
    last30Days: {
        label: __("Last 30 days", "metricool"),
        option: "last30days",
        isUpsell: false,
        xAxisInterval: 2,
    },
    previousMonth: {
        label: __("Previous month", "metricool"),
        option: "previousmonth",
        isUpsell: false,
        xAxisInterval: 2,
    },
    last3Months: {
        label: __("Last 3 months", "metricool"),
        option: "last3months",
        isUpsell: false,
        xAxisInterval: 6,
    },
    last6Months: {
        label: __("Last 6 months", "metricool"),
        option: "last6months",
        isUpsell: true,
        xAxisInterval: 29,
    },
    last12Months: {
        label: __("Last 12 months", "metricool"),
        option: "last12months",
        isUpsell: true,
        xAxisInterval: 29,
    },
};

const getCurrentPeriodFilter = (defaultPeriodFilter: PeriodFilterOption, activePeriodFilterInContext: PeriodFilterOption | undefined): PeriodFilterOption => {
    if (activePeriodFilterInContext) {
        return activePeriodFilterInContext;
    }

    return defaultPeriodFilter;
};

const AnalyticsTab = () => {
    const { httpClient, metricool, dispatch, dashboardSettings } = useGlobalContext();
    const metricoolSSOLink = `https://app.metricool.com/user-settings/plan?blogId=${metricool.blogId}&userId=${metricool.userId}`;
    const numberFormatter = Intl.NumberFormat(metricool.locale, {
        notation: "compact",
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    });
    const defaultPeriodFilter = periodFilterOptions.last30Days;
    const [periodFilter, setPeriodFilter] = useState(getCurrentPeriodFilter(defaultPeriodFilter, dashboardSettings.analytics?.activePeriodFilter));
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
    const [xAxisInterval, setXAxisInterval] = useState(defaultPeriodFilter.xAxisInterval);

    const { data: analyticsData, isLoading, error, isSuccess: hasAnalyticsData } = useQuery({
        queryKey: ["analytics"],
        queryFn: () => httpClient?.setRoute("analytics").setFilters({ period: periodFilter.option }).get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): { totals: Record<string, MetricData>, timelineData: TimelineData } => data.data,
    });

    const toggleMetric = (dataKey: string) => {
        setChartConfig((prevState) => ({
            ...prevState,
            [dataKey]: { ...prevState[dataKey], hidden: !prevState[dataKey].hidden }
        }));
    };

    const { mutate: updateChartData, isPending } = useMutation({
        mutationFn: async ({ period }: {
            period: string,
        }) => {
            return httpClient?.setRoute("analytics").setFilters({ period: period }).get();
        },
        onSuccess: (response) => {
            queryClient.setQueryData(["analytics"], { ...response });
            if (periodFilter === periodFilterOptions.currentMonth) {
                const maxPossibleDataPointsOnXAxis = 14;
                const isCurrentMonthTooLongForXAxis = response.data.timelineData.length >= maxPossibleDataPointsOnXAxis;
                const appropriateXAxisIntervalForCurrentMonth =
                    isCurrentMonthTooLongForXAxis ?
                    periodFilterOptions.previousMonth.xAxisInterval :
                    periodFilterOptions.currentMonth.xAxisInterval;
                setXAxisInterval(appropriateXAxisIntervalForCurrentMonth);
            } else {
                setXAxisInterval(periodFilter.xAxisInterval);
            }
        },
        onError: (error) => {
            showToast.error(__("There was an fetching the chart data", "metricool"));
            console.error(error.message);
        }
    });

    return (
        <FlexContainer direction={"column"} className={"justify-between grow"}>
            {isLoading ? (
                <FlexContainer direction={"row"} className={"justify-center items-center w-full h-full"}>
                    <Icon icon={"loading"} className={"size-5"}/>
                </FlexContainer>
            ) : error ? (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching the data", "metricool")}
                </FlexContainer>
            ) : hasAnalyticsData && (
                <FlexContainer direction={"column"} className={"relative rounded-md bg-gray-50 !gap-2 p-2"}>
                    <FlexContainer direction={"row"} className={"flex w-full justify-end !gap-2"}>
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
                    <hr className={"-mx-2"}/>
                    {isPending && (
                        <div className={"absolute w-full h-full bg-white opacity-45"}>
                            <FlexContainer direction={"row"} className={"justify-center items-center w-full h-full"}>
                                <Icon icon={"loading"} className={"size-5"}/>
                            </FlexContainer>
                        </div>
                    )}
                    <LineChart
                        className={clsx(isPending && "opacity-45")}
                        chartConfig={chartConfig}
                        chartSettings={{
                            xAxisKey: lineChartXAxisDataKey,
                            general: { height: 290 },
                            xAxis: { interval: xAxisInterval },
                        }}
                        chartData={analyticsData.timelineData}
                        linesSettings={{ type: "monotone" }}
                    />
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"justify-between items-center"}>
                <FlexContainer direction={"row"} className={"flex-wrap !gap-2"}>
                    {hasAnalyticsData && (
                        <>
                            <Select
                                defaultValue={periodFilter.option}
                                icon={{ icon: "upsell", className: "bg-upsell size-2.5 p-0.5 text-black rounded-full" }}
                                inputSize={"sm"}
                                className={"border-neutral-200 font-semibold !text-black min-w-36 max-w-36 flex-row-reverse "}
                                onValueChange={(value) => {
                                    const selectedPeriodFilter = Object.values(periodFilterOptions).find((option) => option.option === value);
                                    setPeriodFilter((prevState) => selectedPeriodFilter ?? prevState);
                                    dispatch({
                                        dispatchType: "setDashboardSetting",
                                        change: { dashboardSettings: { analytics: { activePeriodFilter: selectedPeriodFilter } } }
                                    });
                                    updateChartData({ period: value });
                                }}
                                placeholder={periodFilter.label}
                            >
                                {Object.values(periodFilterOptions).map((filterOption) =>
                                    filterOption.isUpsell ? (
                                        <DisabledSelectOption
                                            className={"bg-secondary-light hover:bg-upsell focus:bg-upsell"}
                                            onClick={() => {
                                                window.open(metricoolSSOLink);
                                                window.focus();
                                            }}
                                        >
                                    <span className="flex size-3.5 items-center justify-center">
                                        <Icon icon={"upsell"} className={"bg-upsell rounded-full text-black size-2.5 p-0.5"}/>
                                    </span>
                                            {filterOption.label}
                                        </DisabledSelectOption>
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
                            <Button
                                variant={"upsell"}
                                size={"sm"}
                                icon={"file"}
                                iconPosition={"left"}
                                link={`https://app.metricool.com/evolution/reports?blogId=${metricool.blogId}&userId=${metricool.userId}`}
                            >
                                {__("Report", "metricool")}
                            </Button>
                        </>
                    )}
                </FlexContainer>
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

export default AnalyticsTab;