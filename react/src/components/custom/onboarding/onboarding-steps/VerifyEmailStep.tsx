import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation } from "@tanstack/react-query";
import { Alert, Button, DialogHeader, DialogTitle, FlexContainer, Icon } from "@/components/shared";
import { __, sprintf } from "@wordpress/i18n";

type OnboardingStepsProps = {
    enteredEmail: string
};
const VerifyEmailStep = ({ enteredEmail }: OnboardingStepsProps) => {
    const { metricool } = useGlobalContext();

    const { mutate: resendEmail, isPending: resendEmailPending, isSuccess: resendEmailSuccess } = useMutation({
        mutationFn: async () => {
            // const response = await httpClient.setRoute("").setPayload({
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
            <DialogHeader className={"justify-center items-center"}>
                <img src={`${metricool.assets_url}img/onboarding-email-sent.svg`} alt={__("Email sent icon", "metricool")}/>
                <DialogTitle className={"font-bold font-nunito m-0 text-2xl"}>
                    {__("Thanks for signing up!", "metricool")}
                </DialogTitle>
            </DialogHeader>
            <FlexContainer direction={"column"} className={"w-full justify-center items-center text-base text-center"}>
                {sprintf(
                    /*translators: the variable is the email address the user entered */
                    __("We have sent you an email at %s so you can activate your account.", "metricool"),
                    enteredEmail,
                )}
                {resendEmailSuccess && (
                    <Alert variant={"info"}>
                        {sprintf(
                            /*translators: the variable is the email address the user entered */
                            __("We have resent the email to %s", "metricool"),
                            enteredEmail,
                        )}
                    </Alert>
                )}
            </FlexContainer>
            <Button
                variant={"primary-gradient-ghost"}
                onClick={() => resendEmail()}
            >
                <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                    {__("Resend email", "metricool")}
                    <Icon icon={resendEmailPending ? "loading" : "arrow-right"} className={"svg-gradient"}/>
                </FlexContainer>
            </Button>
        </FlexContainer>
    );
};

export { VerifyEmailStep };