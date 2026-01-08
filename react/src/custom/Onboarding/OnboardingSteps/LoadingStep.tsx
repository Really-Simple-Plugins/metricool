import { useGlobalContext } from "../../../context/GlobalContext.tsx";
import { DialogHeader, DialogTitle, FlexContainer } from "../../../components";
import { __ } from "@wordpress/i18n";

const LoadingStep = () => {
    const { metricool } = useGlobalContext();
    return (
        <FlexContainer direction={"column"} className={"justify-center items-center"}>
            <DialogHeader className={"!gap-8 justify-center items-center"}>
                <img src={`${metricool.assets_url}img/metricool_welcome.png`} className={"min-h-[150px] max-h-[150px] w-auto"} alt={"Metricool welcome"}/>
                <DialogTitle className={"font-bold font-nunito m-0 text-2xl loading-ellipses"}>
                    {__("Creating Awesomeness", "metricool")}
                </DialogTitle>
            </DialogHeader>
            <div className={"text-base text-center"}>
                {__("We’re setting up your Metricool account.", "metricool")}
            </div>
        </FlexContainer>
    );
}

export default LoadingStep;