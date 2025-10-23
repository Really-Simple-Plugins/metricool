import { Block, BlockHeader, BlockHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";

const NotificationsSidebar = () => {
    return (
        <Block variant={"transparent"}>
            <BlockHeader className={"!gap-3"}>
                <BlockHeaderTitle>
                    {__("Notifications", "metricool")}
                </BlockHeaderTitle>
                <hr/>
            </BlockHeader>
            <div className={"text-gray-400 italic"}>{__("You currently have no notifications.", "metricool")}</div>
        </Block>
    );
};

export default NotificationsSidebar;