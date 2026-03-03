import { __ } from "@wordpress/i18n";
import { Button, Dialog, DialogHeader, DialogTitle, FlexContainer } from "@/components/shared";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { ConnectBrandStep, LoadingStep, OnboardingForm, OnboardingHeader, SignInForm, } from "@/components/custom";
import { useEffect, useState } from "react";
import { useMutation } from "@tanstack/react-query";
import OnboardingSchema from "@/components/custom/onboarding/OnboardingSchema.ts";
import { z } from "zod";
import { HeadContent } from "@tanstack/react-router";

const generateRecaptchaToken = async (): Promise<string> => (
    new Promise((resolve) => {
        // @ts-expect-error grecaptcha globally defined through script
        grecaptcha.enterprise.ready(
            () =>
                void (async () => {
                    // @ts-expect-error grecaptcha globally defined through script
                    const token = await grecaptcha.enterprise.execute("6LflMV4sAAAAAMyPohHfMRVjZQBcu-YuZz_3nTTK", { action: "signup" });
                    resolve(token);
                })(),
        );
    })
);

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
    const { metricool, httpClient, dispatch } = useGlobalContext();
    const [signInModalOpen, setSignInModalOpen] = useState<boolean>(false);
    const [onboardingModalOpen, setOnboardingModalOpen] = useState<boolean>(false);
    // const [enteredEmail, setEnteredEmail] = useState<string>("");
    const [activeOnboardingStep, setActiveOnboardingStep] = useState<number>(0);
    const [activeSignInStep, setActiveSignInStep] = useState<number>(0);
    const [connectedBrands, setConnectedBrands] = useState<z.infer<typeof OnboardingSchema.shape.brand>[]>([]);

    const { mutate: onSignUp } = useMutation({
        onMutate: () => {
            // setEnteredEmail(formValues.credentials.email);
            setActiveOnboardingStep(0);
            setOnboardingModalOpen(true);
        },
        mutationFn: async (formValues: Omit<z.infer<typeof OnboardingSchema>, "brand">) => {
            const token = await generateRecaptchaToken();

            return await httpClient.setRoute("onboarding/create_account").setPayload({
                email: formValues.credentials.email,
                password: formValues.credentials.password,
                marketing: formValues.marketing,
                captcha: token,
                terms: formValues.terms,
            }).post();
        },
        onSuccess: async (response) => {
            console.log(response);
            if (response.data.finish_onboarding === false) {
                setConnectedBrands(response.data.connected_brands);
                setActiveOnboardingStep(1);
            } else {
                finishOnboarding();
            }
        },
        onError: (error) => {
            console.error(error);
        }
    });

    const { mutate: finishOnboarding } = useMutation({
        mutationFn: async () => {
            return await httpClient.setRoute("onboarding/finish_onboarding").setPayload({

            }).post();
        },
        onSuccess: () => {
            dispatch({ dispatchType: "setOnboardingComplete" });
        },
        onError: (error) => {
            console.error(error);
        }
    });

    const onboardingSteps = [
        (<LoadingStep/>),
        (<ConnectBrandStep connectedBrands={connectedBrands}/>),
    ];

    const signInSteps = [
        (<SignInForm setActiveSignInStep={setActiveSignInStep} finishOnboarding={finishOnboarding}/>),
        (<ConnectBrandStep connectedBrands={connectedBrands}/>),
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
            <OnboardingHeader
                logo={{ src: `${metricool.assets_url}img/mc-logo.svg`, alt: "Metricool Logo" }}
                actions={[
                    (__("Already a Metricooler?", "metricool")),
                    (
                        <Button
                            variant={"primary-gradient-ghost"}
                            className={"p-0 after:!bg-white after:!border-none !border-none"}
                            onClick={() => setSignInModalOpen(true)}
                        >
                            {__("Sign in here", "metricool")}
                        </Button>
                    )
                ]}
            >
                <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[30px]"} alt={__("Metricool logo", "metricool")}/>
                {__("The digital Swiss Army Knife for social media marketers", "metricool")}
            </OnboardingHeader>
            <div className={"w-full h-[2px] bg-[image:var(--gradient-brand-secondary)]"}></div>
            <FlexContainer direction={"row"} className={"w-full !gap-0 justify-between"}>
                <FlexContainer direction={"column"} className={"min-w-[45%] max-w-[45%]"}>
                    <h1 className={"font-bold font-nunito text-[1.75rem] leading-8"}>{__("Join more than 2 million professionals, agencies and brands that use Metricool as their one-stop shop for social media and online ad management.", "metricool")}</h1>
                    <OnboardingForm onSubmit={(values) => onSignUp(values)}/>
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