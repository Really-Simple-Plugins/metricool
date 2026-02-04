import { createRootRoute, Outlet } from '@tanstack/react-router';
import { GlobalContextProvider } from "@/context/GlobalContext.tsx";

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
export const Route = createRootRoute({
    head: () => ({
        scripts: [
            {
                src: "https://www.google.com/recaptcha/enterprise.js?render=6LflMV4sAAAAAMyPohHfMRVjZQBcu-YuZz_3nTTK",
            },
        ],
    }),
    component: () => {
        return (
            <GlobalContextProvider>
                <Outlet/>
            </GlobalContextProvider>
        )
    },
});