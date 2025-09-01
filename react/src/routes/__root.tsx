import { createRootRoute, Outlet } from '@tanstack/react-router';
import { GlobalContextProvider } from "../context/GlobalContext.tsx";

export const Route = createRootRoute({
    component: () => {
        return (
            <GlobalContextProvider>
                <Outlet/>
            </GlobalContextProvider>
        )
    },
});