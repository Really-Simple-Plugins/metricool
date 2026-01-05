import { createLazyFileRoute } from '@tanstack/react-router';
import { DashboardLayout } from "../layouts/DashboardLayout.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { OnboardingLayout } from "../layouts/OnboardingLayout.tsx";

export const Route = createLazyFileRoute('/')({
    component: Index,
});

function Index() {
    const { metricool } = useGlobalContext();

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