import Header from "../custom/Header.tsx";
import FlexContainer from "../custom/FlexContainer.tsx";
import Progress from "../custom/Progress.tsx";
import WebsiteAnalytics from "../custom/WebsiteAnalytics.tsx";
import ConnectedAccounts from "../custom/ConnectedAccounts.tsx";
import OtherPlugins from "../custom/OtherPlugins.tsx";

export const DashboardLayout = () => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full"}>
            <Header/>
            <FlexContainer direction={"column"} className={"px-4 w-full"}>
                <FlexContainer direction={"column"} className={"w-full justify-around md:flex-row"}>
                    <Progress />
                    <WebsiteAnalytics />
                </FlexContainer>
                <FlexContainer direction={"column"} className={"w-full justify-around sm:flex-row"}>
                    <ConnectedAccounts />
                    <OtherPlugins />
                </FlexContainer>
            </FlexContainer>
        </FlexContainer>
    );
};