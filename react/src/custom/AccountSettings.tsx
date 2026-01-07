import { Block, BlockHeader, FlexContainer, Input, Label, showToast, Switch, Icon } from "../components";
import { __ } from "@wordpress/i18n";
import FormFooter from "./FormFooter.tsx";
import { zodResolver } from "@hookform/resolvers/zod";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { useBlocker } from "@tanstack/react-router";
import { useMutation, useQuery } from "@tanstack/react-query";
import { queryClient } from "../main.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";

const formSchema = z.object({
    sendToAlternativeEmail: z.boolean(),
    alternativeEmail: z.email(__("Please enter a valid email address", "metricool")),
}).required();

const AccountSettings = () => {
    const { httpClient } = useGlobalContext();
    const { data: values, isLoading, error: queryError } = useQuery({
        enabled: !!httpClient,
        queryKey: ["user_settings"],
        queryFn: () => httpClient?.setRoute("user_settings").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): z.infer<typeof formSchema> => ({
            sendToAlternativeEmail: data.data.sendToAlternativeEmail,
            alternativeEmail: data.data.alternativeEmail,
        })
    });

    const {
        handleSubmit,
        formState: { errors: formValidationErrors, isDirty },
        control,
        setError,
    } = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            sendToAlternativeEmail: false,
            alternativeEmail: "",
        },
        values,
    });

    const { mutate: onSubmit, isPending } = useMutation({
        mutationFn: async ({ sendToAlternativeEmail, alternativeEmail }: z.infer<typeof formSchema>) => {
            const response = await httpClient?.setRoute("user_settings").setPayload({
                "sendToAlternativeEmail": sendToAlternativeEmail,
                "alternativeEmail": alternativeEmail,
            }).post();

            const newFormValues = response?.data;

            if (!newFormValues) {
                console.error("Error updating settings: ", response?.message);
                showToast.error(__("There was an error updating your settings", "metricool"));
                return;
            }

            return newFormValues;
        },
        onSuccess: (data) => {
            const currentSettingsData: {
                data: z.infer<typeof formSchema>,
            } = queryClient.getQueryData(["user_settings"]) ?? {
                data: {
                    sendToAlternativeEmail: false,
                    alternativeEmail: "",
                }
            };
            queryClient.setQueryData(["user_settings"], {
                ...currentSettingsData,
                data: {
                    ...currentSettingsData.data,
                    sendToAlternativeEmail: data.sendToAlternativeEmail,
                    alternativeEmail: data.alternativeEmail,
                }
            });
            showToast.success(__("Settings have been saved", "metricool"));
        },
        onError: (data: {
            fields?: Record<keyof z.infer<typeof formSchema>, { message: string }>,
        }) => {
            showToast.error(__("There was an error updating your settings", "metricool"));
            if (data.fields) {
                try {
                    (Object.entries(data.fields) as [keyof z.infer<typeof formSchema>, {message: string}][]).forEach(([fieldKey, fieldContent]) => {
                        setError(fieldKey, {
                            type: "custom",
                            message: fieldContent?.message,
                        });
                    });
                } catch (error) {
                    console.error("There was an error setting the form errors: " + error);
                }
            }
        }
    });

    useBlocker({
        shouldBlockFn: () => {
            if (!isDirty) {
                return false; // Dont block
            }

            const shouldLeave = window.confirm(
                __("This page is asking you to confirm that you want to leave — information you’ve entered may not be saved.", "metricool"),
            );

            return !shouldLeave;
        },
        enableBeforeUnload: isDirty,
    });

    return (
        <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Block className={"rounded-t-md rounded-b-none"}>
                    <BlockHeader title={__("Monthly summary", "metricool")}/>
                    {isLoading ? (
                        <FlexContainer direction={"row"} className={"justify-center items-center w-full h-full"}>
                            <Icon icon={"loading"} className={"size-5"}/>
                        </FlexContainer>
                    ) : queryError ? (
                        <div>
                            {__("There was an error fetching your monthly summary settings", "metricool")}
                        </div>
                    ) : (
                        <FlexContainer direction={"column"}>
                            <FlexContainer direction={"column"} className={"!gap-2"}>
                                <FlexContainer direction={"row"} className={"w-full justify-between"}>
                                    <Label htmlFor={"sendToAlternativeEmail"}>{__("Receive monthly summary", "metricool")}</Label>
                                    <Controller
                                        control={control}
                                        render={({ field }) =>
                                            <Switch checked={field.value} onCheckedChange={field.onChange}/>}
                                        name={"sendToAlternativeEmail"}
                                    />
                                </FlexContainer>
                                <span className={"text-red-500 text-sm"}>{formValidationErrors.sendToAlternativeEmail?.message}</span>
                            </FlexContainer>
                            <FlexContainer direction={"column"} className={"!gap-2"}>
                                <Label htmlFor={"alternativeEmail"}>{__("Custom e-mail for the monthly summary", "metricool")}</Label>
                                <Controller
                                    control={control}
                                    render={({ field }) =>
                                        <Input {...field} id={"alternativeEmail"} placeholder={__("Placeholder", "metricool")}/>}
                                    name={"alternativeEmail"}
                                />
                                <span className={"text-red-500 text-sm"}>{formValidationErrors.alternativeEmail?.message}</span>
                            </FlexContainer>
                        </FlexContainer>
                    )}
                </Block>
            </FlexContainer>
            <FormFooter formHasUnsavedChanges={isDirty} formIsSubmitting={isPending} formHasErrors={Object.keys(formValidationErrors).length > 0}/>
        </form>
    );
};

export default AccountSettings;