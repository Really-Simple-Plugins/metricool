import Header from "../custom/Header.tsx";
import { Button, Dialog, DialogHeader, DialogTitle, FlexContainer } from "../components";
import Progress from "../custom/Progress.tsx";
import WebsiteAnalytics from "../custom/WebsiteAnalytics.tsx";
import ConnectedAccounts from "../custom/ConnectedAccounts.tsx";
import RelatedPlugins from "../custom/RelatedPlugins.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";

export const DashboardLayout = () => {
    const { metricool, dispatch } = useGlobalContext();
    return (
        <FlexContainer direction={"column"} className={"h-full w-full min-[125rem]:items-center"}>
            <Header/>
            <FlexContainer direction={"column"} className={"px-4 w-full max-w-[125rem]"}>
                <FlexContainer direction={"column"} className={"w-full h-full justify-around xl:flex-row"}>
                    <Progress/>
                    <WebsiteAnalytics/>
                </FlexContainer>
                <FlexContainer direction={"column"} className={"w-full justify-around sm:flex-row"}>
                    <ConnectedAccounts/>
                    <RelatedPlugins/>
                </FlexContainer>
            </FlexContainer>
            <Dialog
                id={"onboarding-completed-modal"}
                open={!metricool.was_dashboard_modal_closed}
                showCloseButton={true}
                onOpenChange={() => dispatch({ dispatchType: "setDashboardModalClosed" })}
                className={"flex flex-col justify-center items-center"}
            >
                <FlexContainer direction={"column"} className={"justify-center items-center !gap-6"}>
                    <img src={`${metricool.assets_url}img/onboarding-completed.svg`} alt="Checkmark icon"/>
                    <FlexContainer direction={"column"} className={"!gap-2"}>
                        <DialogHeader className={"justify-center items-center"}>
                            <DialogTitle className={"font-bold font-nunito m-0 text-2xl"}>
                                {__("You're all set!", "metricool")}
                            </DialogTitle>
                        </DialogHeader>
                        <div className={"text-base text-center"}>
                            {__("Welcome to the Metricool Wordpress plugin", "metricool")}
                        </div>
                    </FlexContainer>
                    <Button
                        variant={"black"}
                        onClick={() => dispatch({ dispatchType: "setDashboardModalClosed" })}
                        icon={"arrow-right"}
                        iconPosition={"right"}>
                        {__("Let's go to your dashboard!", "metricool")}
                    </Button>
                </FlexContainer>
            </Dialog>
        </FlexContainer>
    );
};