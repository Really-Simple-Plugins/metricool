import { Link } from "@tanstack/react-router";
import { Block, BlockHeader, BlockHeaderTitle } from "../components";
import { __ } from "@wordpress/i18n";
import { FlexContainer } from "../components";
import ListItem from "./ListItem.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";

const SettingsMenu = () => {
    const { metricool } = useGlobalContext();
    return (
        <Block className={"md:sticky md:top-[3rem]"}>
            <BlockHeader>
                <BlockHeaderTitle>
                    {__("Settings", "metricool")}
                </BlockHeaderTitle>
            </BlockHeader>
            <FlexContainer direction={"column"} className={"!gap-3"}>
                <Link to={"/settings/account"} className="text-md text-black hover:underline [&.active]:text-primary [&.active]:font-semibold [&.active]:border-none">
                    {__("Account Settings", "metricool")}
                </Link>
                <Link to={"/settings/connections"} className="text-md text-black hover:underline [&.active]:text-primary [&.active]:font-semibold [&.active]:border-none">
                    {__("Connections", "metricool")}
                </Link>
                <ListItem className={"text-md text-black cursor-pointer hover:underline"} icon={"inline-external-link"} iconPosition={"right"} link={`https://app.metricool.com/affiliation/general?blogId=${metricool.blogId}&userId=${metricool.userId}`}>
                    {__("Affiliation Program", "metricool")}
                </ListItem>
                <ListItem className={"text-md text-upsell font-semibold cursor-pointer hover:underline"} icon={"upsell"} iconClass={"rounded-full bg-upsell size-2.5 p-0.5"} iconPosition={"right"} link={`https://app.metricool.com/user-management/users?blogId=${metricool.blogId}&userId=${metricool.userId}`}>
                    {__("User Management", "metricool")}
                </ListItem>
                <ListItem className={"text-md text-upsell font-semibold cursor-pointer hover:underline"} icon={"upsell"} iconClass={"rounded-full bg-upsell size-2.5 p-0.5"} iconPosition={"right"} link={`https://app.metricool.com/my-tasks/open?blogId=${metricool.blogId}&userId=${metricool.userId}`}>
                    {__("My Tasks", "metricool")}
                </ListItem>
            </FlexContainer>
        </Block>
    );
};

export default SettingsMenu;