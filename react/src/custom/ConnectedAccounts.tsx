import { Button, Block, BlockHeader, BlockHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import { FlexContainer } from "../components";
import AccountTile from "./AccountTile.tsx";

const ConnectedAccounts = () => {
    return (
        <Block>
            <BlockHeader>
                <BlockHeaderTitle>{__("Connected Accounts", "metricool")}</BlockHeaderTitle>
            </BlockHeader>
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
        </Block>
    );
};

export default ConnectedAccounts;