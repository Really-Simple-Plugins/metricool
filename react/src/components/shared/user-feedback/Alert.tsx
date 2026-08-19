import { Icon } from "@/components/shared/user-feedback/Icon";
import {
    Alert as PrimitiveAlert,
    AlertDescription as PrimitiveAlertDescription,
    alertVariants as PrimitiveAlertVariants
} from "@/components/shared/primitives/alert";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";

const AlertVariantStyling = {
    success: "bg-rsp-success-light text-rsp-success-dark *:data-[slot=alert-description]:text-rsp-success-dark",
    warning: "bg-rsp-warning-light text-rsp-warning-dark *:data-[slot=alert-description]:text-rsp-warning-dark",
    error: "bg-rsp-error-light text-rsp-error-dark *:data-[slot=alert-description]:text-rsp-error-dark",
    info: "bg-rsp-info-light text-rsp-info-dark *:data-[slot=alert-description]:text-rsp-info-dark",
};

const AlertVariants = cva(
    "gap-2! justify-between w-full rounded-md p-3 text-md text-center font-semibold leading-4",
    {
        variants: {
            variant: AlertVariantStyling,
        },
        defaultVariants: {
            variant: "info",
        },
    }
);

type AlertVariantsProps =
    | VariantProps<typeof PrimitiveAlertVariants>
    | VariantProps<typeof AlertVariants>;

type AlertProps = {
    action?: React.ReactNode,
};

/**
 *
 * @version 1.0.0
 */
const Alert = ({
    action,
    children,
    variant,
    className,
    ...props
}: React.ComponentProps<"div"> & AlertProps & AlertVariantsProps) => {
    return (
        <PrimitiveAlert
            className={cn(
                // @ts-expect-error tsc can't verify type narrowing on variant
                variant ? variant in AlertVariantStyling ? AlertVariants({ variant }) : PrimitiveAlertVariants({ variant }) : "",
                className
            )}
            {...props}
        >
            <div data-slot={"alert-icon"}>
                <Icon icon={"info"}/>
            </div>
            <PrimitiveAlertDescription className={"text-md font-semibold leading-4 text-left"}>
                {children}
                {action && (
                    <div>
                        {action}
                    </div>
                )}
            </PrimitiveAlertDescription>
        </PrimitiveAlert>
    );
};

export { Alert };