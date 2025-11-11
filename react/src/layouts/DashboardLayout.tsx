import Header from "../custom/Header.tsx";
import { FlexContainer } from "../components";
import Progress from "../custom/Progress.tsx";
import WebsiteAnalytics from "../custom/WebsiteAnalytics.tsx";
import ConnectedAccounts from "../custom/ConnectedAccounts.tsx";
import RelatedPlugins from "../custom/RelatedPlugins.tsx";

export const DashboardLayout = () => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full min-[125rem]:items-center"}>
            <Header/>
            <FlexContainer direction={"column"} className={"px-4 w-full max-w-[125rem]"}>
                <FlexContainer direction={"column"} className={"w-full h-full justify-around xl:flex-row"}>
                    <Progress />
                    <WebsiteAnalytics />
                </FlexContainer>
                <FlexContainer direction={"column"} className={"w-full justify-around sm:flex-row"}>
                    <ConnectedAccounts />
                    <RelatedPlugins />
                </FlexContainer>
            </FlexContainer>
        </FlexContainer>
    );
};