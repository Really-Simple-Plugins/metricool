import { createLazyFileRoute } from "@tanstack/react-router";
import { ConnectionsSettings } from "@/components/custom";

export const Route = createLazyFileRoute("/settings/connections")({
    component: ConnectionsSettings,
});