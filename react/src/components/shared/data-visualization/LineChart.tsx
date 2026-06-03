import {
    Line as RechartLine,
    LineChart as RechartLineChart,
    type LineProps as RechartLineProps,
    XAxis as RechartXAxis,
    type XAxisProps as RechartXAxisProps,
    YAxis as RechartYAxis,
    type YAxisProps as RechartYAxisProps,
} from "recharts";
import {
    type ChartConfig as PrimitiveChartConfig,
    ChartContainer as PrimitiveChartContainer,
    ChartTooltip as PrimitiveChartTooltip,
    ChartTooltipContent as PrimitiveChartTooltipContent,
} from "@/components/shared/primitives/chart";
import { cn } from "@/support/functions/utils";

type ChartConfig = PrimitiveChartConfig & {
    [k in string]: {
        hidden?: boolean,
    }
}

type LineChartProps = {
    chartConfig: ChartConfig,
    chartData: object[],
    chartSettings: {
        xAxisKey: string,
        general?: {
            height?: 290 | number,
            displayTooltip?: boolean,
            hideTooltipLabel?: boolean,
            margin?: {
                [k in "left" | "right" | "top" | "bottom"]: number
            }
        }
        xAxis?: RechartXAxisProps,
        yAxis?: RechartYAxisProps,
    },
    className?: string,
    linesSettings?: RechartLineProps,
}

/**
 *
 * @version 1.0.0
 */
const LineChart = ({ chartConfig, chartData, chartSettings, linesSettings, className }: LineChartProps) => {
    return (
        <PrimitiveChartContainer config={chartConfig} className={cn(className, chartSettings?.general?.height === 290 && "max-h-72.5")} {...(chartSettings?.general?.height && { height: chartSettings.general.height })} >
            <RechartLineChart
                accessibilityLayer
                data={chartData}
                margin={{
                    top: 12,
                    right: 12,
                }}
            >
                <RechartXAxis
                    padding={{ left: 12, right: 6 }}
                    dataKey={chartSettings.xAxisKey}
                    tickLine={false}
                    axisLine={{ stroke: "#DCDCDE" }}
                    interval={chartSettings.xAxis?.interval ?? 4}
                />
                <RechartYAxis
                    padding={{ bottom: 12 }}
                    width={40}
                    axisLine={false}
                    tickLine={false}
                    allowDecimals={false}
                    domain={["dataMin", "dataMax"]}
                />
                <PrimitiveChartTooltip
                    cursor={false}
                    content={
                        <PrimitiveChartTooltipContent hideLabel={chartSettings.general?.hideTooltipLabel ?? false}/>
                    }
                />
                {Object.entries(chartConfig).map(([key, data]) => (
                    <RechartLine
                        isAnimationActive={false}
                        dataKey={key}
                        type={linesSettings?.type ?? "monotone"}
                        stroke={data.color?.startsWith("#") ? data.color : `var(--color-${data.color})`}
                        strokeWidth={linesSettings?.strokeWidth ?? 1}
                        hide={data.hidden}
                        dot={false}
                    />
                ))}
            </RechartLineChart>
        </PrimitiveChartContainer>
    );
};

export { LineChart, type ChartConfig };