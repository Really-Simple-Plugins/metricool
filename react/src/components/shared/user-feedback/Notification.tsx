import { Alert as PrimitiveAlert } from "@/components/shared/primitives/alert";
import { type VariantProps } from "class-variance-authority";
import { SingleAccordion } from "@/components/shared/user-feedback/Accordion";
import { cn } from "@/support/functions/utils";
import { notificationVariants } from "@/support/helpers/Variants";

type NotificationVariantsProps = VariantProps<typeof notificationVariants>;

type NotificationProps = {
    title: string,
}

/**
 * Custom extension of shadcn's {@link PrimitiveAlert} component.
 * Is implemented as a Notice in the NotificationsSidebar.
 *
 * @uses {@link PrimitiveAlert} from primitives
 * @link https://ui.shadcn.com/docs/components/radix/alert
 *
 *
 * Note: original variants from shadcn's {@link PrimitiveAlert} can no longer be
 * passed to this component.
 *
 * @see {@link SingleAccordion} - Implements a single accordion
 *
 * @version 1.0.0
 */
const Notification = ({
    className,
    title,
    variant,
    children,
    ...props
}: NotificationProps & React.ComponentProps<"div"> & NotificationVariantsProps) => {

    return (
        <PrimitiveAlert
            className={cn(notificationVariants({ variant, className }))}
            {...props}
        >
            <SingleAccordion title={title}>
                {children}
            </SingleAccordion>
        </PrimitiveAlert>
    );
};

export { Notification };