import { Badge as PrimitiveBadge } from "@/components/shared/primitives/badge";
import { cn } from "@/support/functions/utils";
import { cva, type VariantProps } from "class-variance-authority";

const BadgeVariantStyling = {
    basis: "rounded-full text-[9px] font-semibold",
    variant: {
        primary: "bg-primary-light text-primary",
        gradient: "bg-(image:--gradient-brand) border-transparent bg-origin-border text-white",
    },
    size: {
        default: "h-5 px-2 py-1",
    },
};

const BadgeVariants = cva(
    BadgeVariantStyling.basis, {
    variants: {
        variant: BadgeVariantStyling.variant,
        size: BadgeVariantStyling.size,
    },
    defaultVariants: {
        variant: "primary",
        size: "default",
    }
});

/**
 * Custom extension of shadcn's {@link PrimitiveBadge} component.
 * Is implemented primarily in the Task component to show the status.
 *
 * In addition to the named props, it accepts all other possible props
 * for a `span` element, passed to Badge through a `...props` rest object
 * @uses {@link PrimitiveBadge} from primitives
 *
 * Note: original variants from shadcn's {@link PrimitiveBadge} can no longer be
 * passed to this component.
 *
 * @version 1.0.0
 */
const Badge = ({
    className,
    children,
    variant,
    ...props
}: Omit<React.ComponentProps<typeof PrimitiveBadge>, "variant"> & VariantProps<typeof BadgeVariants>) => {
    return (
        <PrimitiveBadge
            className={cn(
                BadgeVariants({ variant }),
                className
            )}
            {...props}
        >
            {children}
        </PrimitiveBadge>
    );
};

export { Badge, BadgeVariantStyling };