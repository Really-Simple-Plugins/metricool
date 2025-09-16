import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";

const ConnectionsSettings = () => {
    return (
        <div className={"flex flex-col md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Card className={"min-h-[400px]"}>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Connections", "metricool")}
                        </CardHeaderTitle>
                    </CardHeader>
                </Card>
            </FlexContainer>
        </div>
    );
};

export default ConnectionsSettings;