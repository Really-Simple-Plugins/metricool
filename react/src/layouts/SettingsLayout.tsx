import { FlexContainer } from "@/components";
import Header from "@/custom/Header.tsx";
import SettingsMenu from "@/custom/SettingsMenu.tsx";
import NotificationsSidebar from "@/custom/NotificationsSidebar.tsx";

/**
 * The Settings Layout.
 *
 * Conditionally rendered in `lazy.index.ts`, based on the user's
 * subscriptions data.
 *
 * Routing setup in `routes/settings/route.lazy.tsx`, where it is given an
 * `<Outlet/>`, meaning it receives either {@link AccountSettings} or
 * {@link ConnectionsSettings} as a child, rendered here through the `children`
 * prop.
 *
 * Contains the {@link SettingsMenu} component to allow a user to navigate to
 * different settings pages.
 *
 * Contains the {@link NotificationsSidebar} component to show notifications.
 *
 *
 */
export const SettingsLayout = ({ children }: React.ComponentProps<"div">) => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full min-[125rem]:items-center"}>
            <Header/>
            <FlexContainer direction={"column"} className={"px-4 w-full items-start md:flex-row max-w-[125rem]"}>
                <SettingsMenu/>
                {children}
                <NotificationsSidebar/>
            </FlexContainer>
        </FlexContainer>
    );
};