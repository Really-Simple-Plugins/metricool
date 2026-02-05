import { createLazyFileRoute } from "@tanstack/react-router";
import { AccountSettings } from "@/components/custom/settings/AccountSettings.tsx";

export const Route = createLazyFileRoute("/settings/account")({
    component: AccountSettings,
});