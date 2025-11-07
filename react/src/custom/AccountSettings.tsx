import { Block, BlockHeader, Button, Dialog, FlexContainer, Input, Label, showToast, Switch } from "../components";
import { __ } from "@wordpress/i18n";
import FormFooter from "./FormFooter.tsx";
import { zodResolver } from "@hookform/resolvers/zod";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { useBlocker } from "@tanstack/react-router";
import { useMutation, useQuery } from "@tanstack/react-query";
import { queryClient } from "../main.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useEffect } from "react";

const formSchema = z.object({
    receiveMonthlySummary: z.boolean(),
    customEmail: z.email(__("Please enter a valid email address.", "metricool")),
}).required();

const AccountSettings = () => {
    const { httpClient } = useGlobalContext();
    const { data: values, isLoading, error } = useQuery({
        enabled: !!httpClient,
        queryKey: ["user_settings"],
        queryFn: () => httpClient?.setRoute("user_settings").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data) => {
            console.log(data);
            return {
                receiveMonthlySummary: data.data.send_to_alternative_email,
                customEmail: data.data.alternative_email,
            };
        },
    });

    useEffect(() => {
        console.log(values, isLoading, error);
    }, [values, isLoading, error]);

    const {
        handleSubmit,
        formState: { errors, isDirty },
        control,
        setError,
    } = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            receiveMonthlySummary: false,
            customEmail: "",
        },
        values,
    });

    const { mutate: onSubmit } = useMutation({
        mutationFn: async ({ receiveMonthlySummary, customEmail }: z.infer<typeof formSchema>) => {
            const response = await httpClient?.setRoute("user_settings").setPayload({
                "send_to_alternative_email": receiveMonthlySummary,
                "alternative_email": customEmail,
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
                data: { send_to_alternative_email: boolean, alternative_email: string },
            } = queryClient.getQueryData(["user_settings"]) ?? {
                data: {
                    send_to_alternative_email: false,
                    alternative_email: "",
                }
            };
            queryClient.setQueryData(["user_settings"], {
                ...currentSettingsData,
                data: {
                    ...currentSettingsData.data,
                    send_to_alternative_email: data.send_to_alternative_email,
                    alternative_email: data.alternative_email,
                }
            });
            showToast.success(__("Settings have been saved", "metricool"));
        },
        onError: (data: {
            fields: { send_to_alternative_email: { message: string }, alternative_email: { message: string } },
        }) => {
            setError("receiveMonthlySummary", {
                type: "custom",
                message: data.fields?.send_to_alternative_email.message
            });
            setError("customEmail", {
                type: "custom",
                message: data.fields?.alternative_email.message
            });
            showToast.error(__("There was an error updating your settings", "metricool"));
        }
    });

    //noinspection JSVoidFunctionReturnValueUsed - useBlocker is not void if withResolver is passed as option, but PHPStorm doesn't realise this
    const { proceed, reset, status } = useBlocker({
        shouldBlockFn: () => isDirty,
        withResolver: true,
        enableBeforeUnload: isDirty,
    });

    return (
        <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Block className={"rounded-t-md rounded-b-none"}>
                    <BlockHeader title={__("Monthly summary", "metricool")}/>
                    <FlexContainer direction={"column"}>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <FlexContainer direction={"row"} className={"w-full justify-between"}>
                                <Label htmlFor={"receiveMonthlySummary"}>{__("Receive monthly summary", "metricool")}</Label>
                                <Controller
                                    control={control}
                                    render={({ field }) =>
                                        <Switch checked={field.value} onCheckedChange={field.onChange}/>}
                                    name={"receiveMonthlySummary"}
                                />
                            </FlexContainer>
                            <span className={"text-red-500 text-sm"}>{errors.receiveMonthlySummary?.message}</span>
                        </FlexContainer>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <Label htmlFor={"customEmail"}>{__("Custom e-mail for the monthly summary", "metricool")}</Label>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Input {...field} id={"customEmail"} placeholder={__("Placeholder", "metricool")}/>}
                                name={"customEmail"}
                            />
                            <span className={"text-red-500 text-sm"}>{errors.customEmail?.message}</span>
                        </FlexContainer>
                    </FlexContainer>
                </Block>
            </FlexContainer>
            <FormFooter unsavedChanges={isDirty}/>
            {status === "blocked" && (
                <Dialog open={status === "blocked"}>
                    <p>{__("You have unsaved changes. Are you sure you want to leave?", "simplybook")}</p>
                    <p>{__("Your changes will be lost.", "simplybook")}</p>
                    <FlexContainer direction={"row"} className={"w-full justify-center"}>
                        <Button variant={"black"} onClick={proceed}>{__("Leave", "metricool")}</Button>
                        <Button variant={"black"} onClick={reset}>{__("Stay", "metricool")}</Button>
                    </FlexContainer>
                </Dialog>
            )}
        </form>
    );
};

export default AccountSettings;