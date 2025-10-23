import { FlexContainer } from "../components";
import Header from "../custom/Header.tsx";
import SettingsMenu from "../custom/SettingsMenu.tsx";
import NotificationsSidebar from "../custom/NotificationsSidebar.tsx";

export const SettingsLayout = ({ children }: React.ComponentProps<"div">) => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full"}>
            <Header />
            <FlexContainer direction={"column"} className={"px-4 w-full justify-between md:flex-row items-start"}>
                <SettingsMenu />
                {children}
                <NotificationsSidebar />
            </FlexContainer>
        </FlexContainer>
    );
};