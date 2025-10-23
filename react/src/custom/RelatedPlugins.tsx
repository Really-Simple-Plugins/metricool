import { Block, BlockHeader, BlockHeaderTitle, FlexContainer } from "../components";
import { __ } from "@wordpress/i18n";
import ListItem from "./ListItem.tsx";

const RelatedPlugins = () => {
    return (
        <Block variant={"transparent"}>
            <BlockHeader>
                <BlockHeaderTitle>{__("Related Plugins", "metricool")}</BlockHeaderTitle>
            </BlockHeader>
            <FlexContainer direction={"column"} className={"!gap-2"}>
                <ListItem icon={"circle"} iconColor={"rss"} iconPosition={"left"} action={__("Upgrade", "metricool")} className={"font-semibold"}>
                    {__("Really Simple Security Pro", "metricool")}
                </ListItem>
                <ListItem icon={"circle"} iconColor={"simplybook"} iconPosition={"left"} action={__("Install", "metricool")} className={"font-semibold"}>
                    {__("Simplybook: Online booking system", "metricool")}
                </ListItem>
                <ListItem icon={"circle"} iconColor={"complianz"} iconPosition={"left"} action={__("Install", "metricool")} className={"font-semibold"}>
                    {__("Complianz - Terms and Conditions", "metricool")}
                </ListItem>
            </FlexContainer>
        </Block>
    );
};

export default RelatedPlugins;