import { createLazyFileRoute } from "@tanstack/react-router";
import ConnectionsSettings from "@/custom/settings/ConnectionsSettings.tsx";

export const Route = createLazyFileRoute("/settings/connections")({
    component: ConnectionsSettings,
});