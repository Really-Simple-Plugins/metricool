import { Button, FlexContainer, Header, HeaderTab, Icon } from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";

const AuthorizedLayout = ({ children }: React.ComponentProps<"div">) => {
    const { metricool, metricoolDynamicUrl } = useGlobalContext();
    return (
        <FlexContainer direction={"column"} className={"h-full w-full min-[125rem]:items-center"}>
            <Header
                className={"px-4"}
                tabs={[
                    (<HeaderTab link={"/"}>
                        {__("Dashboard", "metricool")}
                    </HeaderTab>),
                    (<HeaderTab link={"/settings"}>
                        {__("Settings", "metricool")}
                    </HeaderTab>),
                    <HeaderTab link={metricoolDynamicUrl.withPath("planner/calendar")} external={true}>
                        {__("Planner", "metricool")}
                    </HeaderTab>
                ]}
                actions={[
                    (<Button
                        variant={"black"}
                        link={metricool.metricool_help_url}
                    >
                        <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                            <Icon icon={"faq"} className={"text-white"}/>
                            {__("Help Center", "metricool")}
                        </FlexContainer>
                    </Button>),
                    (!metricool.account.is_premium && (
                        <Button
                            variant={"primary-gradient"}
                            link={metricoolDynamicUrl.withPath("user-settings/plan")}
                        >
                            <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
                                <Icon icon={"sparkle"} className={"text-secondary"}/>
                                {__("Upgrade", "metricool")}
                            </FlexContainer>
                        </Button>
                        )
                    )
                ]}
                logo={(
                    <div className={"flex min-w-[4.375rem] min-h-[4.375rem] items-center justify-center"}>
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