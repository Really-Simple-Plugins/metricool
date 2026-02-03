import { createLazyFileRoute } from "@tanstack/react-router";
import { AccountSettings } from "@/components/custom";

export const Route = createLazyFileRoute("/settings/account")({
    component: AccountSettings,
});