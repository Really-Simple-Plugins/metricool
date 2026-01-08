import { createLazyFileRoute } from "@tanstack/react-router";
import { DashboardLayout } from "../layouts/DashboardLayout.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { OnboardingLayout } from "../layouts/OnboardingLayout.tsx";
import DOMPurify from "dompurify";

export const Route = createLazyFileRoute("/")({
    component: Index,
});

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