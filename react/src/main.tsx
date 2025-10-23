import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { RouterProvider, createRouter, createHashHistory } from '@tanstack/react-router';
import { TanStackDevtools } from '@tanstack/react-devtools';
import { ReactQueryDevtoolsPanel } from '@tanstack/react-query-devtools';
import { TanStackRouterDevtoolsPanel } from '@tanstack/react-router-devtools';
import './tailwind.css';

// Import the generated route tree
import { routeTree } from './routeTree.gen';

// Create hashHistory so all routes are relative
// to WordPress's route for the plugin
const hashHistory = createHashHistory();

// Create default queryClient
export const queryClient = new QueryClient();

// Create a new router instance
const router = createRouter({
    routeTree,
    history: hashHistory,
    context: {
        queryClient,
    },
});

// Register the router instance for type safety
declare module '@tanstack/react-router' {
    interface Register {
        router: typeof router;
    }
}

// Wait for DOMContentLoaded to render the app
// to allow WordPress to load properly first
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("metricool_app");
    if (container) {
        createRoot(container).render(
            <StrictMode>
                <QueryClientProvider client={queryClient} >
                    <RouterProvider router={router} />
                        <TanStackDevtools plugins={[
                            {
                                name: 'TanStack Query',
                                render: <ReactQueryDevtoolsPanel />,
                            },
                            {
                                name: 'TanStack Router',
                                render: <TanStackRouterDevtoolsPanel router={router} />,
                            },
                        ]}/>
                </QueryClientProvider>
            </StrictMode>,
        );
    }
});


