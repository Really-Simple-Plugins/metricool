import { useState } from "react";
import { Block, BlockHeader } from "@/components";
import { __ } from "@wordpress/i18n";
import TabNavigation from "@/custom/general/TabNavigation.tsx";
import CountriesTab from "./tabs/CountriesTab.tsx";
import RealtimeTab from "./tabs/RealtimeTab.tsx";
import AnalyticsTab from "./tabs/AnalyticsTab.tsx";
import TrafficTab from "./tabs/TrafficTab.tsx";

/**
 * The WebsiteAnalytics block used in {@link DashboardLayout}.
 *
 * Renders one of these 4 tabs: {@link RealtimeTab}, {@link AnalyticsTab},
 * {@link TrafficTab} or {@link CountriesTab}, each displaying different
 * analytics data from Metricool.
 *
 * Default tab is {@link AnalyticsTab}.
 *
 * Contains the logic (state and array) for switching between these tabs,
 * rendering the {@link TabNavigation} through the `action` prop of the
 * {@link BlockHeader}.
 *
 * Displays everything in a {@link Block} with a fixed height (500px)
 */
const WebsiteAnalytics = () => {
    // This state saves the activeTab's index in the tabs array, so it can be
    // easily rendered with {tabs[activeTab].component} below.
    // Initiated as 1 for the AnalyticsTab.
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