import { __ } from "@wordpress/i18n";
import { Button, Dialog, DialogHeader, DialogTitle, FlexContainer } from "../components";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import OnboardingHeader from "../custom/OnboardingHeader.tsx";
import { useState } from "react";
import SignInForm from "../custom/SignInForm.tsx";
import OnboardingForm from "../custom/OnboardingForm.tsx";

export const OnboardingLayout = () => {
    const { metricool } = useGlobalContext();
    const [openModal, setOpenModal] = useState(false);

    const onSubmit = (values: { email: string; password: string; terms: boolean; marketing: boolean; }) => {
        setOpenModal(true);
        console.log(values);
    };

    return (
        <FlexContainer direction={"column"} className={"w-full h-full px-20 py-12 !gap-0"}>
            <OnboardingHeader
                logo={{ src: `${metricool.assets_url}img/mc-logo.svg`, alt: "Metricool Logo" }}
                actions={[
                    (__("Already a Metricooler?", "metricool")),
                    (
                        <Button variant={"primary-gradient-ghost"} className={"p-0 after:!bg-white after:!border-none !border-none"} onClick={() => setOpenModal(true)}>
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
                <img src={`${metricool.assets_url}img/mc-onboarding-image.webp`} className={"max-w-[55%]"} alt={"Metricool logo"}/>
            </FlexContainer>
            <Dialog
                open={openModal}
                onOpenChange={() => setOpenModal(!openModal)}
                showCloseButton={true}
                className={"flex flex-col justify-center items-center"}
            >
                <DialogHeader className={"!gap-0 mt-8"}>
                    <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[37px]"} alt={"Metricool logo"}/>
                    <DialogTitle className={"font-bold font-nunito m-0"}>
                        {__("Sign in with your credentials", "metricool")}
                    </DialogTitle>
                </DialogHeader>
                <SignInForm onSubmit={(values) => console.log(values)}/>
            </Dialog>
        </FlexContainer>
    );
};