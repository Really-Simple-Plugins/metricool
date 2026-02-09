import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { Button, FlexContainer, Icon } from "@/components/shared";
import { HeaderTab } from "@/components/custom/general/HeaderTab.tsx";

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
                        link={metricool.metricool_help_url}
                    >
                        <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                            <Icon icon={"faq"} className={"text-white"}/>
                            {__("Help Center", "metricool")}
                        </FlexContainer>
                    </Button>
                    <Button
                        variant={"primary-gradient"}
                        link={metricoolDynamicUrl.withPath("user-settings/plan")}
                    >
                        <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                            <Icon icon={"sparkle"} className={"text-secondary"}/>
                            {__("Upgrade to Premium", "metricool")}
                        </FlexContainer>
                    </Button>
                </div>
            </div>
        </div>
    );
};

export { Header };