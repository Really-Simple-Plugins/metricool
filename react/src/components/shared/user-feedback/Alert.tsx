import { Icon } from "@/components/shared/user-feedback/Icon";
import {
    Alert as PrimitiveAlert,
    AlertDescription as PrimitiveAlertDescription,
    AlertTitle as PrimitiveAlertTitle,
} from "@/components/shared/primitives/alert";
import { type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";
import { alertVariants } from "@/support/helpers/Variants";

type AlertVariantsProps = VariantProps<typeof alertVariants>;

type AlertProps = {
    action?: React.ReactNode,
    title?: string,
};

/**
 *
 *
 * Note: original variants from shadcn's {@link PrimitiveAlert} can no longer be
 * passed to this component.
 *
 * @version 1.0.0
 */
const Alert = ({
    action,
    children,
    variant,
    className,
    title,
    ...props
}: React.ComponentProps<"div"> & AlertProps & AlertVariantsProps) => {

    return (
        <PrimitiveAlert
            className={cn(alertVariants({ variant, className }))}
            {...props}
        >
            <div data-slot={"alert-icon"}>
                <Icon icon={"info"}/>
            </div>
            {title && (
                <PrimitiveAlertTitle className={"text-left"}>
                    {title}
                </PrimitiveAlertTitle>
            )}
            <PrimitiveAlertDescription
                className={cn(
                    "text-md leading-4 text-left",
                    {
                        "font-semibold": !title,
                        "font-normal": title,
                    }
                )}
            >
                {children}
                {action && (
                    <div className={"font-semibold"}>
                        {action}
                    </div>
                )}
            </PrimitiveAlertDescription>
        </PrimitiveAlert>
    );
};

export { Alert };