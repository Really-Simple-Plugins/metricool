import { Link } from "@tanstack/react-router";
import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";
import ListItem from "./ListItem.tsx";

const SettingsMenu = () => {
    return (
        <Card className={"md:sticky md:top-[3rem]"}>
            <CardHeader>
                <CardHeaderTitle>
                    {__("Settings", "metricool")}
                </CardHeaderTitle>
            </CardHeader>
            <FlexContainer direction={"column"} className={"!gap-3"}>
                <Link to={"/settings/account"} className="text-md text-black hover:underline [&.active]:text-primary [&.active]:font-semibold [&.active]:border-none focus:shadow-none">
                    {__("Account Settings", "metricool")}
                </Link>
                <Link to={"/settings/connections"} className="text-md text-black hover:underline [&.active]:text-primary [&.active]:font-semibold [&.active]:border-none focus:shadow-none">
                    {__("Connections", "metricool")}
                </Link>
                <ListItem className={"text-md cursor-pointer hover:underline"} icon={"inline-external-link"} iconPosition={"right"}>{__("Affiliation Program", "metricool")}</ListItem>
                <ListItem className={"text-md text-upsell font-semibold cursor-pointer hover:underline"} icon={"upsell"} iconClass={"rounded-full bg-upsell size-2 p-0.5"} iconPosition={"right"}>{__("User Management", "metricool")}</ListItem>
                <ListItem className={"text-md text-upsell font-semibold cursor-pointer hover:underline"} icon={"upsell"} iconClass={"rounded-full bg-upsell size-2 p-0.5"} iconPosition={"right"}>{__("My Tasks", "metricool")}</ListItem>
            </FlexContainer>
        </Card>
    );
};

export default SettingsMenu;