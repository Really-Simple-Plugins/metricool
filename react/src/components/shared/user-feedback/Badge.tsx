import { Badge as PrimitiveBadge } from "@/components/shared/primitives/badge";
import { cn } from "@/support/functions/utils";
import { type VariantProps } from "class-variance-authority";
import { badgeVariants } from "@/support/helpers/Variants";

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
}: Omit<React.ComponentProps<typeof PrimitiveBadge>, "variant"> & VariantProps<typeof badgeVariants>) => {

    return (
        <PrimitiveBadge
            className={cn(badgeVariants({ variant, className }))}
            {...props}
        >
            {children}
        </PrimitiveBadge>
    );
};

export { Badge };