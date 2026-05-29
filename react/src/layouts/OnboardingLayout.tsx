import { __ } from "@wordpress/i18n";
import { Alert, Button, Dialog, FlexContainer, Header } from "@/components/shared";
import { type MetricoolData, useGlobalContext } from "@/context/GlobalContext.tsx";
import { ConnectBrandStep, LoadingStep, OnboardingForm, SignInStep } from "@/components/custom";
import { useEffect, useState } from "react";
import { HeadContent } from "@tanstack/react-router";
import DOMPurify from "dompurify";
import { useAuthenticationData } from "@/hooks/useAuthenticationData.tsx";

/**
 * The Onboarding Layout.
 *
 * Used in {@link Index}, conditionally rendered based on the user's
 * subscriptions data.
 *
 * Contains a {@link Header}
 *
 * Contains a {@link Dialog} to show the onboarding flow.
 *
 * Contains a {@link Dialog} to show the {@link SignInStep}
 *
 */
export const OnboardingLayout = () => {
    const { metricool, dispatch } = useGlobalContext();
    const [signInModalOpen, setSignInModalOpen] = useState<boolean>((metricool.onboarding.mode.forced_login || (metricool.onboarding.state.authenticated && !metricool.onboarding.state.blog_id_selected)));
    const [onboardingModalOpen, setOnboardingModalOpen] = useState<boolean>(false);
    const [activeOnboardingStep, setActiveOnboardingStep] = useState<number>(0);
    const [activeSignInStep, setActiveSignInStep] = useState<number>((!metricool.onboarding.state.authenticated && !metricool.onboarding.state.blog_id_selected) ? 0 : 1);

    const beforeSignUpCallback = () => {
        setActiveOnboardingStep(0);
        setOnboardingModalOpen(true);
    };

    const onSignUpSuccessCallback = (onboarding: MetricoolData["onboarding"], account: Pick<Required<MetricoolData>, "account">["account"]) => {
        dispatch({
            dispatchType: "setAccountData",
            change: { metricool: { account: { ...account } } }
        });
        if (onboarding.state.blog_id_selected === false) {
            setActiveOnboardingStep(1);
        } else {
            dispatch({
                dispatchType: "setOnboardingState",
                change: { metricool: { onboarding: { ...onboarding } } }
            });
        }
    };

    const onSignUpErrorCallback = () => {
        setOnboardingModalOpen(false);
        setActiveOnboardingStep(0);
    };

    const { signUpMutation: { mutate: onSignUp, error: signUpError, } } = useAuthenticationData({
        signUpCallbacks: {
            beforeSignUpCallback,
            onSignUpSuccessCallback,
            onSignUpErrorCallback,
        }
    });

    /**
     * The ConnectBrandStep is here purely as a contingency, just in case the
     * `metricool.onboarding.state` ever returns the wring combination of
     * booleans, but it should never appear to the user during onboarding.
     */
    const onboardingSteps = [
        (
            <LoadingStep/>
        ),
        (
            <ConnectBrandStep/>
        ),
    ];

    const signInSteps = [
        (
            <SignInStep/>
        ),
        (
            <ConnectBrandStep
                setModalOpen={setSignInModalOpen}
                resetSignInSteps={() => setActiveSignInStep(0)}
            />
        ),
    ];

    useEffect(() => {
        return () => {
            const leftoverRecaptchaScript = document.querySelector("script[src*='recaptcha']");
            if (leftoverRecaptchaScript) {
                leftoverRecaptchaScript.remove();
            }
            // @ts-expect-error grecaptcha globally defined by script
            delete window.grecaptcha;
        };
    }, []);

    return (
        <FlexContainer direction={"column"} className={"w-full h-full px-20 py-12 !gap-0 max-w-[125rem] mx-auto"}>
            {/* HeadContent adds the scripts defined in head in __root.tsx to the document's <head>. Only for recaptcha script, so only implemented here. */}
            <HeadContent/>
            <Header
                variant={"transparent"}
                logo={(
                    <FlexContainer direction={"row"} className={"text-base font-bold font-nunito items-center"}>
                        <img src={`${metricool.assets_url}img/mc-logo.svg`} alt={__("Metricool logo icon", "metricool")}/>
                        <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[30px]"} alt={__("Metricool logo", "metricool")}/>
                        {__("The digital Swiss Army Knife for social media marketers", "metricool")}
                    </FlexContainer>
                )}
                actions={[
                    (
                        <div className={"text-md font-[600]"}>
                            {__("Already a Metricooler?", "metricool")}
                        </div>),
                    (
                        <Button
                            variant={"primary-gradient-ghost"}
                            className={"p-0 after:!bg-white after:!border-none !border-none font-[600]"}
                            onClick={() => setSignInModalOpen(true)}
                        >
                            {__("Sign in here", "metricool")}
                        </Button>
                    )
                ]}
            />
            <div className={"w-full h-[2px] bg-[image:var(--gradient-brand-secondary)]"}></div>
            <FlexContainer direction={"row"} className={"w-full !gap-0 justify-between"}>
                <FlexContainer direction={"column"} className={"min-w-[45%] max-w-[45%]"}>
                    <h1 className={"font-bold font-nunito text-[1.75rem] leading-8"}>{__("Join more than 2 million professionals, agencies and brands that use Metricool as their one-stop shop for social media and online ad management.", "metricool")}</h1>
                    {signUpError && (
                        <Alert variant={"error"} className={"sm:max-w-5/6"}>
                            <div
                                dangerouslySetInnerHTML={{
                                    __html: DOMPurify.sanitize(signUpError.message, { ADD_ATTR: ["target"] })
                                }}
                            />
                        </Alert>
                    )}
                    <OnboardingForm onSubmit={(values) => onSignUp(values)}/>
                </FlexContainer>
                <img src={`${metricool.assets_url}img/mc-onboarding-image.webp`} className={"max-w-[55%] h-fit"} alt={__("Laptop and phone displaying the Metricool app", "metricool")}/>
            </FlexContainer>
            <Dialog
                id={"sign-in-modal"}
                open={signInModalOpen}
                onOpenChange={(metricool.onboarding.mode.forced_login) ? undefined : setSignInModalOpen}
                showCloseButton={!(metricool.onboarding.mode.forced_login)}
                className={"flex flex-col gap-6 justify-center items-center"}
            >
                {signInSteps[activeSignInStep]}
            </Dialog>
            <Dialog
                id={"onboarding-modal"}
                open={onboardingModalOpen}
                showCloseButton={false}
                className={"flex flex-col justify-center items-center"}
            >
                {onboardingSteps[activeOnboardingStep]}
            </Dialog>
        </FlexContainer>
    );
};