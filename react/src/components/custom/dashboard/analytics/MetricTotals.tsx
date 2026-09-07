import { MetricTile } from "@/components/custom/dashboard/analytics/MetricTile";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import type { MetricData } from "@/hooks/analytics/useWebAnalyticsData.tsx";
import type { AnalyticsChartConfig } from "@/components/custom/dashboard/analytics/tabs/AnalyticsTab.tsx";

type MetricTileContainerProps = {
    toggleMetric: (metricKey: string) => void,
    metrics: Record<"pageViews" | "comments" | "posts" | "visits" | "visitors", MetricData>,
    chartConfig: AnalyticsChartConfig,
};

const MetricTotals = ({ metrics, toggleMetric, chartConfig }: MetricTileContainerProps) => {
    const { metricool } = useGlobalContext();

    /**
     * Function to localize and format numbers.
     * Used to ensure numbers are never displayed with more than 2 decimals but aren't
     * forced to display decimals if there aren't any either.
     *
     * `notation: "compact"` is used to shorten larger numbers, e.g. `1,000,000`
     * becomes `1M`
     *
     * @see {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat/NumberFormat#notation}
     */
    const localizedMetricNumberFormatter = Intl.NumberFormat(metricool.locale, {
        notation: "compact",
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    });

    return (
        <>
            {Object.entries(metrics).map(([metricKey, metricData]) => (
                <MetricTile
                    key={metricData.label}
                    onClick={() => toggleMetric(metricKey)}
                    metric={localizedMetricNumberFormatter.format(metricData.totalAmount)}
                    trend={metricData.trend}
                    variant={chartConfig[metricKey].color}
                    inactive={chartConfig[metricKey].hidden}
                    disabled={metricData.totalAmount === 0}
                >
                    {metricData.label}
                </MetricTile>
            ))}
        </>
    );
};

export { MetricTotals };