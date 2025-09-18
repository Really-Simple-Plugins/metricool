import { Outlet, createLazyFileRoute } from "@tanstack/react-router";
import { SettingsLayout } from "../../layouts/SettingsLayout.tsx";

export const Route = createLazyFileRoute('/settings')({
    component: Settings,
});

function Settings() {
    return (
        <SettingsLayout >
            <Outlet />
        </SettingsLayout>
    );
}