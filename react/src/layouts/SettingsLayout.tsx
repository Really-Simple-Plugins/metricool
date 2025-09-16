import FlexContainer from "../custom/FlexContainer.tsx";
import Header from "../custom/Header.tsx";
import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import AccountSettings from "../custom/AccountSettings.tsx";
import SettingsMenu from "../custom/SettingsMenu.tsx";

export const SettingsLayout = () => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full"}>
            <Header />
            <FlexContainer direction={"column"} className={"px-4 w-full justify-between md:flex-row items-start"}>
                <SettingsMenu />
                <AccountSettings />
                <Card variant={"transparent"}>
                    <CardHeader className={"!gap-3"}>
                        <CardHeaderTitle>
                            {__("Notifications", "metricool")}
                        </CardHeaderTitle>
                        <hr/>
                    </CardHeader>
                    <div className={"text-gray-400 italic"}>{__("You currently have no notifications.", "metricool")}</div>
                </Card>
            </FlexContainer>
        </FlexContainer>
    );
};