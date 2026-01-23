import { createLazyFileRoute } from "@tanstack/react-router";
import AccountSettings from "../../custom/AccountSettings.tsx";

export const Route = createLazyFileRoute("/settings/account")({
    component: AccountSettings,
});