import { createLazyFileRoute } from "@tanstack/react-router";
import { ConnectionsSettings } from "@/components/custom";

export const Route = createLazyFileRoute("/_authorized/settings/connections")({
    component: ConnectionsSettings,
});