import { Card, CardHeader, CardHeaderTitle, Input, Label, Select, SelectOption, Switch } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";
import FormFooter from "./FormFooter.tsx";
import { zodResolver } from "@hookform/resolvers/zod";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";

/**
 * List of all timezones as strings. Used for validation and to map over for select options.
 * Doesn't get localised/translated when changing browser language.
 */
const timeZones = Intl.supportedValuesOf("timeZone");

const formSchema = z.object({
    firstName: z.string().min(1, {
        error: __("First name is required.", "metricool")
    }),
    lastName: z.string().min(1, {
        error: __("Last name is required.", "metricool")
    }),
    language: z.literal(["english", "german"]),
    timeZone: z.literal(timeZones),
    weekStart: z.literal(["monday", "sunday"]),
    receiveMonthlySummary: z.boolean(),
    customEmail: z.literal(["english"], { error: __("Can only be English", "metricool") }),
}).required();

const AccountSettings = () => {
    const {
        handleSubmit,
        formState: { errors },
        control,
    } = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            firstName: "",
            lastName: "",
            language: "english",
            timeZone: "Europe/Amsterdam",
            weekStart: "sunday",
            receiveMonthlySummary: false,
            customEmail: "english",
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        console.log(values);
    };

    return (
        <form onSubmit={handleSubmit(onSubmit)} className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Card>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Personal Information", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                    <FlexContainer direction={"column"}>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <Label htmlFor="firstName">{__("First Name", "metricool")}</Label>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Input {...field} id={"firstName"} placeholder={__("Placeholder", "metricool")}/>}
                                name="firstName"
                            />
                            <span className={"text-red-500 text-sm"}>{errors.firstName?.message}</span>
                        </FlexContainer>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <Label htmlFor="lastName">{__("Last Name", "metricool")}</Label>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Input {...field} id={"lastName"} placeholder={__("Placeholder", "metricool")}/>}
                                name="lastName"
                            />
                            <span className={"text-red-500 text-sm"}>{errors.lastName?.message}</span>
                        </FlexContainer>
                    </FlexContainer>
                </Card>
                <Card>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Preferences", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                    <FlexContainer direction={"column"}>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <Label htmlFor="language">{__("Language", "metricool")}</Label>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Select onValueChange={field.onChange} defaultValue={field.value} id={"language"} placeholder={field.value}>
                                        <SelectOption value="english">{__("English", "metricool")}</SelectOption>
                                        <SelectOption value="german">{__("German", "metricool")}</SelectOption>
                                    </Select>}
                                name="language"
                            />
                            <span className={"text-red-500 text-sm"}>{errors.language?.message}</span>
                        </FlexContainer>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <Label htmlFor="timeZone">{__("Time Zone", "metricool")}</Label>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Select onValueChange={field.onChange} defaultValue={field.value} id={"timeZone"} placeholder={field.value}>
                                        {timeZones.map((zone) => (
                                            <SelectOption value={zone}>{zone}</SelectOption>
                                        ))}
                                    </Select>}
                                name="timeZone"
                            />
                            <span className={"text-red-500 text-sm"}>{errors.timeZone?.message}</span>
                        </FlexContainer>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <Label htmlFor="weekStart">{__("First day of the week", "metricool")}</Label>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Select onValueChange={field.onChange} defaultValue={field.value} id={"weekStart"} placeholder={field.value}>
                                        <SelectOption value="sunday">{__("Sunday", "metricool")}</SelectOption>
                                        <SelectOption value="monday">{__("Monday", "metricool")}</SelectOption>
                                    </Select>}
                                name="weekStart"
                            />
                            <span className={"text-red-500 text-sm"}>{errors.weekStart?.message}</span>
                        </FlexContainer>
                    </FlexContainer>
                </Card>
                <Card className={"rounded-t-md rounded-b-none"}>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Monthly summary", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                    <FlexContainer direction={"column"}>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <FlexContainer direction={"row"}>
                                <Label htmlFor="receiveMonthlySummary">{__("Receive monthly summary", "metricool")}</Label>
                                <Controller
                                    control={control}
                                    render={({ field }) =>
                                        <Switch checked={field.value} onCheckedChange={field.onChange}/>}
                                    name="receiveMonthlySummary"
                                />
                            </FlexContainer>
                            <span className={"text-red-500 text-sm"}>{errors.receiveMonthlySummary?.message}</span>
                        </FlexContainer>
                        <FlexContainer direction={"column"} className={"!gap-2"}>
                            <Label htmlFor="customEmail">{__("Custom e-mail for the monthly summary", "metricool")}</Label>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Select onValueChange={field.onChange} defaultValue={field.value} id={"customEmail"} placeholder={field.value}>
                                        <SelectOption value="english">{__("English", "metricool")}</SelectOption>
                                        <SelectOption value="german">{__("German", "metricool")}</SelectOption>
                                    </Select>}
                                name="customEmail"
                            />
                            <span className={"text-red-500 text-sm"}>{errors.customEmail?.message}</span>
                        </FlexContainer>
                    </FlexContainer>
                </Card>
            </FlexContainer>
            <FormFooter/>
        </form>
    );
};

export default AccountSettings;