import { Button, FieldWrapper, FlexContainer, Icon, Input } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { type UseMutateFunction, useMutation } from "@tanstack/react-query";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { z } from "zod";
import OnboardingSchema from "@/support/form-schemas/OnboardingSchema.ts";
import type { Dispatch, SetStateAction } from "react";

const twoFaFormSchema = OnboardingSchema.pick({ twoFa: true });

type TwoFaFormProps = {
    setActiveStep?: Dispatch<SetStateAction<number>>,
    finishOnboarding: UseMutateFunction,
};
const TwoFaForm = ({ finishOnboarding, setActiveStep }: TwoFaFormProps) => {
    const {
        metricool,
        // httpClient
    } = useGlobalContext();

    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof twoFaFormSchema>>({
        resolver: zodResolver(twoFaFormSchema),
        defaultValues: {
            twoFa: "",
        }
    });

    const { mutate: onSubmit, isPending } = useMutation({
        mutationFn: async (formValues: z.infer<typeof twoFaFormSchema>) => {
            console.log(formValues);
            // return await httpClient.setRoute("onboarding/login").setPayload({
            //     twoFa: formValues.twoFa,
            // }).post();
        },
        onSuccess: async (response) => {
            console.log(response);
            if (metricool.from_legacy_upgrade && setActiveStep) {
                setActiveStep((prev) => prev + 1);
            } else {
                finishOnboarding();
            }
        },
        onError: (error) => {
            console.error(error);
        }
    });

    return (
        <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col items-center justify-center gap-6"}>
            <FlexContainer direction={"column"}>
                <FieldWrapper
                    required
                    label={__("Enter 2FA authentication code", "metricool")}
                    control={control}
                    name={"twoFa"}
                    render={(props) => (
                        <Input
                            {...props}
                            placeholder={__("Enter code", "metricool")}
                            className={"min-w-76 max-w-80"}
                        />
                    )}
                />
            </FlexContainer>
            <Button
                variant={"primary-gradient"}
                type={"submit"}
                disabled={(isPending || !(dirtyFields.twoFa))}
            >
                <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                    {__("Sign in", "metricool")}
                    <Icon icon={"arrow-right"}/>
                </FlexContainer>
            </Button>
        </form>
    );
};

export { TwoFaForm };