import { createRootRouteWithContext, Outlet } from '@tanstack/react-router';
import { type GlobalContext } from "@/context/GlobalContext.tsx";
import type { QueryClient } from "@tanstack/react-query";

// @ts-expect-error window.metricool is globally defined
const GOOGLE_RECAPTCHA_URL = window.metricool.values.google_recaptcha_url;

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
                src: GOOGLE_RECAPTCHA_URL,
            },
        ],
    }),
    component: () => {
        return (
            <Outlet/>
        )
    },
});