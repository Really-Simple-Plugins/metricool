import { createRootRoute, Outlet } from '@tanstack/react-router';
import { DashboardLayout } from "../layouts/DashboardLayout.tsx";

export const Route = createRootRoute({
    component: () => (
        <DashboardLayout>
            <Outlet/>
        </DashboardLayout>
    ),
});