import {
    Button,
    type ChartConfig,
    DisabledSelectOption,
    FlexContainer,
    Icon,
    LineChart,
    LoadingAndErrorState,
    Select,
    SelectOption,
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useState } from "react";
import { MetricTile } from "@/components/custom/dashboard/analytics/MetricTile.tsx";
import { cn } from "@/support/functions/utils";
import { useAnalyticsData } from "@/hooks/useAnalyticsData.tsx";

export type PeriodFilterOption = {
    label: string,
    option: string,
    isUpsell: boolean,
    xAxisInterval: number,
}

const getCurrentPeriodFilter = (defaultPeriodFilter: PeriodFilterOption, activePeriodFilterInContext: PeriodFilterOption | undefined): PeriodFilterOption => {
    if (activePeriodFilterInContext) {
        return activePeriodFilterInContext;
    }

    return defaultPeriodFilter;
};

const AnalyticsTab = () => {
    const { metricool, dispatch, dashboardSettings, metricoolDynamicUrl } = useGlobalContext();

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
            isUpsell: !metricool.account?.is_premium,
            xAxisInterval: 29,
        },
        last12Months: {
            label: __("Last 12 months", "metricool"),
            option: "last12months",
            isUpsell: !metricool.account?.is_premium,
            xAxisInterval: 29,
        },
    };

    const numberFormatter = Intl.NumberFormat(metricool.locale, {
        notation: "compact",
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    });
    const defaultPeriodFilter = periodFilterOptions.last30Days;
    const [periodFilter, setPeriodFilter] = useState(getCurrentPeriodFilter(defaultPeriodFilter, dashboardSettings.activePeriodFilter));
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
    const [xAxisInterval, setXAxisInterval] = useState(getCurrentPeriodFilter(defaultPeriodFilter, dashboardSettings.activePeriodFilter).xAxisInterval);

    const adjustXAxisInterval = (timelineDataLength: number) => {
        if (periodFilter === periodFilterOptions.currentMonth) {
            const maxPossibleDataPointsOnXAxis = 14;
            const isCurrentMonthTooLongForXAxis = (timelineDataLength >= maxPossibleDataPointsOnXAxis);
            let appropriateXAxisIntervalForCurrentMonth = periodFilterOptions.currentMonth.xAxisInterval;

            if (isCurrentMonthTooLongForXAxis) {
                appropriateXAxisIntervalForCurrentMonth = periodFilterOptions.previousMonth.xAxisInterval;
            }

            setXAxisInterval(appropriateXAxisIntervalForCurrentMonth);
        } else {
            setXAxisInterval(periodFilter.xAxisInterval);
        }
    };

    const toggleMetric = (dataKey: string) => {
        setChartConfig((prevState) => ({
            ...prevState,
            [dataKey]: { ...prevState[dataKey], hidden: !prevState[dataKey].hidden }
        }));
    };

    const {
        analyticsDataQuery: {
            data: analyticsData,
            isLoading,
            error,
            isSuccess: hasAnalyticsData,
            refetch,
            errorUpdateCount,
            isRefetching,
        },
    } = useAnalyticsData({
        tab: "analytics",
        selectedAnalyticsPeriod: periodFilter.option,
    });

    return (
        <FlexContainer direction={"column"} className={"justify-between grow"}>
            {!hasAnalyticsData ? (
                <LoadingAndErrorState
                    error={error}
                    isLoading={isLoading}
                    errorUpdateCount={errorUpdateCount}
                    refetch={refetch}
                    supportTicketLink={metricool.trusted_urls.new_support_ticket}
                />
            ) : (
                <FlexContainer direction={"column"} className={"relative rounded-md bg-gray-50 !gap-2 p-2"}>
                    <FlexContainer direction={"row"} className={"flex w-full justify-end !gap-2"}>
                        {Object.entries(analyticsData.totals).map(([metricKey, metricData]) => (
                            <MetricTile
                                onClick={() => toggleMetric(metricKey)}
                                metric={numberFormatter.format(metricData.totalAmount)}
                                trend={metricData.trend}
                                // @ts-expect-error tsc can't verify color is a valid variant
                                variant={chartConfig[metricKey].color}
                                inactive={chartConfig[metricKey].hidden}
                                disabled={metricData.totalAmount === 0}
                            >
                                {chartConfig[metricKey].label}
                            </MetricTile>
                        ))}
                    </FlexContainer>
                    <hr className={"-mx-2"}/>
                    {isRefetching && (
                        <div className={"absolute w-full h-full bg-white opacity-45"}>
                            <FlexContainer direction={"row"} className={"justify-center items-center w-full h-full"}>
                                <Icon icon={"loading"} className={"size-5"}/>
                            </FlexContainer>
                        </div>
                    )}
                    <LineChart
                        className={cn(isRefetching && "opacity-45")}
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
                                icon={!metricool.account?.is_premium ? {
                                    icon: "upsell",
                                    className: "bg-upsell size-2.5 p-0.5 text-black rounded-full"
                                } : undefined}
                                inputSize={"sm"}
                                className={"border-neutral-200 font-semibold !text-black min-w-36 max-w-36 flex-row-reverse "}
                                onValueChange={(value) => {
                                    const selectedPeriodFilter = Object.values(periodFilterOptions).find((option) => option.option === value);
                                    setPeriodFilter((prevState) => selectedPeriodFilter ?? prevState);
                                    dispatch({
                                        dispatchType: "setDashboardSetting",
                                        change: { dashboardSettings: { activePeriodFilter: selectedPeriodFilter } }
                                    });
                                    adjustXAxisInterval(analyticsData.timelineData.length);
                                }}
                                placeholder={periodFilter.label}
                            >
                                {Object.values(periodFilterOptions).map((filterOption) =>
                                    filterOption.isUpsell ? (
                                        <DisabledSelectOption
                                            className={"bg-secondary-light hover:bg-upsell focus:bg-upsell"}
                                        >
                                            <Button
                                                variant={"link"}
                                                link={metricoolDynamicUrl.withPath("user-settings/plan")}
                                                className={"!no-underline font-semibold hover:text-black"}
                                            >
                                                <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                                                    <span className={"flex size-3.5 items-center justify-center"}>
                                                        <Icon icon={"upsell"} className={"bg-upsell rounded-full text-black size-2.5 p-0.5"}/>
                                                    </span>
                                                    {filterOption.label}
                                                </FlexContainer>
                                            </Button>
                                        </DisabledSelectOption>
                                    ) : (
                                        <SelectOption
                                            value={filterOption.option}
                                            className={cn("font-semibold hover:bg-primary-light/50 focus:bg-primary-light/50")}
                                        >
                                            {filterOption.label}
                                        </SelectOption>
                                    )
                                )}
                            </Select>
                            <Button
                                variant={metricool.account?.is_premium ? "black-ghost" : "upsell"}
                                className={cn(metricool.account?.is_premium && "border-neutral-200")}
                                size={"sm"}
                                link={metricoolDynamicUrl.withPath("evolution/reports")}
                            >
                                <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                                    <Icon icon={"file"}/>
                                    {__("Report", "metricool")}
                                </FlexContainer>
                            </Button>
                        </>
                    )}
                </FlexContainer>
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

export { AnalyticsTab };