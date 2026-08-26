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

    const numberFormatter = Intl.NumberFormat(metricool.locale, {
        notation: "compact",
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    });

    return (
        <>
            {Object.entries(metrics).map(([metricKey, metricData]) => (
                <MetricTile
                    onClick={() => toggleMetric(metricKey)}
                    metric={numberFormatter.format(metricData.totalAmount)}
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