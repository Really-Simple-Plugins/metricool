import { Link } from "@tanstack/react-router";
import { Block, BlockHeader, FlexContainer, ListItem } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { cn } from "@/support/functions/utils.ts";

const SettingsMenu = () => {
    const { metricoolDynamicUrl, metricool } = useGlobalContext();

    return (
        <Block className={"md:sticky md:top-[3rem]"}>
            <BlockHeader title={__("Settings", "metricool")}/>
            <FlexContainer direction={"column"} className={"!gap-3"}>
                <Link to={"/settings/account"} className={"text-md text-black hover:underline [&.active]:text-primary [&.active]:font-semibold [&.active]:border-none"}>
                    {__("Account Settings", "metricool")}
                </Link>
                <Link to={"/settings/connections"} className={"text-md text-black hover:underline [&.active]:text-primary [&.active]:font-semibold [&.active]:border-none"}>
                    {__("Connections", "metricool")}
                </Link>
                <ListItem
                    className={"text-md text-black cursor-pointer hover:underline"}
                    link={metricoolDynamicUrl.withPath("affiliation/general")}
                    iconProps={{
                        icon: "inline-external-link",
                        iconPosition: "right",
                    }}
                >
                    {__("Affiliation Program", "metricool")}
                </ListItem>
                <ListItem
                    className={cn("text-md cursor-pointer hover:underline", !metricool.account?.is_premium ? "text-upsell font-semibold" : "text-black")}
                    link={metricoolDynamicUrl.withPath("user-management/users")}
                    {...(!metricool.account?.is_premium ? {
                        iconProps: {
                            icon: "upsell",
                            iconClass: "rounded-full bg-upsell size-2.5 p-0.5 text-black",
                            iconPosition: "right",
                        }
                    } : {
                        iconProps: {
                            icon: "inline-external-link",
                            iconPosition: "right",
                        }
                    })}
                >
                    {__("User Management", "metricool")}
                </ListItem>
                <ListItem
                    className={cn("text-md cursor-pointer hover:underline", !metricool.account?.is_premium ? "text-upsell font-semibold" : "text-black")}
                    link={metricoolDynamicUrl.withPath("my-tasks/open")}
                    {...(!metricool.account?.is_premium ? {
                        iconProps: {
                            icon: "upsell",
                            iconClass: "rounded-full bg-upsell size-2.5 p-0.5 text-black",
                            iconPosition: "right",
                        }
                    } : {
                        iconProps: {
                            icon: "inline-external-link",
                            iconPosition: "right",
                        }
                    })}
                >
                    {__("My Tasks", "metricool")}
                </ListItem>
            </FlexContainer>
        </Block>
    );
};

export { SettingsMenu };