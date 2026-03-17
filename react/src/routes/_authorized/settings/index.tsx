import { createFileRoute, redirect } from "@tanstack/react-router";

/**
 * This file and Route exist purely to ensure navigating directly to /settings
 * always redirects the user to /settings/account. /settings is purely a
 * layout route, so it has to have a child route as an outlet to show anything.
 * /settings/account is the default child route.
 */
export const Route = createFileRoute("/_authorized/settings/")({
    beforeLoad: ({ context }) => {
        if (!context.metricool.is_onboarding_completed) {
            throw redirect({ to: "/", replace: true });
        }
        throw redirect({ to: "/settings/account", replace: true });
    },
    component: () => null,
});