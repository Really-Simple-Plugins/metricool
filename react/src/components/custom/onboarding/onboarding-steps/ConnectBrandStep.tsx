import {
    Button,
    DialogHeader,
    DialogTitle,
    FlexContainer,
    Select,
    SelectOption,
    FieldWrapper,
    Icon,
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { clsx } from "clsx";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import OnboardingSchema from "@/components/custom/onboarding/OnboardingSchema.ts";
import { useMutation } from "@tanstack/react-query";

const brandSchema = OnboardingSchema.shape.brand;

type ConnectBrandStepProps = {
    connectedBrands: z.infer<typeof brandSchema>[],
};

const ConnectBrandStep = ({ connectedBrands } : ConnectBrandStepProps) => {
    const { httpClient, dispatch, metricool } = useGlobalContext();

    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof brandSchema>>({
        resolver: zodResolver(brandSchema),
        defaultValues: {
            id: undefined,
        },
    });

    const { mutate: onSubmit } = useMutation({
        mutationFn: async (formValues: z.infer<typeof brandSchema>) => {
            console.log(formValues);
            return await httpClient.setRoute("onboarding/finish_onboarding").setPayload({
                blogId: formValues.id,
            }).post();
        },
        onSuccess: async () => {
            dispatch({dispatchType: "setOnboardingComplete"});
        }
    })

    return (
        <FlexContainer direction={"column"} className={"justify-center !gap-6 items-center"}>
            <FlexContainer direction={"column"} className={"w-full !gap-2"}>
                <DialogHeader className={"justify-center items-center"}>
                    <img src={`${metricool.assets_url}img/onboarding-connect-brand.svg`} alt={__("Link icon", "metricool")}/>
                    <DialogTitle className={"font-bold font-nunito m-0 text-2xl"}>
                        {__("Connect your brand", "metricool")}
                    </DialogTitle>
                </DialogHeader>
                <div className={"text-base text-center"}>
                    {__("Choose the brand that you want to connect to this website", "metricool")}
                </div>
            </FlexContainer>
            <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col items-center justify-center gap-6 w-full"}>
                <Controller
                    control={control}
                    name={"id"}
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
                                {connectedBrands.map((brand) => (
                                    <SelectOption
                                        value={brand.id}
                                        className={clsx("font-semibold hover:bg-primary-light/50 focus:bg-primary-light/50")}
                                    >
                                        {brand.label}
                                    </SelectOption>
                                ))}
                            </Select>
                        </FieldWrapper>
                    )}
                />
                <Button
                    variant={"black"}
                    type={"submit"}
                    disabled={!dirtyFields.id}
                >
                    <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                        {__("Finish", "metricool")}
                        <Icon icon={"arrow-right"}/>
                    </FlexContainer>
                </Button>
            </form>
        </FlexContainer>
    );
}

export { ConnectBrandStep };