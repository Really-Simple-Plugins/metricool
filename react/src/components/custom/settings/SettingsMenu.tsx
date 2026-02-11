import { Link } from "@tanstack/react-router";
import { Block, BlockHeader, FlexContainer } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { ListItem } from "@/components/custom/general/ListItem.tsx";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

const SettingsMenu = () => {
    const { metricoolDynamicUrl } = useGlobalContext();
    return (
        <Block className={"md:sticky md:top-[3rem]"}>
            <BlockHeader title={__("Settings", "metricool")}/>
            <FlexContainer direction={"column"} className={"!gap-3"}>
                <Link to={"/settings/account"} className="text-md text-black hover:underline [&.active]:text-primary [&.active]:font-semibold [&.active]:border-none">
                    {__("Account Settings", "metricool")}
                </Link>
                <Link to={"/settings/connections"} className="text-md text-black hover:underline [&.active]:text-primary [&.active]:font-semibold [&.active]:border-none">
                    {__("Connections", "metricool")}
                </Link>
                <ListItem className={"text-md text-black cursor-pointer hover:underline"} icon={"inline-external-link"} iconPosition={"right"} link={metricoolDynamicUrl.withPath("affiliation/general")}>
                    {__("Affiliation Program", "metricool")}
                </ListItem>
                <ListItem className={"text-md text-upsell font-semibold cursor-pointer hover:underline"} icon={"upsell"} iconClass={"rounded-full bg-upsell size-2.5 p-0.5"} iconPosition={"right"} link={metricoolDynamicUrl.withPath("user-management/users")}>
                    {__("User Management", "metricool")}
                </ListItem>
                <ListItem className={"text-md text-upsell font-semibold cursor-pointer hover:underline"} icon={"upsell"} iconClass={"rounded-full bg-upsell size-2.5 p-0.5"} iconPosition={"right"} link={metricoolDynamicUrl.withPath("my-tasks/open")}>
                    {__("My Tasks", "metricool")}
                </ListItem>
            </FlexContainer>
        </Block>
    );
};

export { SettingsMenu };