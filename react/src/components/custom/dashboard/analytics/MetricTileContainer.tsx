import { FlexContainer } from "@/components/shared";
import { MetricTile, type MetricTileProps } from "@/components/custom/dashboard/analytics/MetricTile";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

type MetricTileContainerProps = {
    showFullAnalytics?: boolean,
    metrics: Record<"pageViews" | "comments" | "posts" | "visits" | "visitors", MetricTileProps & { label: string, totalAmount: number }>
};



const MetricTileContainer = ({ showFullAnalytics, metrics }: MetricTileContainerProps) => {
    const { metricool } = useGlobalContext();

    const numberFormatter = Intl.NumberFormat(metricool.locale, {
        notation: "compact",
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    });

    return (
        <FlexContainer direction={"row"} className={"w-full min-h-12.5 justify-end gap-2!"}>
            <MetricTile
                onClick={metrics.pageViews.onClick}
                metric={numberFormatter.format(metrics.pageViews.totalAmount)}
                trend={metrics.pageViews.trend}
                // @ts-expect-error tsc can't verify color is a valid variant
                variant={metrics.pageViews.color}
                inactive={metrics.pageViews.hidden}
                disabled={metrics.pageViews.totalAmount === 0}
            >
                {metrics.pageViews.label}
            </MetricTile>
            {showFullAnalytics && (
                <MetricTile
                    onClick={metrics.visits.onClick}
                    metric={numberFormatter.format(metrics.visits.totalAmount)}
                    trend={metrics.visits.trend}
                    // @ts-expect-error tsc can't verify color is a valid variant
                    variant={metrics.visits.color}
                    inactive={metrics.visits.hidden}
                    disabled={metrics.visits.totalAmount === 0}
                >
                    {metrics.visits.label}
                </MetricTile>
            )}
            <MetricTile
                onClick={metrics.visitors.onClick}
                metric={numberFormatter.format(metrics.visitors.totalAmount)}
                trend={metrics.visitors.trend}
                // @ts-expect-error tsc can't verify color is a valid variant
                variant={metrics.visitors.color}
                inactive={metrics.visitors.hidden}
                disabled={metrics.visitors.totalAmount === 0}
            >
                {metrics.visitors.label}
            </MetricTile>
            {showFullAnalytics && (
                <>
                    <MetricTile
                        onClick={metrics.posts.onClick}
                        metric={numberFormatter.format(metrics.posts.totalAmount)}
                        trend={metrics.posts.trend}
                        // @ts-expect-error tsc can't verify color is a valid variant
                        variant={metrics.posts.color}
                        inactive={metrics.posts.hidden}
                        disabled={metrics.posts.totalAmount === 0}
                    >
                        {metrics.posts.label}
                    </MetricTile>
                    <MetricTile
                        onClick={metrics.comments.onClick}
                        metric={numberFormatter.format(metrics.comments.totalAmount)}
                        trend={metrics.comments.trend}
                        // @ts-expect-error tsc can't verify color is a valid variant
                        variant={metrics.comments.color}
                        inactive={metrics.comments.hidden}
                        disabled={metrics.comments.totalAmount === 0}
                    >
                        {metrics.comments.label}
                    </MetricTile>
                </>
            )}
        </FlexContainer>
    );
};

export { MetricTileContainer };