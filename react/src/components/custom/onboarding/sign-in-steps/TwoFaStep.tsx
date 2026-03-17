import { DialogHeader, DialogTitle, FlexContainer } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { type UseMutateFunction } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { TwoFaForm } from "@/components/custom/onboarding/sign-in-steps/TwoFaForm.tsx";
import type { Dispatch, SetStateAction } from "react";

type TwoFaStepProps = {
    setActiveStep?: Dispatch<SetStateAction<number>>,
    finishOnboarding: UseMutateFunction,
};
const TwoFaStep = ({ finishOnboarding, setActiveStep }: TwoFaStepProps) => {
    const {
        metricool,
        // httpClient
    } = useGlobalContext();

    return (
        <FlexContainer direction={"column"} className={"md:mx-8 mt-8 w-full"}>
            <DialogHeader className={"!gap-8 justify-center items-center"}>
                <img src={`${metricool.assets_url}img/metricool-logo.png`} className={"h-11 w-auto"} alt={__("Metricool logo", "metricool")}/>
                <DialogTitle className={"font-bold m-0 text-2xl leading-6"}>
                    {__("2FA authentication", "metricool")}
                </DialogTitle>
                <span className={"text-xl"}>{__("Please use your 2FA provider to sign in.", "metricool")}</span>
            </DialogHeader>
            <TwoFaForm setActiveStep={setActiveStep} finishOnboarding={finishOnboarding}/>
        </FlexContainer>
    );
};

export { TwoFaStep };