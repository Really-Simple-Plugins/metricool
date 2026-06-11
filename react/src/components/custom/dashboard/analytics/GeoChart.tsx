import {Chart} from "react-google-charts";

type GeoChartProps = {
    chartData: string[][],
    chartOptions: Record<string, unknown>
};

const GeoChart = ({ chartData, chartOptions }: GeoChartProps) => {

    /**
     * Google Chart requires the data array to always have headers,
     * else it throws an error. It also requires the headers array
     * to always have a length of 2 if there is no further data, or
     * it throws a different error. Therefor we use this custom
     * array if the backend returns an empty chartData array.
     */
    let fallbackChartData = [["", ""]];

    /**
     * Default chart version for Google Chart is 51, but if we do not set it
     * explicitly, it will throw a warning in the console. Dont really know why.
     */
    let requiredChartVersion = "51";

    return (
        <Chart
            data={chartData.length > 0 ? chartData : fallbackChartData}
            chartType={"GeoChart"}
            options={chartOptions}
            height={"185px"}
            width={"100%"}
            chartVersion={requiredChartVersion}
        />
    );
};

export { GeoChart };