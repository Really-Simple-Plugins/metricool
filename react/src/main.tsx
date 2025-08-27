import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { RouterProvider, createRouter, createHashHistory } from '@tanstack/react-router';
import './tailwind.css';


// Import the generated route tree
import { routeTree } from './routeTree.gen';

const hashHistory = createHashHistory();
// Create a new router instance
const router = createRouter({ routeTree, history: hashHistory });
// Register the router instance for type safety
declare module '@tanstack/react-router' {
    interface Register {
        router: typeof router;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("metricool_app");
    if (container) {
        createRoot(container).render(
            <StrictMode>
                <RouterProvider router={router}/>
            </StrictMode>,
        );
    }
});


