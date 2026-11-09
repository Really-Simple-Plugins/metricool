import { createLazyFileRoute, Outlet } from "@tanstack/react-router";
import AuthorizedLayout from "@/layouts/AuthorizedLayout.tsx";
import { ToastContainer } from "@/components/shared";

export const Route = createLazyFileRoute("/_authorized")({
    component: RouteComponent,
});

function RouteComponent() {
    return (
        <>
            <AuthorizedLayout>
                <Outlet/>
            </AuthorizedLayout>
            {/* ToastContainer adds a 0px element to the DOM,
                meaning it is taken into account in flex layouts, e.g. a gap
                will be rendered either side of it.
                Added both here and in index.lazy.tsx, because adding it in
                __root.tsx results in a big chunk size.
            */}
            <ToastContainer/>
        </>
    );
}
