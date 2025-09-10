import FlexContainer from "../custom/FlexContainer.tsx";
import Header from "../custom/Header.tsx";
import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";

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