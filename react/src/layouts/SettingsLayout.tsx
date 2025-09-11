import FlexContainer from "../custom/FlexContainer.tsx";
import Header from "../custom/Header.tsx";
import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import ListItem from "../custom/ListItem.tsx";

export const SettingsLayout = () => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full"}>
            <Header />
            <FlexContainer direction={"column"} className={"px-4 w-full justify-between md:flex-row"}>
                <Card>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Settings", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                    <FlexContainer direction={"column"} className={"!gap-3"}>
                        <ListItem className={"text-md text-primary cursor-pointer hover:underline font-semibold"}>{__("Account Settings", "metricool")}</ListItem>
                        <ListItem className={"text-md cursor-pointer hover:underline"}>{__("Connections", "metricool")}</ListItem>
                        <ListItem className={"text-md cursor-pointer hover:underline"} icon={"external-link"} iconPosition={"right"}>{__("Affiliation Program", "metricool")}</ListItem>
                        <ListItem className={"text-md text-upsell font-semibold cursor-pointer hover:underline"} icon={"pro"} iconClass={"rounded-full bg-(--color-upsell) size-2 p-0.5"} iconPosition={"right"}>{__("User Management", "metricool")}</ListItem>
                        <ListItem className={"text-md text-upsell font-semibold cursor-pointer hover:underline"} icon={"pro"} iconClass={"rounded-full bg-(--color-upsell) size-2 p-0.5"} iconPosition={"right"}>{__("My Tasks", "metricool")}</ListItem>
                    </FlexContainer>
                </Card>
                <FlexContainer direction={"column"} className={"md:min-w-[50%]"}>
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
                    <Card>
                        <CardHeader>
                            <CardHeaderTitle>
                                {__("Monthly summary", "metricool")}
                            </CardHeaderTitle>
                        </CardHeader>
                    </Card>
                </FlexContainer>
                <Card variant={"transparent"}>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Notifications", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                </Card>
            </FlexContainer>
        </FlexContainer>
    );
};