import {
    Block,
    BlockHeader,
    FetchingErrorAlert,
    FieldWrapper,
    FlexContainer,
    FormFooter,
    Icon,
    Input,
    showToast,
    Switch
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { zodResolver } from "@hookform/resolvers/zod";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { useBlocker } from "@tanstack/react-router";
import { useMutation, useQuery } from "@tanstack/react-query";
import { queryClient } from "@/main.tsx";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

const userSettingsFormSchema = z.object({
    sendToAlternativeEmail: z.boolean(),
    alternativeEmail: z.email({
        error: () => __("Please enter a valid email address", "metricool"),
    }),
}).required();

/**
 * The Account Settings section in Settings.
 *
 * Is a `<form>` component which contains {@link Block}(s). This way the form's
 * onSubmit attribute can be used and a submit callback function doesn't have
 * to be passed down to the button in the {@link FormFooter}. No other button
 * with type "submit" should be added anywhere in the subtree of this component.
 *
 * Contains a {@link useQuery} which fetches the user settings.
 *
 * Contains a {@link useMutation} which updates the user settings and sets the
 * forms error states if updating fails.
 *
 * Contains a {@link useForm} which implements the {@link userSettingsFormSchema}.
 *
 * Contains a {@link useBlocker} which requests user confirmation to leave or
 * refresh the page if there are unsaved changes.
 *
 */
const AccountSettings = () => {
    const { httpClient, metricool } = useGlobalContext();
    const { data: values, isLoading, error: queryError, errorUpdateCount, refetch } = useQuery({
        queryKey: ["user_settings"],
        queryFn: () => httpClient.setRoute("user_settings").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): z.infer<typeof userSettingsFormSchema> => ({
            sendToAlternativeEmail: data.data.sendToAlternativeEmail,
            alternativeEmail: data.data.alternativeEmail,
        })
    });

    const {
        handleSubmit,
        formState: { errors: formValidationErrors, isDirty },
        getValues,
        resetField,
        control,
        setError,
    } = useForm<z.infer<typeof userSettingsFormSchema>>({
        resolver: zodResolver(userSettingsFormSchema),
        defaultValues: {
            sendToAlternativeEmail: false,
            alternativeEmail: "",
        },
        values,
    });

    const { mutate: onSubmit, isPending } = useMutation({
        mutationFn: async ({ sendToAlternativeEmail, alternativeEmail }: z.infer<typeof userSettingsFormSchema>) => {
            return httpClient.setRoute("user_settings").setPayload({
                "sendToAlternativeEmail": sendToAlternativeEmail,
                "alternativeEmail": alternativeEmail,
            }).post();
        },
        onSuccess: (response) => {
            queryClient.setQueryData(["user_settings"], { ...response });
            showToast.success(__("Settings have been saved", "metricool"));
        },
        onError: (data: {
            fields?: Record<keyof z.infer<typeof userSettingsFormSchema>, { message: string }>,
        }) => {
            showToast.error(__("There was an error updating your settings", "metricool"));
            if (data.fields) {
                try {
                    (Object.entries(data.fields) as [keyof z.infer<typeof userSettingsFormSchema>, {
                        message: string
                    }][]).forEach(([fieldKey, fieldContent]) => {
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
                        <FlexContainer direction={"row"} className={"justify-center items-center w-full grow"}>
                            <Icon icon={"loading"} className={"size-5"}/>
                        </FlexContainer>
                    ) : queryError ? (
                        <FetchingErrorAlert errorUpdateCount={errorUpdateCount} refetch={refetch} supportTicketLink={metricool.trusted_urls.new_support_ticket}/>
                    ) : (
                        <FlexContainer direction={"column"}>
                            <Controller
                                control={control}
                                name={"sendToAlternativeEmail"}
                                render={({ field, fieldState }) => (
                                    <FieldWrapper
                                        flexDirection={"row"}
                                        className={"justify-between"}
                                        label={__("Receive monthly summary", "metricool")}
                                        htmlFor={"send-to-alternative-email"}
                                        fieldState={{
                                            invalid: fieldState.invalid,
                                            error: { message: fieldState.error?.message }
                                        }}
                                    >
                                        <Switch
                                            id={"send-to-alternative-email"}
                                            checked={field.value}
                                            onCheckedChange={
                                                (checked) => {
                                                    field.onChange(checked);
                                                    // If the switch is set to false, the alternativeEmail field is
                                                    // reset so the form doesn't think there are unsaved changes on
                                                    // this field or send these changes to be saved
                                                    if (!checked) {
                                                        resetField("alternativeEmail");
                                                    }
                                                }
                                            }
                                        />
                                    </FieldWrapper>
                                )}
                            />
                            {/* Render the email input field only if the value of sendToAlternativeEmail is true */}
                            {getValues().sendToAlternativeEmail && (
                                <Controller
                                    control={control}
                                    name={"alternativeEmail"}
                                    render={({ field, fieldState }) => (
                                        <FieldWrapper
                                            label={__("Custom e-mail for the monthly summary", "metricool")}
                                            htmlFor={"alternative-email"}
                                            fieldState={{
                                                invalid: fieldState.invalid,
                                                error: { message: fieldState.error?.message }
                                            }}
                                        >
                                            <Input
                                                {...field}
                                                id={"alternative-email"}
                                                placeholder={__("Placeholder", "metricool")}
                                            />
                                        </FieldWrapper>
                                    )}
                                />
                            )}
                        </FlexContainer>
                    )}
                </Block>
            </FlexContainer>
            <FormFooter formHasUnsavedChanges={isDirty} formIsSubmitting={isPending} formHasErrors={Object.keys(formValidationErrors).length > 0}/>
        </form>
    );
};

export { AccountSettings };