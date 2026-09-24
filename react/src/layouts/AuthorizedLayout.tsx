import { Link, FlexContainer, Header, HeaderTab, Icon } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

const AuthorizedLayout = ({ children }: React.ComponentProps<"div">) => {
    const { metricool, metricoolDynamicUrl } = useGlobalContext();
    return (
        <FlexContainer direction={"column"} className={"h-full w-full min-[125rem]:items-center"}>
            <Header
                className={"px-4"}
                tabs={[
                    (<HeaderTab route={"/"}>
                        {__("Dashboard", "metricool")}
                    </HeaderTab>),
                    (<HeaderTab route={"/settings"}>
                        {__("Settings", "metricool")}
                    </HeaderTab>),
                    <HeaderTab href={metricoolDynamicUrl.withPath("planner/calendar")}>
                        {__("Planner", "metricool")}
                    </HeaderTab>,
                ]}
                actions={[
                    (<Link
                        variant={"black"}
                        href={metricool.metricool_help_url}
                        className={"gap-2! items-center"}
                    >
                        <Icon icon={"faq"} className={"text-white"}/>
                        {__("Help Center", "metricool")}
                    </Link>),
                    (!metricool.account?.is_premium && (
                        <Link
                            variant={"primary-gradient"}
                            href={metricoolDynamicUrl.withPath("user-settings/plan")}
                            className={"gap-2! items-center"}
                        >
                            <Icon icon={"sparkle"} className={"text-secondary"}/>
                            {__("Upgrade", "metricool")}
                        </Link>
                        )
                    )
                ]}
                logo={(
                    <div className={"flex min-w-17.5 min-h-17.5 items-center justify-center"}>
                        <img
                            src={`${metricool.assets_url}img/mc-logo.svg`}
                            alt={__("Metricool logo", "metricool")}
                        />
                    </div>
                )}
            />
            {children}
        </FlexContainer>
    );
};

export default AuthorizedLayout;