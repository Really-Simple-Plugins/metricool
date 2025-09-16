import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";
import ListItem from "./ListItem.tsx";

const SettingsMenu = () => {
    return (
        <Card className={"sticky top-[3rem]"}>
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
    );
};

export default SettingsMenu;