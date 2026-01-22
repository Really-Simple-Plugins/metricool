import { FlexContainer, ToastContainer } from "../components";
import Header from "../custom/Header.tsx";
import SettingsMenu from "../custom/SettingsMenu.tsx";
import NotificationsSidebar from "../custom/NotificationsSidebar.tsx";

export const SettingsLayout = ({ children }: React.ComponentProps<"div">) => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full min-[125rem]:items-center"}>
            <Header/>
            <FlexContainer direction={"column"} className={"px-4 w-full items-start md:flex-row max-w-[125rem]"}>
                <SettingsMenu/>
                {children}
                <NotificationsSidebar/>
            </FlexContainer>
            <ToastContainer/>
        </FlexContainer>
    );
};