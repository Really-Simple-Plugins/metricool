import { ConnectedAccounts, OtherPlugins, Progress, WebsiteAnalytics, } from "@/components/custom";
import { Button, Dialog, DialogHeader, DialogTitle, FlexContainer, Icon } from "@/components/shared";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { __ } from "@wordpress/i18n";

/**
 * The Dashboard Layout.
 *
 * Used in {@link Index}, conditionally rendered based on the user's
 * subscriptions data.
 *
 * Contains the {@link Header}, {@link Progress}, {@link WebsiteAnalytics}, {@link ConnectedAccounts}, {@link OtherPlugins} and {@link Dialog} components.
 *
 */
export const DashboardLayout = () => {
    const { metricool, dispatch } = useGlobalContext();
    return (
        <>
            <FlexContainer direction={"column"} className={"px-4 w-full max-w-8xl"}>
                <FlexContainer direction={"column"} className={"w-full h-full justify-around xl:flex-row"}>
                    <Progress/>
                    <WebsiteAnalytics/>
                </FlexContainer>
                <FlexContainer direction={"column"} className={"w-full justify-around sm:flex-row"}>
                    <ConnectedAccounts/>
                    <OtherPlugins/>
                </FlexContainer>
            </FlexContainer>
            <Dialog
                id={"onboarding-completed-modal"}
                open={metricool.onboarding.mode.show_welcome_screen}
                showCloseButton={true}
                onOpenChange={() => dispatch({ dispatchType: "setDashboardModalClosed" })}
                className={"flex flex-col justify-center items-center"}
            >
                <FlexContainer direction={"column"} className={"justify-center items-center !gap-6"}>
                    <img src={`${metricool.assets_url}img/onboarding-completed.svg`} alt={__("Checkmark icon", "metricool")}/>
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
                    >
                        <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                            {__("Let's go to your dashboard!", "metricool")}
                            <Icon icon={"arrow-right"}/>
                        </FlexContainer>
                    </Button>
                </FlexContainer>
            </Dialog>
        </>
    );
};