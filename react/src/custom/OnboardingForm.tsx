import { Button, FieldWrapper, FlexContainer, Input, Switch } from "../components";
import { __, sprintf } from "@wordpress/i18n";
import { Controller, useForm } from "react-hook-form";
import DOMPurify from "dompurify";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useGlobalContext } from "../context/GlobalContext.tsx";

const formSchema = z.object({
    signUpEmail: z.email({
        error: () => __("Please enter a valid email address", "metricool"),
    }),
    signUpPassword: z.string().min(8, {
        error: () => __("Password must be at least 8 characters", "metricool"),
    }),
    terms: z.boolean().refine((val) => val === true, {
        error: () => __("Please read and accept the Legal Terms", "metricool"),
    }),
    marketing: z.boolean(),
}).required();

type OnboardingFormProps = {
    onSubmit: (values: z.infer<typeof formSchema>) => void,
};

const OnboardingForm = ({ onSubmit }: OnboardingFormProps) => {
    const { metricool } = useGlobalContext();
    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            signUpEmail: "",
            signUpPassword: "",
            terms: false,
            marketing: false,
        },
    });

    return (
        <FlexContainer direction={"column"} className={"min-w-[45%] max-w-[45%]"}>
            <h1 className={"font-bold font-nunito text-[1.75rem] leading-8"}>{__("Join more than 2 million professionals, agencies and brands that use Metricool as their one-stop shop for social media and online ad management.", "metricool")}</h1>
            <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col gap-4"}>
                <FlexContainer direction={"column"}>
                    <Controller
                        control={control}
                        name={"signUpEmail"}
                        render={({ field, fieldState }) => (
                            <FieldWrapper
                                required
                                label={__("Email", "metricool")}
                                htmlFor={"sign-up-email"}
                                fieldState={{
                                    invalid: fieldState.invalid,
                                    error: { message: fieldState.error?.message }
                                }}
                            >
                                <Input
                                    {...field}
                                    aria-required
                                    aria-invalid={fieldState.invalid}
                                    id={"sign-up-email"}
                                    placeholder={__("Enter your email", "metricool")}
                                    className={"max-w-5/6"}
                                />
                            </FieldWrapper>
                        )}
                    />
                    <Controller
                        control={control}
                        name={"signUpPassword"}
                        render={({ field, fieldState }) => (
                            <FieldWrapper
                                required
                                label={__("Password", "metricool")}
                                htmlFor={"sign-up-password"}
                                fieldState={{
                                    invalid: fieldState.invalid,
                                    error: { message: fieldState.error?.message }
                                }}
                            >
                                <Input
                                    {...field}
                                    aria-required
                                    aria-invalid={fieldState.invalid}
                                    id={"sign-up-password"}
                                    placeholder={__("Write your password here", "metricool")}
                                    className={"max-w-5/6"}
                                    type={"password"}
                                />
                            </FieldWrapper>
                        )}

                    />
                </FlexContainer>
                <Button
                    variant={"primary-gradient"}
                    icon={"arrow-right"}
                    iconPosition={"right"}
                    type={"submit"}
                    disabled={!(dirtyFields.signUpEmail && dirtyFields.signUpPassword)}
                >
                    {__("Create your free account", "metricool")}
                </Button>
                <FlexContainer direction={"column"}>
                    <Controller
                        control={control}
                        name={"terms"}
                        render={({ field, fieldState }) => (
                            <FieldWrapper
                                flexDirection={"row-reverse"}
                                className={"!gap-3 justify-end"}
                                label={(
                                    <span
                                        className={"required-asterisk"}
                                        dangerouslySetInnerHTML={{
                                            __html:
                                                DOMPurify.sanitize(
                                                    sprintf(
                                                        /*! translators: the two variables are opening and closing anchor tags */
                                                        __("I have read and accept the %sLegal Terms%s by Metricool.", "metricool"),
                                                        `<a href=${metricool.trusted_urls.legal_terms} target="_blank">`,
                                                        `</a>`),
                                                    { ADD_ATTR: ["target"] }
                                                )
                                        }}
                                    />
                                )}
                                htmlFor={"terms"}
                                fieldState={{
                                    invalid: fieldState.invalid,
                                    error: { message: fieldState.error?.message }
                                }}
                            >
                                <Switch
                                    aria-required
                                    id={"terms"}
                                    aria-invalid={fieldState.invalid}
                                    checked={field.value}
                                    onCheckedChange={field.onChange}
                                />
                            </FieldWrapper>
                        )}
                    />
                    <Controller
                        control={control}
                        name={"marketing"}
                        render={({ field, fieldState }) => (
                            <FieldWrapper
                                flexDirection={"row-reverse"}
                                className={"!gap-3 justify-end"}
                                label={(
                                    <span
                                        dangerouslySetInnerHTML={{
                                            __html: DOMPurify.sanitize(__("I wish to receive communications about news and/or promotions from Metricool Software.", "metricool"))
                                        }}
                                    />
                                )}
                                htmlFor={"marketing"}
                                fieldState={{
                                    invalid: fieldState.invalid,
                                    error: { message: fieldState.error?.message }
                                }}
                            >
                                <Switch
                                    id={"marketing"}
                                    checked={field.value}
                                    onCheckedChange={field.onChange}
                                />
                            </FieldWrapper>
                        )}
                    />
                </FlexContainer>
            </form>
        </FlexContainer>
    );
};

export default OnboardingForm;