import { Alert as PrimitiveAlert, alertVariants as PrimitiveAlertVariants } from "@/components/shared/primitives/alert.tsx";
import { cva, type VariantProps } from "class-variance-authority";
import { SingleAccordion } from "@/components/shared/user-feedback/Accordion.tsx";
import { cn } from "@/support/functions/utils.ts";

const NotificationVariantStyling = {
    "info": "border-rsp-info-light bg-rsp-info-light",
    "warning": "border-rsp-error-light bg-rsp-error-light",
};

const NotificationVariants = cva(
    "grid-cols-1 p-4 shadow-lg",
    {
        variants: {
            variant: NotificationVariantStyling,
        },
        defaultVariants: {
            variant: "info",
        },
    }
);

type NotificationVariantsProps =
    | VariantProps<typeof PrimitiveAlertVariants>
    | VariantProps<typeof NotificationVariants>;

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
            // @ts-expect-error tsc can't verify type narrowing on variant
            className={cn(variant ? variant in NotificationVariantStyling ? NotificationVariants({ variant }) : PrimitiveAlertVariants({ variant }) : "",
                className,
            )}
            {...props}
        >
            <SingleAccordion title={title}>
                {children}
            </SingleAccordion>
        </PrimitiveAlert>
    );
};

export { Notification };