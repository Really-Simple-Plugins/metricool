import { createLazyFileRoute } from '@tanstack/react-router';
import { DashboardLayout } from "../layouts/DashboardLayout.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { OnboardingLayout } from "../layouts/OnboardingLayout.tsx";
import DOMPurify from "dompurify";

export const Route = createLazyFileRoute('/')({
    component: Index,
});

function Index() {
    const { metricool } = useGlobalContext();

    DOMPurify.addHook("afterSanitizeAttributes", (node) => {
        if (node.hasAttribute("href") && node.getAttribute("href") !== "https://metricool.com/legal-terms/") {
            node.remove();
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