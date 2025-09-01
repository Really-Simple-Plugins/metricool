import { createLazyFileRoute } from '@tanstack/react-router';
import { DashboardLayout } from "../layouts/DashboardLayout.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { OnboardingLayout } from "../layouts/OnboardingLayout.tsx";
import { useEffect } from "react";

export const Route = createLazyFileRoute('/')({
    component: Index,
});

function Index() {
    const { globalState } = useGlobalContext();

    useEffect(() => {
        console.log(globalState);
    }, [globalState]);

    return (
        <>
            {globalState.metricool.is_onboarding_completed ? (
                <DashboardLayout />
            ) : (
                <OnboardingLayout />
            )}
        </>
    );
}