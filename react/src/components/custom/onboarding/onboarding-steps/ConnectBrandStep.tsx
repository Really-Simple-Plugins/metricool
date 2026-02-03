import {
    Button,
    DialogHeader,
    DialogTitle,
    FlexContainer,
    Select,
    SelectOption,
    FieldWrapper
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { clsx } from "clsx";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import OnboardingSchema from "../OnboardingSchema.ts";

const brandSchema = OnboardingSchema.pick({ brand: true });

const ConnectBrandStep = () => {
    const { dispatch, metricool } = useGlobalContext();

    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof brandSchema>>({
        resolver: zodResolver(brandSchema),
        defaultValues: {
            brand: "",
        },
    });

    const onSubmit = (values: z.infer<typeof brandSchema>) => {
        console.log(values);
        dispatch({dispatchType: "setOnboardingComplete"});
    }

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

export default ConnectBrandStep;