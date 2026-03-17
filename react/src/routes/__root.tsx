import { createRootRouteWithContext, Outlet } from '@tanstack/react-router';
import { type GlobalContext } from "@/context/GlobalContext.tsx";
import type { QueryClient } from "@tanstack/react-query";

/**
 * Tanstack Router's entry into the entire app, from which it generates
 * `routeTree.gen.ts`.
 *
 * Used to initiate our custom context provider(s) so they are available to the
 * entire app. The app is rendered as an `<Outlet/>` here, making
 * {@link Index} a child of this root component. This hierarchy is crucial
 * as {@link Index} needs to immediately access the context data.
 *
 */
export const Route = createRootRouteWithContext<GlobalContext & { queryClient: QueryClient }>()({
    head: () => ({
        scripts: [
            {
                src: "https://www.google.com/recaptcha/enterprise.js?render=6LflMV4sAAAAAMyPohHfMRVjZQBcu-YuZz_3nTTK",
            },
        ],
    }),
    component: () => {
        return (
            <Outlet/>
        )
    },
});