import { Suspense } from "react";
import { createRootRoute, Outlet } from '@tanstack/react-router';
import { DashboardLayout } from "../layouts/dashboard-layout.tsx";
import { TanStackRouterDevtools } from "@tanstack/router-devtools";

export const Route = createRootRoute({
    component: () => (
        <DashboardLayout>
            <Outlet/>
            {process.env.NODE_ENV === 'development' && (
                <Suspense>
                    <TanStackRouterDevtools position={'bottom-right'}/>
                </Suspense>
            )}
        </DashboardLayout>
    ),
});