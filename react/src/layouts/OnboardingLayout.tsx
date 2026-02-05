import { __ } from "@wordpress/i18n";
import { Button, Dialog, DialogHeader, DialogTitle, FlexContainer } from "@/components/shared";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import {
    ConnectBrandStep,
    LoadingStep,
    OnboardingForm,
    OnboardingHeader,
    SignInForm,
    VerifyEmailStep
} from "@/components/custom";
import { useEffect, useState } from "react";
import { useMutation } from "@tanstack/react-query";
import OnboardingSchema from "@/components/custom/onboarding/OnboardingSchema.ts";
import { z } from "zod";
import { HeadContent } from "@tanstack/react-router";

/**
 * The Onboarding Layout.
 *
 * Used in {@link Index}, conditionally rendered based on the user's
 * subscriptions data.
 *
 * Contains a {@link OnboardingHeader}
 *
 * Contains a {@link useMutation} to make the sign-up request.
 *
 * Contains a {@link Dialog} to show the onboarding flow.
 *
 * Contains a {@link Dialog} to show the {@link SignInForm}
 *
 */
export const OnboardingLayout = () => {
    const { metricool, dispatch } = useGlobalContext();
    const [signInModalOpen, setSignInModalOpen] = useState<boolean>(false);
    const [onboardingModalOpen, setOnboardingModalOpen] = useState<boolean>(false);
    const [enteredEmail, setEnteredEmail] = useState<string>("");
    const [activeStep, setActiveStep] = useState<number>(0);
    const onboardingSteps = [
        (<VerifyEmailStep enteredEmail={enteredEmail}/>),
        (<LoadingStep/>),
        (<ConnectBrandStep/>),
    ];

    const { mutate: onSubmit } = useMutation({
        onMutate: (formValues: Omit<z.infer<typeof OnboardingSchema>, "brand">) => {
            setEnteredEmail(formValues.credentials.email);
            setOnboardingModalOpen(true);
        },
        mutationFn: async (formValues: Omit<z.infer<typeof OnboardingSchema>, "brand">) => {
            // @ts-expect-error grecaptcha globally defined though script
            await grecaptcha.enterprise.ready(async () => {
                // @ts-expect-error grecaptcha globally defined though script
                const token = await grecaptcha.enterprise.execute('6LflMV4sAAAAAMyPohHfMRVjZQBcu-YuZz_3nTTK', {action: 'signup'});
                console.log("Post this token to the server: ");
                console.log(token);
                // const response = await httpClient.setRoute("create_account").setPayload({
                //   email: formValues.credentials.email,
                //   password: formValues.credentials.password,
                //   newsletters: formValues.terms,
                //   captcha: token,
                // }).post();
            });

            const timer = new Promise(resolve => setTimeout(resolve, 8000));
            await timer;

            return formValues;
        },
        onSuccess: async (response) => {
            console.log(response);
            setActiveStep(1);
            const timer = new Promise(resolve => setTimeout(resolve, 8000));
            await timer;
            setActiveStep(2);
        },
        onError: (error) => {
            console.error(error);
        }
    });

    useEffect(()=> {
        return () => {
            const leftoverRecaptchaScript = document.querySelector("script[src*='recaptcha']");
            if (leftoverRecaptchaScript){
                leftoverRecaptchaScript.remove();
            }
            // @ts-expect-error grecaptcha globally defined by script
            delete window.grecaptcha;
        }
    }, []);

    return (
        <FlexContainer direction={"column"} className={"w-full h-full px-20 py-12 !gap-0"}>
            {/* HeadContent adds the scripts defined in head in __root.tsx to the document's <head>. Only for recaptcha script, so only implemented here. */}
            <HeadContent/>
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
                <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[30px]"} alt={__("Metricool logo", "metricool")}/>
                {__("The digital Swiss Army Knife for social media marketers", "metricool")}
            </OnboardingHeader>
            <div className={"w-full h-[2px] bg-[image:var(--gradient-brand-secondary)]"}></div>
            <FlexContainer direction={"row"} className={"w-full !gap-0"}>
                <FlexContainer direction={"column"} className={"min-w-[45%] max-w-[45%]"}>
                    <h1 className={"font-bold font-nunito text-[1.75rem] leading-8"}>{__("Join more than 2 million professionals, agencies and brands that use Metricool as their one-stop shop for social media and online ad management.", "metricool")}</h1>
                    <OnboardingForm onSubmit={(values) => onSubmit(values)}/>
                </FlexContainer>
                <img src={`${metricool.assets_url}img/mc-onboarding-image.webp`} className={"max-w-[55%] h-fit"} alt={__("Laptop and phone displaying the Metricool app", "metricool")}/>
            </FlexContainer>
            <Dialog
                id={"sign-in-modal"}
                open={signInModalOpen}
                onOpenChange={setSignInModalOpen}
                showCloseButton={true}
                className={"flex flex-col gap-6 justify-center items-center"}
            >
                <DialogHeader className={"!gap-0 mt-8 justify-center items-center"}>
                    <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[37px] w-auto"} alt={__("Metricool logo", "metricool")}/>
                    <DialogTitle className={"font-bold font-nunito m-0 text-2xl leading-6"}>
                        {__("Sign in with your credentials", "metricool")}
                    </DialogTitle>
                </DialogHeader>
                <SignInForm onSubmit={(values) => {
                    console.log(values);
                    dispatch({ dispatchType: "setOnboardingComplete" });
                }}/>
            </Dialog>
            <Dialog
                id={"onboarding-modal"}
                open={onboardingModalOpen}
                showCloseButton={false}
                className={"flex flex-col justify-center items-center"}
            >
                {onboardingSteps[activeStep]}
            </Dialog>
        </FlexContainer>
    );
};