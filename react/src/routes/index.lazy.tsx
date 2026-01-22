import { createLazyFileRoute } from "@tanstack/react-router";
import { DashboardLayout } from "../layouts/DashboardLayout.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { OnboardingLayout } from "../layouts/OnboardingLayout.tsx";
import DOMPurify from "dompurify";

export const Route = createLazyFileRoute("/")({
    component: Index,
});

/**
 * Defines what to render on the app's root route (`/`).
 *
 * Conditionally renders either the {@link DashboardLayout} or
 * {@link SettingsLayout} based on the user's subscriptions data.
 *
 * Utilises lazy file routes for code splitting and ensuring the app loads
 * properly withing the WP ecosystem.
 *
 * Sets up a custom {@link DOMPurify} hook which removes elements with `href`
 * attributes that contain links not present in our list of `trusted_urls`,
 * which is active for the entire app.
 *
 */
function Index() {
    const { metricool } = useGlobalContext();

    DOMPurify.addHook("afterSanitizeAttributes", (node) => {
        // This first check is to ensure the metricool object has been populated
        // with the data from the backend properly.
        // todo: remove or simplify check once the context has been set up better
        const listOfAcceptedLinks = Object.values(metricool.trusted_urls);
        if (!listOfAcceptedLinks.some((link) => link === "")) {
            const href = node.getAttribute("href");
            if (href && !listOfAcceptedLinks.includes(href)) {
                node.remove();
            }
        }
    });

    return (
        <>
            {metricool.is_onboarding_completed ? (
                <DashboardLayout />
            ) : (
                <OnboardingLayout />
            )}
        </>
    );
}