import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";
import FormFooter from "./FormFooter.tsx";

const AccountSettings = () => {
    return (
        <div className={"flex flex-col md:min-w-[50%]"}>
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
        </div>
    );
};

export default AccountSettings;