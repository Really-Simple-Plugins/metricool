import { useState } from "react";
import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import TabNavigation from "./TabNavigation.tsx";
import CountriesTab from "./CountriesTab.tsx";
import RealtimeTab from "./RealtimeTab.tsx";
import AnalyticsTab from "./AnalyticsTab.tsx";
import TrafficTab from "./TrafficTab.tsx";

const WebsiteAnalytics = () => {
    const [activeTab, setActiveTab] = useState(1);
    const tabs = [{
        title: __("Real-time", "metricool"),
        component: <RealtimeTab />
    }, {
        title: __("Analytics", "metricool"),
        component: <AnalyticsTab />
    }, {
        title: __("Traffic", "metricool"),
        component: <TrafficTab />
    }, {
        title: __("Countries", "metricool"),
        component: <CountriesTab />
    }];

    return (
        <Card>
            <CardHeader className={"flex justify-between"}>
                <CardHeaderTitle>{__("Website Analytics", "metricool")}</CardHeaderTitle>
                <TabNavigation activeTab={activeTab} onTabClick={setActiveTab} tabs={tabs}></TabNavigation>
            </CardHeader>
            {tabs[activeTab].component}
        </Card>
    );
};

export default WebsiteAnalytics;