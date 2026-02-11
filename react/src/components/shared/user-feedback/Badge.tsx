import { Badge as PrimitiveBadge, badgeVariants as PrimitiveBadgeVariants } from "@/components/shared/primitives/badge.tsx";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/functions/utils.ts";

const BadgeVariantStyling = {
    "open": "bg-rsp-task-open text-black",
    "special-feature": "bg-rsp-task-special-feature text-white",
    "premium": "bg-rsp-task-premium text-white",
    "warning": "bg-rsp-task-warning text-black",
    "urgent": "bg-rsp-task-urgent text-white",
    "completed": "bg-rsp-task-completed text-white",
    "dismissed": "bg-gray-100 text-gray-800",
};

const BadgeVariants = cva(
    "min-w-27 py-1 min-h-[28px] text-sm font-semibold",
    {
        variants: {
            variant: BadgeVariantStyling
        },
        defaultVariants: {
            variant: "open",
        },
    }
);

export type BadgeVariantsProps =
    | VariantProps<typeof PrimitiveBadgeVariants>
    | VariantProps<typeof BadgeVariants>;

/**
 * Custom extension of shadcn's {@link PrimitiveBadge} component.
 * Is implemented primarily in the Task component to show the status.
 *
 * In addition to the named props, it accepts all other possible props
 * for a `span` element, passed to Badge through a `...props` rest object
 * @uses {@link PrimitiveBadge} from primitives
 *
 * @version 1.0.0
 */
const Badge = ({
    className,
    children,
    variant,
    ...props
}: React.ComponentProps<"span"> & BadgeVariantsProps) => {
    return (
        <PrimitiveBadge
            // @ts-expect-error tsc can't verify type narrowing on variant
            className={cn(variant ? variant in BadgeVariantStyling ? BadgeVariants({ variant }) : PrimitiveBadgeVariants({ variant }) : "",
                className,
            )}
            {...props}
        >
            {children}
        </PrimitiveBadge>
    );
};

export { Badge };