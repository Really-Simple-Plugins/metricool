import { useGlobalContext } from "../../../context/GlobalContext.tsx";
import { useMutation } from "@tanstack/react-query";
import { Button, DialogHeader, DialogTitle, FlexContainer, Icon } from "../../../components";
import DOMPurify from "dompurify";
import { __, sprintf } from "@wordpress/i18n";

type OnboardingStepsProps = {
    enteredEmail: string
};
const VerifyEmailStep = ({ enteredEmail }: OnboardingStepsProps) => {
    const { metricool } = useGlobalContext();

    const { mutate: resendEmail, isPending: resendEmailPending, isSuccess: resendEmailSuccess } = useMutation({
        mutationFn: async () => {
            // const response = await httpClient?.setRoute("").setPayload({
            // }).post();

            return new Promise(resolve => setTimeout(resolve, 2000));
        },
        onSuccess: (response) => {
            console.log(response);
        },
        onError: (error) => {
            console.error(error);
        }
    });
    return (
        <FlexContainer direction={"column"} className={"justify-center items-center"}>
            <DialogHeader className={"!gap-8 justify-center items-center"}>
                <img src={`${metricool.assets_url}img/metricool_welcome.png`} className={"min-h-[150px] max-h-[150px] w-auto"} alt={"Metricool welcome"}/>
                <FlexContainer direction={"column"} className={"w-full justify-center items-center"}>
                    {resendEmailSuccess && (
                        <FlexContainer direction={"row"} className={"!gap-2 justify-center items-center rounded-md bg-rsp-success-light px-3 py-2 "}>
                            <Icon icon={"info"} className={"text-rsp-success-dark"}/>
                            <div className={"text-md text-center text-rsp-success-dark font-semibold"}
                                 dangerouslySetInnerHTML={{
                                     __html:
                                         DOMPurify.sanitize(
                                             sprintf(
                                                 /*! translators: the variable is the email address the user entered */
                                                 __("We have resent the email to %s", "metricool"),
                                                 enteredEmail,
                                             )
                                         )
                                 }}
                            ></div>
                        </FlexContainer>
                    )}
                    <DialogTitle className={"font-bold font-nunito m-0 text-2xl"}>
                        {__("Thanks for signing up!", "metricool")}
                    </DialogTitle>
                </FlexContainer>
            </DialogHeader>
            <div className={"text-base text-center"}
                 dangerouslySetInnerHTML={{
                     __html:
                         DOMPurify.sanitize(
                             sprintf(
                                 /*! translators: the variable is the email address the user entered */
                                 __("We have sent you an email at %s so you can activate your account.", "metricool"),
                                 enteredEmail,
                             )
                         )
                 }}
            >
            </div>
            <Button
                variant={"primary-gradient-ghost"}
                onClick={() => resendEmail()}
                icon={resendEmailPending ? "loading" : "arrow-right"}
                iconClass={"svg-gradient"}
                iconPosition={"right"}>
                {__("Resend email", "metricool")}
            </Button>
        </FlexContainer>
    );
};

export default VerifyEmailStep;