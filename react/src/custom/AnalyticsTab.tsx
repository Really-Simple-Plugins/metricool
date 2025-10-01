import FlexContainer from "./FlexContainer.tsx";
import { Button } from "../components";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";
import { useEffect } from "react";
import MetricTile from "./MetricTile.tsx";

const AnalyticsTab = () => {
    const { httpClient } = useGlobalContext();
    const { data: response, isLoading, error } = useQuery({
        queryKey: ["analytics"],
        queryFn: () => httpClient?.setRoute("statistics/pageViews").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
    });

    //statistics/pageViews
    //statistics/visitors
    //statistics/visits
    //statistics/posts
    //statistics/comments

    useEffect(() => {
        console.log("analytics");
        console.log(response, isLoading, error);
    }, [error, isLoading, response]);

    return (
        <FlexContainer direction={"column"} className={"min-h-[290px] justify-between grow"}>
            {isLoading && (
                <div>LOADING</div>
            )}
            {(<FlexContainer direction={"column"} className={"rounded-md bg-gray-50"}>
                    <FlexContainer direction={"row"} className={"justify-between"}>
                        <div className={"text-md font-semibold p-2"}>{__("Website", "metricool")}</div>
                        <FlexContainer direction={"row"}>
                            <MetricTile metric={"10"} metricTitle={__("Page Views", "metricool")} variant={"tertiary"}/>
                            <MetricTile metric={"10"} metricTitle={__("Visits", "metricool")} variant={"light-green"}/>
                            <MetricTile metric={"10"} metricTitle={__("Visitors", "metricool")} variant={"primary"}/>
                            <MetricTile metric={"10"} metricTitle={__("Posts", "metricool")} variant={"secondary"}/>
                            <MetricTile metric={"10"} metricTitle={__("Comments", "metricool")} variant={"primary-dark"}/>
                        </FlexContainer>
                    </FlexContainer>
                    <hr/>
                    <FlexContainer direction={"row"} className={"rounded-md"}>
                        CHART
                    </FlexContainer>
                </FlexContainer>
            )}
            <FlexContainer direction={"row"} className={"justify-between items-center"}>
                <FlexContainer direction={"row"} className={"sm:flex-col xl:flex-row"}>
                    <Button variant={"upsell"} icon={"unfold"} iconPosition={"left"}>
                        {__("Last 30 Days", "metricool")}
                    </Button>
                    <Button variant={"upsell"} icon={"file"} iconPosition={"left"}>
                        {__("Generate Report", "metricool")}
                    </Button>
                    <Button variant={"upsell"} icon={"download"} iconPosition={"left"}>
                        {__("Download CSV", "metricool")}
                    </Button>
                </FlexContainer>
                <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"}>
                    {__("View Analytics", "metricool")}
                </Button>
            </FlexContainer>
        </FlexContainer>
    );
};

export default AnalyticsTab;