import { __ } from "@wordpress/i18n";
import { Button, Dialog, DialogHeader, DialogTitle, FlexContainer } from "../components";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import OnboardingHeader from "../custom/OnboardingHeader.tsx";
import { useState } from "react";
import { useMutation } from "@tanstack/react-query";
import SignInForm from "../custom/SignInForm.tsx";
import OnboardingForm from "../custom/OnboardingForm.tsx";
import DOMPurify from "dompurify";
import { VerifyEmailStep, LoadingStep, ConnectBrandStep } from "../custom/OnboardingSteps.tsx";

export const OnboardingLayout = () => {
    const { metricool, dispatch } = useGlobalContext();
    const [signInModalOpen, setSignInModalOpen] = useState(false);
    const [onboardingModalOpen, setOnboardingModalOpen] = useState(false);
    const [enteredEmail, setEnteredEmail] = useState("");
    const [activeStep, setActiveStep] = useState(0);
    const onboardingSteps = [
        (<VerifyEmailStep enteredEmail={enteredEmail} />),
        (<LoadingStep/>),
        (<ConnectBrandStep/>),
    ];

    const { mutate: onSubmit } = useMutation({
        mutationFn: async (formValues: { email: string; password: string; terms: boolean; marketing: boolean; }) => {
            setEnteredEmail(formValues.email)
            setOnboardingModalOpen(true);
            // const response = await httpClient?.setRoute("").setPayload({
            // }).post();
            const timer = new Promise(resolve => setTimeout(resolve, 8000));
            await timer;

            return formValues;
        },
        onSuccess: async (data) => {
            console.log(data);
            setActiveStep(1);
            const timer = new Promise(resolve => setTimeout(resolve, 8000));
            await timer;
            setActiveStep(2);
        },
        onError: (data) => {
            console.log(data);
        }
    });

    DOMPurify.addHook("afterSanitizeAttributes", (node) => {
        if (node.hasAttribute("href") && node.getAttribute("href") !== "https://metricool.com/legal-terms/") {
            node.remove();
        }
    });

    return (
        <FlexContainer direction={"column"} className={"w-full h-full px-20 py-12 !gap-0"}>
            <OnboardingHeader
                logo={{ src: `${metricool.assets_url}img/mc-logo.svg`, alt: "Metricool Logo" }}
                actions={[
                    (__("Already a Metricooler?", "metricool")),
                    (
                        <Button variant={"primary-gradient-ghost"} className={"p-0 after:!bg-white after:!border-none !border-none"} onClick={() => setSignInModalOpen(true)}>
                            {__("Sign in here", "metricool")}
                        </Button>
                    )
                ]}
            >
                <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[30px]"} alt={"Metricool logo"}/>
                {__("The digital Swiss Army Knife for social media marketers", "metricool")}
            </OnboardingHeader>
            <div className={"w-full h-[2px] bg-[image:var(--gradient-brand-secondary)]"}></div>
            <FlexContainer direction={"row"} className={"w-full !gap-0"}>
                <OnboardingForm onSubmit={(values)=> onSubmit(values)} />
                <img src={`${metricool.assets_url}img/mc-onboarding-image.webp`} className={"max-w-[55%] h-fit"} alt={"Metricool logo"}/>
            </FlexContainer>
            <Dialog
                id={"sign-in-modal"}
                open={signInModalOpen}
                onOpenChange={setSignInModalOpen}
                showCloseButton={true}
                className={"flex flex-col justify-center items-center"}
            >
                <DialogHeader className={"!gap-0 mt-8"}>
                    <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[37px]"} alt={"Metricool logo"}/>
                    <DialogTitle className={"font-bold font-nunito m-0"}>
                        {__("Sign in with your credentials", "metricool")}
                    </DialogTitle>
                </DialogHeader>
                <SignInForm onSubmit={(values) => {
                    console.log(values);
                    dispatch({dispatchType: "setOnboardingComplete"});
                } }/>
            </Dialog>
            <Dialog
                id={"onboarding-modal"}
                open={onboardingModalOpen}
                showCloseButton={false}
                className={"flex flex-col justify-center items-center h-[500px]"}
            >
                {onboardingSteps[activeStep]}
            </Dialog>
        </FlexContainer>
    );
};