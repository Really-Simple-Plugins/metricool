import { Outlet, createLazyFileRoute } from "@tanstack/react-router";
import { SettingsLayout } from "../../layouts/SettingsLayout.tsx";
import { ToastContainer } from "../../components";

export const Route = createLazyFileRoute('/settings')({
    component: Settings,
});

function Settings() {
    return (
        <SettingsLayout >
            <ToastContainer/>
            <Outlet />
        </SettingsLayout>
    );
}