import { __ } from '@wordpress/i18n';
import { Button, FlexContainer } from "../components";
import { useGlobalContext } from "../context/GlobalContext.tsx";

export const OnboardingLayout = () => {
    const { metricool } = useGlobalContext();
    return (
        <FlexContainer direction={"column"} className={"w-full h-full px-20 py-12 !gap-0"}>
            <FlexContainer direction={"row"} className={"justify-between items-center pb-4"}>
                <FlexContainer direction={"row"} className={"text-base font-bold font-nunito items-center"}>
                    <img src={`${metricool.assets_url}img/mc-logo.svg`} alt={"Metricool logo"}/>
                    <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[30px]"} alt={"Metricool logo"}/>
                    {__("The digital Swiss Army Knife for social media marketers", "metricool")}
                </FlexContainer>
                <FlexContainer direction={"row"} className={"text-md font-semibold font-nunito items-center"}>
                    {__("Already a Metricooler?", "metricool")}
                    <Button variant={"primary-gradient-ghost"} className={"p-0 after:!bg-white after:!border-none !border-none"} onClick={() => console.log("hi")}>
                        {__("Sign in here", "metricool")}
                    </Button>
                </FlexContainer>
            </FlexContainer>
            <div className={"w-full h-[2px] bg-[image:var(--gradient-brand-secondary)]"}></div>
        </FlexContainer>
    );
};