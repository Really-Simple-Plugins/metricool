import { Button, FieldWrapper, FlexContainer, Input } from "../../components";
import { __ } from "@wordpress/i18n";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import OnboardingSchema from "./OnboardingSchema.ts";

const signInSchema = OnboardingSchema.pick({ credentials: true });

type SignInFormProps = {
    onSubmit: (values: z.infer<typeof signInSchema>) => void,
};
const SignInForm = ({ onSubmit }: SignInFormProps) => {
    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof signInSchema>>({
        resolver: zodResolver(signInSchema),
        defaultValues: {
            credentials: {
                email: "",
                password: "",
            },
        },
    });

    return (
        <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col items-center justify-center gap-6"}>
            <FlexContainer direction={"column"}>
                <Controller
                    control={control}
                    name={"credentials.email"}
                    render={({ field, fieldState }) => (
                        <FieldWrapper
                            required
                            label={__("Email", "metricool")}
                            htmlFor={"sign-in-email"}
                            fieldState={{
                                invalid: fieldState.invalid,
                                error: { message: fieldState.error?.message }
                            }}
                        >
                            <Input
                                {...field}
                                aria-invalid={fieldState.invalid}
                                id={"sign-in-email"}
                                placeholder={__("Enter your email", "metricool")}
                                className={"min-w-76 max-w-80"}
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
                            htmlFor={"sign-in-password"}
                            fieldState={{
                                invalid: fieldState.invalid,
                                error: { message: fieldState.error?.message }
                            }}
                        >
                            <Input
                                {...field}
                                aria-invalid={fieldState.invalid}
                                id={"sign-in-password"}
                                placeholder={__("Write your password here", "metricool")}
                                className={"min-w-76 max-w-80"}
                                type={"password"}
                            />
                        </FieldWrapper>
                    )}
                />
            </FlexContainer>
            <Button
                variant={"black"}
                icon={"arrow-right"}
                iconPosition={"right"}
                type={"submit"}
                disabled={!(dirtyFields.credentials?.email && dirtyFields.credentials?.password)}
            >
                {__("Sign in", "metricool")}
            </Button>
        </form>
    );
};

export default SignInForm;