import { Button, Block, BlockHeader, BlockHeaderTitle, BlockDescription } from "../components";
import { __ } from "@wordpress/i18n";
import { FlexContainer } from "../components";
import AccountTile from "./AccountTile.tsx";

const ConnectionsSettings = () => {
    return (
        <div className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Block>
                    <BlockHeader>
                        <BlockHeaderTitle>
                            {__("Connections", "metricool")}
                        </BlockHeaderTitle>
                        <BlockDescription>
                            {__("The accounts that are connected to Metricool", "metricool")}
                        </BlockDescription>
                    </BlockHeader>
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
                </Block>
            </FlexContainer>
        </div>
    );
};

export default ConnectionsSettings;