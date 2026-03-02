import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { createHashHistory, createRouter, Navigate, RouterProvider } from "@tanstack/react-router";
import { TanStackDevtools } from "@tanstack/react-devtools";
import { ReactQueryDevtoolsPanel } from "@tanstack/react-query-devtools";
import { TanStackRouterDevtoolsPanel } from "@tanstack/react-router-devtools";
import { GlobalContextProvider, useGlobalContext } from "@/context/GlobalContext.tsx";
import { ErrorBoundary } from "@/components/shared/user-feedback/ErrorBoundary.tsx";
import { ToastContainer } from "@/components/shared/user-feedback/Toast.tsx";
import "./tailwind.css";

// Import the generated route tree
import { routeTree } from "./routeTree.gen";

// Create hashHistory so all routes are relative
// to WordPress's route for the plugin
const hashHistory = createHashHistory();

// Create default queryClient
export const queryClient = new QueryClient();

// @ts-expect-error the metricool variable is globally set in the DashboardController
// but the tsc complains it can't find it
// Saving as a const as we have no access to the context before initializing it,
// but we need it for the ErrorBoundary
const METRICOOL_SUPPORT_TICKET_LINK = window.metricool.values.trusted_urls.new_support_ticket;

// Create a new router instance
// Use hashHistory to make routes relative to the WP route for the plugin
const router = createRouter({
    routeTree,
    defaultNotFoundComponent: () => <Navigate to={"/"} replace={true}/>,
    defaultErrorComponent: ({ error }: { error: Error }) => {
        return (
            <>
                <ErrorBoundary
                    error={error}
                    supportTicketLink={METRICOOL_SUPPORT_TICKET_LINK}
                />
                <ToastContainer/>
            </>)
    },
    history: hashHistory,
    context: {
        metricool: undefined!,
        httpClient: undefined!,
        globalState: undefined!,
        metricoolDynamicUrl: undefined!,
        dispatch: undefined!,
        dashboardSettings: undefined!,
        queryClient,
    },
});

// Creating a separate React component so useGlobalContext can be called as a hook
// and the value can be passed to the RouterProvider so routes have access to the
// context values. InnerApp name taken from Tanstack docks
const InnerApp = () => {
    const globalContext = useGlobalContext();
    return (
        <>
            <RouterProvider router={router} context={globalContext}/>
            <TanStackDevtools
                plugins={[
                    {
                        name: "TanStack Query",
                        render: <ReactQueryDevtoolsPanel/>,
                    },
                    {
                        name: "TanStack Router",
                        render: <TanStackRouterDevtoolsPanel router={router}/>,
                    },
                ]}
            />
        </>
    );
};

// Register the router instance for type safety
declare module "@tanstack/react-router" {
    interface Register {
        router: typeof router;
    }
}

// Event listener to keep track of scroll progress as a CSS variable,
// which can be used in Tailwind classes
window.addEventListener("scroll", () => {
    document.documentElement.style.setProperty("--scroll-progress-in-pixels", `${window.scrollY}px`);
});

// Wait for DOMContentLoaded to render the app
// to allow WordPress to load properly first
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("rsp-app-root");
    if (container) {
        createRoot(container).render(
            <StrictMode>
                <QueryClientProvider client={queryClient}>
                    <GlobalContextProvider>
                        <InnerApp/>
                    </GlobalContextProvider>
                </QueryClientProvider>
            </StrictMode>,
        );
    }
});


