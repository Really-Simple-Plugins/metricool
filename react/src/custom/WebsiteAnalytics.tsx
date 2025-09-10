import { useState } from "react";
import { Button, Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import TabNavigation from "./TabNavigation.tsx";
import FlexContainer from "./FlexContainer.tsx";

const WebsiteAnalytics = () => {
    const [activeTab, setActiveTab] = useState(1);
    const tabs = [__("Real-time", "metricool"), __("Analytics", "metricool"), __("Traffic", "metricool"), __("Countries", "metricool")];
    return (
        <Card className={"justify-between"}>
            <CardHeader className={"flex justify-between"}>
                <CardHeaderTitle>{__("Website Analytics", "metricool")}</CardHeaderTitle>
                <TabNavigation activeTab={activeTab} onTabClick={setActiveTab} tabs={tabs}></TabNavigation>
            </CardHeader>
            <FlexContainer direction={"column"} className={"h-[290px] rounded-md bg-gray-50"}>
                <FlexContainer direction={"row"}>
                    <div className={"text-base font-semibold p-2"}>Website</div>
                </FlexContainer>
                <FlexContainer direction={"row"} className={"items-center justify-center h-[100px]"}>
                    CHART
                </FlexContainer>
            </FlexContainer>
            <FlexContainer direction={"row"} className={"justify-between items-center"}>
                <FlexContainer direction={"row"} className={"sm:flex-col xl:flex-row"}>
                    <Button variant={"upsell"} icon={"down"} iconPosition={"left"}>
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
        </Card>
    );
};

export default WebsiteAnalytics;