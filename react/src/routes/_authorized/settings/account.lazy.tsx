import { createLazyFileRoute } from "@tanstack/react-router";
import { AccountSettings } from "@/components/custom/settings/AccountSettings.tsx";

export const Route = createLazyFileRoute("/_authorized/settings/account")({
    component: AccountSettings,
});