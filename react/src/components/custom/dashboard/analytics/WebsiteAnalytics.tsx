import { useState } from "react";
import { Block, BlockHeader, TabNavigation } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { CountriesTab } from "@/components/custom/dashboard/analytics/tabs/CountriesTab.tsx";
import { RealtimeTab } from "@/components/custom/dashboard/analytics/tabs/RealtimeTab.tsx";
import { AnalyticsTab } from "@/components/custom/dashboard/analytics/tabs/AnalyticsTab.tsx";
import { TrafficTab } from "@/components/custom/dashboard/analytics/tabs/TrafficTab.tsx";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

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
    const { dispatch, dashboardSettings } = useGlobalContext();

    // This state saves the activeTab's index in the tabs array, so it can be
    // easily rendered with {tabs[activeTab].component} below.
    // Initiated as 0 for the RealtimeTab.
    const [activeTab, setActiveTab] = useState(dashboardSettings.activeWebsiteAnalyticsTab ?? 0);
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

    const onTabChange = (tabIndex: number) => {
        setActiveTab(tabIndex);
        dispatch({
            dispatchType: "setDashboardSetting",
            change: { dashboardSettings: { activeWebsiteAnalyticsTab: tabIndex } }
        });
    };

    return (
        <Block className={"min-h-125 max-h-125 xl:max-w-[calc(50%-(--spacing(2)))]"}>
            <BlockHeader
                title={__("Website Analytics", "metricool")}
                action={(
                    <TabNavigation activeTab={activeTab} onTabClick={onTabChange} tabs={tabs}/>
                )}
            />
            {tabs[activeTab].component}
        </Block>
    );
};

export { WebsiteAnalytics };