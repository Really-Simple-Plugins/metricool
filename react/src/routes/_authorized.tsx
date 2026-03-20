import { createFileRoute, redirect } from "@tanstack/react-router";

/**
 * This file and Route exist purely to ensure all routes inside the
 * `/_authorized` directory (that use `/_authorized` in their route), are only
 * accessible if `context.metricool.is_onboarding_completed` is true, otherwise
 * it redirects users to `/`, where it determines what to show.
 * Name of file and route have to match directory exactly.
 */
export const Route = createFileRoute("/_authorized")({
    beforeLoad: ({ context }) => {
        if (!context.metricool.onboarding.completed) {
            throw redirect({ to: "/", replace: true });
        }
    }
});