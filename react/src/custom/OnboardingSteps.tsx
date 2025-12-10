import {
    Button,
    DialogHeader,
    DialogTitle,
    FlexContainer,
    Icon,
    Select,
    SelectOption,
    FieldWrapper
} from "../components";
import DOMPurify from "dompurify";
import { __, sprintf } from "@wordpress/i18n";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useMutation } from "@tanstack/react-query";
import { clsx } from "clsx";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";

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
        onSuccess: (data) => {
            console.log(data);
        },
        onError: (data) => {
            console.log(data);
        }
    });
    return (
        <FlexContainer direction={"column"} className={"justify-center items-center"}>
            <DialogHeader className={"!gap-8 justify-center items-center"}>
                <img src={`${metricool.assets_url}img/metricool_welcome.png`} className={"min-h-[150px] max-h-[150px] w-auto"} alt={"Metricool welcome"}/>
                <FlexContainer direction={"column"} className={"w-full justify-center items-center"}>
                    {resendEmailSuccess && (
                        <FlexContainer direction={"row"} className={"!gap-2 justify-center items-center rounded-md bg-rsp-success-light px-3 py-2 "}>
                            <Icon icon={"info"} iconClass={"text-rsp-success-dark"}/>
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

const formSchema = z.object({
    brand: z.string(),
}).required();

const ConnectBrandStep = () => {
    const { dispatch } = useGlobalContext();

    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            brand: "",
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        console.log(values);
        dispatch({dispatchType: "setOnboardingComplete"});
    }

    return (
        <FlexContainer direction={"column"} className={"justify-center !gap-6 items-center w-2/3"}>
            <FlexContainer direction={"column"} className={"w-full !gap-2"}>
                <DialogHeader className={"!gap-8 justify-center items-center"}>
                    <DialogTitle className={"font-bold font-nunito m-0 text-2xl"}>
                        {__("Connect your brand", "metricool")}
                    </DialogTitle>
                </DialogHeader>
                <div className={"text-base text-center"}>
                    {__("Choose the brand that you want to connect to this website", "metricool")}
                </div>
            </FlexContainer>
            <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col items-center justify-center gap-4 w-full"}>
                <Controller
                    control={control}
                    name={"brand"}
                    render={({ field, fieldState }) => (
                        <FieldWrapper
                            htmlFor={"select-brand"}
                            label={__("Choose your brand", "metricool")}
                            fieldState={{
                                invalid: fieldState.invalid,
                                error: { message: fieldState.error?.message }
                            }}
                        >
                            <Select
                                onValueChange={field.onChange}
                                id={"select-brand"}
                                className={"border-neutral-200 font-semibold !text-black"}
                                placeholder={__("Select a brand", "metricool")}
                            >
                                <SelectOption
                                    value={"1"}
                                    className={clsx("font-semibold hover:bg-primary-light/50 focus:bg-primary-light/50")}
                                >
                                    {__("Brand one", "metricool")}
                                </SelectOption>
                                <SelectOption
                                    value={"2"}
                                    className={clsx("font-semibold hover:bg-primary-light/50 focus:bg-primary-light/50")}
                                >
                                    {__("Brand two", "metricool")}
                                </SelectOption>
                            </Select>
                        </FieldWrapper>
                    )}
                />
                <Button
                    variant={"black"}
                    type={"submit"}
                    icon={"arrow-right"}
                    iconPosition={"right"}
                    disabled={!dirtyFields.brand}
                >
                    {__("Finish", "metricool")}
                </Button>
            </form>
        </FlexContainer>
    );
}

export { VerifyEmailStep, LoadingStep, ConnectBrandStep };