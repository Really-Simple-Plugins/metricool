import { useState } from "react";
import { Block, BlockHeader } from "../components";
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
        <Block className={"min-h-[500px] max-h-[500px] xl:max-w-[calc(50%-(--spacing(2)))]"}>
            <BlockHeader
                title={__("Website Analytics", "metricool")}
                action={(
                    <TabNavigation activeTab={activeTab} onTabClick={setActiveTab} tabs={tabs}/>
                )}
            />
            {tabs[activeTab].component}
        </Block>
    );
};

export default WebsiteAnalytics;