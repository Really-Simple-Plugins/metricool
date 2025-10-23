import { Block, BlockHeader, BlockHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import { FlexContainer } from "../components";
import ListItem from "./ListItem.tsx";

const OtherPlugins = () => {
    return (
        <Block variant={"transparent"}>
            <BlockHeader>
                <BlockHeaderTitle>{__("Other Plugins", "metricool")}</BlockHeaderTitle>
            </BlockHeader>
            <FlexContainer direction={"column"} className={"!gap-2"}>
                <ListItem icon={"circle"} iconColor={"rss"} iconPosition={"left"} link={__("Upgrade", "metricool")} className={"font-semibold"}>
                    {__("Really Simple Security Pro", "metricool")}
                </ListItem>
                <ListItem icon={"circle"} iconColor={"simplybook"} iconPosition={"left"} link={__("Install", "metricool")} className={"font-semibold"}>
                    {__("Simplybook: Online booking system", "metricool")}
                </ListItem>
                <ListItem icon={"circle"} iconColor={"complianz"} iconPosition={"left"} link={__("Install", "metricool")} className={"font-semibold"}>
                    {__("Complianz - Terms and Conditions", "metricool")}
                </ListItem>
            </FlexContainer>
        </Block>
    );
};

export default OtherPlugins;