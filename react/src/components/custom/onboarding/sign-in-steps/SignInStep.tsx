import { Button, DialogHeader, DialogTitle, FlexContainer, Icon } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

const SignInStep = () => {
    const { metricool } = useGlobalContext();

    return (
        <FlexContainer direction={"column"} className={"md:mx-8 mt-8 w-full"}>
            <DialogHeader className={"!gap-8 justify-center items-center"}>
                <img src={`${metricool.assets_url}img/logo.svg`} className={"h-11 w-auto"} alt={__("Metricool logo", "metricool")}/>
                <FlexContainer direction={"column"} className={"justify-center items-center"}>
                    <DialogTitle className={"font-nunito font-bold m-0 text-2xl leading-6"}>
                        {metricool.from_legacy_upgrade ?
                            __("Welcome to our new plugin!", "metricool") :
                            __("Sign In", "metricool")
                        }
                    </DialogTitle>
                    <span className={"text-base"}>
                        {metricool.from_legacy_upgrade ?
                            __("Sign in to your Metricool account to start using our new plugin!", "metricool") :
                            __("Sign in to your Metricool account ", "metricool")
                        }
                    </span>
                </FlexContainer>
                <Button
                    variant={"black"}
                    link={metricool.metricool_base_url}
                    target={"_self"}
                >
                    <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                        {__("Sign in on Metricool", "metricool")}
                        <Icon icon={"external-link"}/>
                    </FlexContainer>
                </Button>
            </DialogHeader>

        </FlexContainer>
    );
};

export { SignInStep };