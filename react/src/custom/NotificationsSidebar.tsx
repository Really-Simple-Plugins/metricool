import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";

const NotificationsSidebar = () => {
    return (
        <Card variant={"transparent"}>
            <CardHeader className={"!gap-3"}>
                <CardHeaderTitle>
                    {__("Notifications", "metricool")}
                </CardHeaderTitle>
                <hr/>
            </CardHeader>
            <div className={"text-gray-400 italic"}>{__("You currently have no notifications.", "metricool")}</div>
        </Card>
    );
};

export default NotificationsSidebar;