import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import FlexContainer from "./FlexContainer.tsx";
import ListItem from "./ListItem.tsx";

const OtherPlugins = () => {
    return (
        <Card variant={"transparent"}>
            <CardHeader>
                <CardHeaderTitle>{__("Other Plugins", "metricool")}</CardHeaderTitle>
            </CardHeader>
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
        </Card>
    );
};

export default OtherPlugins;