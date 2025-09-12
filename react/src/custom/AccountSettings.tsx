import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";

const AccountSettings = () => {
    return (
        <>
            <FlexContainer id={"account-settings"} direction={"column"} className={"md:min-w-[50%]"}>
                <Card className={"h-[400px]"}>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Personal Information", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                </Card>
                <Card className={"h-[400px]"}>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Preferences", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                </Card>
                <Card className={"h-[400px]"}>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Monthly summary", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                </Card>
            </FlexContainer>
        </>
    );
};

export default AccountSettings;