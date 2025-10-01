import FlexContainer from "./FlexContainer.tsx";
import { Button } from "../components";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";
import { useEffect } from "react";


const TrafficTab = () => {
    const { httpClient } = useGlobalContext();
    const { data: trafficData, isLoading, error } = useQuery({
        queryKey: ["analytics", "traffic"],
        queryFn: () => httpClient?.setRoute("statistics/referers").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
    });

    useEffect(() => {
        console.log("analytics");
        console.log(trafficData, isLoading, error);
    }, [error, isLoading, trafficData]);

    return (
        <FlexContainer direction={"column"} className={"min-h-[290px] justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {trafficData && (
                <FlexContainer direction={"column"} className={"rounded-md bg-gray-50"}>
                    <FlexContainer direction={"column"} className={"rounded-md"}>
                        {Object.entries(trafficData.data).map(([source, amount]) => (<div><span>{source}</span> : <span>{amount}</span></div>))}
                    </FlexContainer>
                </FlexContainer>
            )}
            {error && (
                <FlexContainer direction={"row"} className={"justify-center items-center"}>
                    {__("There was an error fetching the data.", "metricool")}
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"justify-between items-center"}>
                <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"}>
                    {__("View Analytics", "metricool")}
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export default TrafficTab;