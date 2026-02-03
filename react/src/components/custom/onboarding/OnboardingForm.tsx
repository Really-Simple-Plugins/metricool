import { Button, FieldWrapper, FlexContainer, Input, Switch } from "@/components";
import { __, sprintf } from "@wordpress/i18n";
import { Controller, useForm } from "react-hook-form";
import DOMPurify from "dompurify";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import OnboardingSchema from "./OnboardingSchema.ts";

const onboardingFormSchema = OnboardingSchema.omit({ brand: true });

type OnboardingFormProps = {
    onSubmit: (values: z.infer<typeof onboardingFormSchema>) => void,
};

/**
 * The main form used in the {@link OnboardingLayout}.
 *
 * Gets passed the onSubmit mutation function from OnboardingLayout.
 *
 * Contains a {@link useForm} which implements the {@link OnboardingSchema}
 *
 */
const OnboardingForm = ({ onSubmit }: OnboardingFormProps) => {
    const { metricool } = useGlobalContext();
    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof onboardingFormSchema>>({
        resolver: zodResolver(onboardingFormSchema),
        defaultValues: {
            credentials: {
                email: "",
                password: "",
            },
            terms: false,
            marketing: false,
        },
    });

    return (
        <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col gap-4"}>
            <FlexContainer direction={"column"}>
                <Controller
                    control={control}
                    name={"credentials.email"}
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
                    name={"credentials.password"}
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
                disabled={!(dirtyFields.credentials?.email && dirtyFields.credentials?.password)}
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
                                // When links need to be displayed within translatable text, our only option is to use dangerouslySetInnerHTML.
                                // DOMPurify is used to sanitize, with a custom hook to remove any element containing an href not specified in trusted_urls
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
                            label={__("I wish to receive communications about news and/or promotions from Metricool Software.", "metricool")}
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
    );
};

export default OnboardingForm;