import FlexContainer from "./FlexContainer.tsx";
import { Chart } from "react-google-charts";
import { useQuery } from "@tanstack/react-query";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { Button } from "../components";
import { __ } from "@wordpress/i18n";

const CountriesTab = () => {
    const { metricool, httpClient } = useGlobalContext();
    const localizeCountryNames = new Intl.DisplayNames(metricool.locale, { type: "region" });
    const { data: countryData, isLoading, error } = useQuery({
        queryKey: ["analytics", "countries"],
        queryFn: () => httpClient?.setRoute("statistics/countries").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data) => {
            const countriesArray = Object.entries(data.data)
                .map(([countryCode, visitorAmount]) => [countryCode, localizeCountryNames.of(countryCode), visitorAmount]);
            return [["value", "Country", __("Visitors", "metricool")], ...countriesArray]
        }
    });

    const geochartOptions = {
        datalessRegionColor: "white",
        backgroundColor: "#A6CEE3",
        colorAxis: { colors: ["white",  "#E6A735",  "#E6A735",  "#E6A735",  "#E6A735",  "#E6A735",  "#E6A735",  "#E6A735",  "#E6A735" ] },
        defaultColor: "white",
        legend: "none",
    };

    return (
        <FlexContainer direction={"column"} className={"min-h-[290px] justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {countryData && (
                <FlexContainer direction={"column"} className={"rounded-md bg-gray-50"}>
                    <FlexContainer direction={"column"} className={"rounded-md overflow-hidden"}>
                        <Chart
                            data={countryData}
                            chartType="GeoChart"
                            options={geochartOptions}
                            height={"200px"}
                            width={"100%"}
                        />
                    </FlexContainer>
                </FlexContainer>
            )}
            {error && (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching the data.", "metricool")}
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"w-full justify-end items-center"}>
                <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"}>
                    {__("View Analytics", "metricool")}
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export default CountriesTab;