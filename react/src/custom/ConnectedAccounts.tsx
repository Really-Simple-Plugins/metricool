import { Button, Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";
import AccountTile from "./AccountTile.tsx";

const ConnectedAccounts = () => {
    return (
        <Card>
            <CardHeader>
                <CardHeaderTitle>{__("Connected Accounts", "metricool")}</CardHeaderTitle>
            </CardHeader>
            <FlexContainer direction={"column"} className={"md:flex-row"}>
                <AccountTile connected={true} accountType={"domain"} accountName={"yourwebsite.com"}/>
                <AccountTile connected={false} accountType={"twitter"}/>
            </FlexContainer>
            <FlexContainer direction={"column"} className={"md:flex-row"}>
                <AccountTile connected={true} accountType={"youtube"} accountName={"YourChannelName"}/>
                <AccountTile connected={false} accountType={"linkedIn"}/>
            </FlexContainer>
            <Button variant={"primary-gradient-ghost"} icon={"external-link"} iconPosition={"right"} iconClass={"svg-gradient"} className={"size-fit"}>
                {__("Connected Accounts", "metricool")}
            </Button>
        </Card>
    );
};

export default ConnectedAccounts;