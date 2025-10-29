import { useState } from "react";
import { Block, BlockHeader, BlockHeaderTitle } from "../components";
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
        <Block className={"xl:max-w-[calc(50%-(--spacing(2)))]"}>
            <BlockHeader className={"flex justify-between"}>
                <BlockHeaderTitle>{__("Website Analytics", "metricool")}</BlockHeaderTitle>
                <TabNavigation activeTab={activeTab} onTabClick={setActiveTab} tabs={tabs}></TabNavigation>
            </BlockHeader>
            {tabs[activeTab].component}
        </Block>
    );
};

export default WebsiteAnalytics;