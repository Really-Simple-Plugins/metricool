import { createLazyFileRoute, Outlet } from "@tanstack/react-router";
import AuthorizedLayout from "@/layouts/AuthorizedLayout.tsx";

export const Route = createLazyFileRoute("/_authorized")({
    component: RouteComponent,
});

function RouteComponent() {
    return (
        <AuthorizedLayout>
            <Outlet/>
        </AuthorizedLayout>
    );
}
