import {
    Block,
    BlockHeader,
    Button,
    Dialog,
    FlexContainer,
    Input,
    Label,
    Switch
} from "../components";
import { __ } from "@wordpress/i18n";
import FormFooter from "./FormFooter.tsx";
import { zodResolver } from "@hookform/resolvers/zod";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { useBlocker } from "@tanstack/react-router";

const formSchema = z.object({
    receiveMonthlySummary: z.boolean(),
    customEmail: z.email(__("Please enter a valid email address.", "metricool")),
}).required();

const AccountSettings = () => {
    const {
        handleSubmit,
        formState: { errors, isDirty },
        control,
    } = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            receiveMonthlySummary: false,
            customEmail: "",
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        console.log(values);
    };

    //noinspection JSVoidFunctionReturnValueUsed - useBlocker is not void if withResolver is passed as option, but PHPStorm doesn't realise this
    const { proceed, reset, status } = useBlocker({
        shouldBlockFn: () => isDirty,
        withResolver: true,
        enableBeforeUnload: isDirty,
    });

    return (
        <form onSubmit={handleSubmit(onSubmit)} className={"flex flex-col min-w-full md:min-w-[50%]"}>
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
            <FormFooter/>
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