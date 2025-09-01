import { createRootRoute, Outlet } from '@tanstack/react-router';
import { DashboardLayout } from "../layouts/dashboard-layout.tsx";

export const Route = createRootRoute({
    component: () => (
        <DashboardLayout>
            <Outlet/>
        </DashboardLayout>
    ),
});