import { Badge } from "@/components/shared/user-feedback/Badge";
import { cn } from "@/support/functions/utils";
import { type TaskProps } from "@/components/shared/dashboard/Task";

const TaskStatusBadgeVariantStyling = {
    "open": "bg-rsp-task-open text-black",
    "special-feature": "bg-rsp-task-special-feature text-white",
    "premium": "bg-rsp-task-premium text-white",
    "warning": "bg-rsp-task-warning text-white",
    "urgent": "bg-rsp-task-urgent text-white",
    "completed": "bg-rsp-task-completed text-white",
    "dismissed": "bg-rsp-task-dismissed text-gray-800",
    "hidden": "hidden",
};

type TaskStatusBadgeProps = {
    status: TaskProps["status"],
    isPremium: boolean,
    isSpecialFeature: boolean,
}

const getBadgeVariant = ({
    status,
    isPremium,
    isSpecialFeature,
}: TaskStatusBadgeProps): keyof typeof TaskStatusBadgeVariantStyling => {
    if (status === "completed") {
        return "completed";
    }
    if (status === "dismissed") {
        return "dismissed";
    }
    if (isPremium) {
        return "premium";
    }
    if (isSpecialFeature) {
        return "special-feature";
    }
    if (status === "hidden") {
        return "hidden";
    }
    return status;
};

/**
 * Extension of our {@link Badge} component for specific implemented in the Task
 * component to show the status.
 *
 * In addition to the named props, it accepts all other possible props
 * for a `span` element, passed to Badge through a `...props` rest object
 * @uses {@link Badge}
 *
 * @version 1.0.0
 */
const TaskStatusBadge = ({
    className,
    children,
    status,
    isPremium,
    isSpecialFeature,
    ...props
}: React.ComponentProps<"span"> & TaskStatusBadgeProps) => {
    return (
        <Badge
            className={cn(
                "rounded-full p-0 min-w-4 max-h-4 leading-none xl:p-2 xl:min-w-27 xl:rounded-[6px] xl:justify-center xl:h-7 xl:max-h-7",
                TaskStatusBadgeVariantStyling[getBadgeVariant({ status, isPremium, isSpecialFeature })],
                className,
            )}
            {...props}
        >
            <span className={"hidden xl:block text-sm font-semibold leading-3"}>{children}</span>
        </Badge>
    );
};

export { TaskStatusBadge };