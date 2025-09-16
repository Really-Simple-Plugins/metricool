import FlexContainer from "../custom/FlexContainer.tsx";
import Header from "../custom/Header.tsx";
import AccountSettings from "../custom/AccountSettings.tsx";
import SettingsMenu from "../custom/SettingsMenu.tsx";
import NotificationsSidebar from "../custom/NotificationsSidebar.tsx";

export const SettingsLayout = () => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full"}>
            <Header />
            <FlexContainer direction={"column"} className={"px-4 w-full justify-between md:flex-row items-start"}>
                <SettingsMenu />
                <AccountSettings />
                <NotificationsSidebar />
            </FlexContainer>
        </FlexContainer>
    );
};