import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";
import FormFooter from "./FormFooter.tsx";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { z } from "zod";

const formSchema = z.object({
    firstName: z.string(),
    lastName: z.string(),
    language: z.literal(["english"]),
    timeZone: z.literal(["Europe/Amsterdam"]),
    weekStart: z.literal(["monday", "sunday"]),
    receiveMonthlySummary: z.boolean(),
    customEmail: z.literal(["english"]),
}).required();

const AccountSettings = () => {
    const {
        register,
        handleSubmit,
        formState: { errors },
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
        <form onSubmit={handleSubmit(onSubmit)} className={"flex flex-col md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Card>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Personal Information", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Preferences", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                </Card>
                <Card className={"rounded-t-md rounded-b-none"}>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Monthly summary", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                </Card>
            </FlexContainer>
            <FormFooter/>
        </form>
    );
};

export default AccountSettings;