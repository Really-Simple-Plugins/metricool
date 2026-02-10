import { Button, FieldWrapper, FlexContainer, Icon, Input } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import OnboardingSchema from "@/components/custom/onboarding/OnboardingSchema.ts";
import { type UseMutateFunction, useMutation } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import type { Dispatch, SetStateAction } from "react";

const signInSchema = OnboardingSchema.pick({ credentials: true });

type SignInFormProps = {
    setActiveSignInStep: Dispatch<SetStateAction<number>>,
    finishOnboarding: UseMutateFunction
};

const SignInForm = ({ setActiveSignInStep, finishOnboarding }: SignInFormProps) => {
    const { httpClient } = useGlobalContext();

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

    const { mutate: onSubmit } = useMutation({
        mutationFn: async (formValues: z.infer<typeof signInSchema>) => {
            return await httpClient.setRoute("onboarding/login").setPayload({
                email: formValues.credentials.email,
                password: formValues.credentials.password,
            }).post();
        },
        onSuccess: async (response) => {
            console.log(response);
            if (response.data.finish_onboarding === false) {
                setActiveSignInStep(1);
            } else {
                finishOnboarding();
            }

        },
        onError: (error) => {
            console.error(error);
        }
    })

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
                type={"submit"}
                disabled={!(dirtyFields.credentials?.email && dirtyFields.credentials?.password)}
            >
                <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                    {__("Sign in", "metricool")}
                    <Icon icon={"arrow-right"}/>
                </FlexContainer>

            </Button>
        </form>
    );
};

export { SignInForm };