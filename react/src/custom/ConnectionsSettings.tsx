import { Button, Card, CardHeader, CardHeaderTitle, CardDescription } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";
import AccountTile from "./AccountTile.tsx";

const ConnectionsSettings = () => {
    return (
        <div className={"flex flex-col md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Card>
                    <CardHeader>
                        <CardHeaderTitle>
                            {__("Connections", "metricool")}
                        </CardHeaderTitle>
                        <CardDescription>
                            {__("The accounts that are connected to Metricool", "metricool")}
                        </CardDescription>
                    </CardHeader>
                    <FlexContainer direction={"column"}>
                        <AccountTile connected={true} accountType={"domain"} accountName={"yourwebsite.com"}/>
                        <AccountTile connected={true} accountType={"youtube"} accountName={"YourChannelName"}/>
                        <AccountTile connected={false} accountType={"twitter"}/>
                        <AccountTile connected={false} accountType={"linkedIn"}/>
                    </FlexContainer>
                    <FlexContainer direction={"row"} className={"justify-end"}>
                        <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"} className={"size-fit"}>
                            {__("Connected Accounts", "metricool")}
                        </Button>
                    </FlexContainer>
                </Card>
            </FlexContainer>
        </div>
    );
};

export default ConnectionsSettings;