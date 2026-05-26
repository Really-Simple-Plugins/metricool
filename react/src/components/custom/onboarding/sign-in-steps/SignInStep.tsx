import { Alert, Button, DialogHeader, DialogTitle, FlexContainer, Icon } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useAuthenticationData } from "@/hooks/useAuthenticationData.tsx";

const SignInStep = () => {
    const { metricool } = useGlobalContext();

    const {
        signInRedirectUrlMutation: { mutate: getRedirectUrl, isPending, error }
    } = useAuthenticationData();

    return (
        <FlexContainer direction={"column"} className={"md:mx-8 mt-8 w-full"}>
            <DialogHeader className={"!gap-8 justify-center items-center"}>
                <img src={`${metricool.assets_url}img/logo.svg`} className={"h-11 w-auto"} alt={__("Metricool logo", "metricool")}/>
                <FlexContainer direction={"column"} className={"justify-center items-center"}>
                    <DialogTitle className={"font-nunito font-bold m-0 text-2xl leading-6"}>
                        {__("Sign In", "metricool")}
                    </DialogTitle>
                    <span className={"text-base"}>
                        {metricool.onboarding.mode.forced_login ?
                            __("Sign in to discover the new Metricool plugin!", "metricool") :
                            __("Sign in to link WordPress to your Metricool account", "metricool")
                        }
                    </span>
                </FlexContainer>
                {error && (<Alert variant={"error"}>{error.message}</Alert>)}
                <Button
                    variant={"black"}
                    onClick={() => getRedirectUrl()}
                    disabled={isPending}
                >
                    <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                        {__("Sign in on Metricool", "metricool")}
                        <Icon icon={isPending ? "loading" : "external-link"}/>
                    </FlexContainer>
                </Button>
            </DialogHeader>
        </FlexContainer>
    );
};

export { SignInStep };