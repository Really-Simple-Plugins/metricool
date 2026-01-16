import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { Button } from "../components";
import HeaderTab from "./HeaderTab.tsx";

const Header = () => {
    const { metricool } = useGlobalContext();
    return (
        <div className={"bg-white min-w-full"}>
            <div className={"max-w-[125rem] mx-auto px-4 flex justify-between items-center flex-wrap max-[700px]:gap-3 gap-8"}>
                <div className={"flex min-w-[4.375rem] min-h-[4.375rem] items-center justify-center"}>
                    <img src={`${metricool.assets_url}img/mc-logo.svg`} alt={"Metricool logo"}/>
                </div>
                <div className={"flex order-3 sm:order-2 w-full sm:w-fit flex-grow justify-center sm:justify-start gap-4"}>
                    <HeaderTab link={"/"}>
                        {__("Dashboard", "metricool")}
                    </HeaderTab>
                    <HeaderTab link={"/settings"}>
                        {__("Settings", "metricool")}
                    </HeaderTab>
                    <HeaderTab link={`https://app.metricool.com/planner/calendar?blogId=${metricool.blogId}&userId=${metricool.userId}`} external={true}>
                        {__("Planner", "metricool")}
                    </HeaderTab>
                </div>
                <div className={"flex order-2 sm:order-3 gap-4"}>
                    <Button
                        variant={"black"}
                        icon={"faq"}
                        iconPosition={"left"}
                        iconClass={"text-white"}
                        link={"https://help.metricool.com"}
                    >
                        {__("Help Center", "metricool")}
                    </Button>
                    <Button
                        variant={"primary-gradient"}
                        icon={"sparkle"}
                        iconPosition={"left"}
                        iconClass={"text-secondary"}
                        link={`https://app.metricool.com/user-settings/plan?blogId=${metricool.blogId}&userId=${metricool.userId}`}
                    >
                        {__("Upgrade to Premium", "metricool")}
                    </Button>
                </div>
            </div>
        </div>
    );
};

export default Header;