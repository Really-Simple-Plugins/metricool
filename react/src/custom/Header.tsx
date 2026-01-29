import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { Button } from "@/components";
import HeaderTab from "./HeaderTab.tsx";

const Header = () => {
    const { metricool, metricoolDynamicUrl } = useGlobalContext();
    return (
        <div className={"bg-white min-w-full"}>
            <div className={"max-w-[125rem] mx-auto px-4 flex justify-between items-center flex-wrap max-[700px]:gap-3 gap-8"}>
                <div className={"flex min-w-[4.375rem] min-h-[4.375rem] items-center justify-center"}>
                    <img src={`${metricool.assets_url}img/mc-logo.svg`} alt={__("Metricool logo", "metricool")}/>
                </div>
                <div className={"flex order-3 sm:order-2 w-full sm:w-fit flex-grow justify-center sm:justify-start gap-4"}>
                    <HeaderTab link={"/"}>
                        {__("Dashboard", "metricool")}
                    </HeaderTab>
                    <HeaderTab link={"/settings"}>
                        {__("Settings", "metricool")}
                    </HeaderTab>
                    <HeaderTab link={metricoolDynamicUrl.withPath("planner/calendar")} external={true}>
                        {__("Planner", "metricool")}
                    </HeaderTab>
                </div>
                <div className={"flex order-2 sm:order-3 gap-4"}>
                    <Button
                        variant={"black"}
                        icon={"faq"}
                        iconPosition={"left"}
                        iconClass={"text-white"}
                        link={metricool.metricool_help_url}
                    >
                        {__("Help Center", "metricool")}
                    </Button>
                    <Button
                        variant={"primary-gradient"}
                        icon={"sparkle"}
                        iconPosition={"left"}
                        iconClass={"text-secondary"}
                        link={metricoolDynamicUrl.withPath("user-settings/plan")}
                    >
                        {__("Upgrade to Premium", "metricool")}
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default Header;