import { Button, FieldWrapper, FlexContainer, Icon, Input, Switch } from "@/components/shared";
import { __, sprintf } from "@wordpress/i18n";
import { useForm } from "react-hook-form";
import DOMPurify from "dompurify";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import OnboardingSchema from "@/support/form-schemas/OnboardingSchema.ts";

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
            marketing: true,
        },
    });

    return (
        <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col gap-7.5"}>
            <FlexContainer direction={"column"}>
                <FieldWrapper
                    required
                    label={__("Email", "metricool")}
                    control={control}
                    name={"credentials.email"}
                    uniqueIdSuffix={"sign-up"}
                    render={(props) => (
                        <Input
                            {...props}
                            aria-required
                            placeholder={__("Enter your email", "metricool")}
                            className={"sm:max-w-5/6"}
                        />
                    )}
                />
                <FieldWrapper
                    required
                    label={__("Password", "metricool")}
                    control={control}
                    name={"credentials.password"}
                    uniqueIdSuffix={"sign-up"}
                    render={(props) => (
                        <Input
                            {...props}
                            aria-required
                            placeholder={__("Write your password here", "metricool")}
                            className={"sm:max-w-5/6"}
                            type={"password"}
                        />
                    )}
                />
            </FlexContainer>
            <FlexContainer direction={"column"} className={"!gap-1"}>
                <Button
                    variant={"primary-gradient"}
                    type={"submit"}
                    disabled={!(dirtyFields.credentials?.email && dirtyFields.credentials?.password)}
                >
                    <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                        {__("Create your free account", "metricool")}
                        <Icon icon={"arrow-right"}/>
                    </FlexContainer>
                </Button>
            </FlexContainer>
            <FlexContainer direction={"column"}>
                <FieldWrapper
                    control={control}
                    name={"terms"}
                    flexDirection={"row-reverse"}
                    className={"!gap-3 justify-end"}
                    render={(props) => (
                        <Switch
                            {...props}
                            aria-required
                            checked={props.value}
                            onCheckedChange={props.onChange}
                        />
                    )}
                    label={(
                        // When links need to be displayed within translatable text, our only option is to use dangerouslySetInnerHTML.
                        // DOMPurify is used to sanitize, with a custom hook to remove any element containing an href not specified in trusted_urls
                        <span
                            className={"required-asterisk"}
                            dangerouslySetInnerHTML={{
                                __html:
                                    DOMPurify.sanitize(
                                        sprintf(
                                            /*translators: the two variables are opening and closing anchor tags */
                                            __("I have read and accept the %1$sLegal Terms%2$s by Metricool.", "metricool"),
                                            `<a href=${metricool.trusted_urls.legal_terms} target="_blank" class="underline">`,
                                            `</a>`),
                                        { ADD_ATTR: ["target"] }
                                    )
                            }}
                        />
                    )}
                />
                <FieldWrapper
                    flexDirection={"row-reverse"}
                    className={"!gap-3 justify-end"}
                    label={__("I wish to receive communications about news and/or promotions from Metricool Software.", "metricool")}
                    control={control}
                    name={"marketing"}
                    render={(props) => (
                        <Switch
                            {...props}
                            checked={props.value}
                            onCheckedChange={props.onChange}
                        />
                    )}
                />
            </FlexContainer>
        </form>
    );
};

export { OnboardingForm };