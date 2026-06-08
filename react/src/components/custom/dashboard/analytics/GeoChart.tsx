import { Chart } from "react-google-charts";

type GeoChartProps = {
    chartData: string[][],
    chartOptions: Record<string, unknown>
};

const GeoChart = ({ chartData, chartOptions }: GeoChartProps) => {

    return (
        <Chart
            /**
             * Google Geochart requires the data array to always have headers,
             * else it throws an error. It also requires the headers array
             * to always have a length of 2 if there is no further data, or
             * it throws a different error. Therefor we use this custom
             * array if the backend returns an empty chartData array.
             */
            data={chartData.length > 0 ? chartData : [["", ""]]}
            chartType={"GeoChart"}
            options={chartOptions}
            height={"185px"}
            width={"100%"}
            // chartVersion necessary to prevent console warning
            chartVersion={"51"}
        />
    );
};

export { GeoChart };