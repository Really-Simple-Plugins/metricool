import { Icon } from "@/components/shared/user-feedback/Icon";
import {
    Alert as PrimitiveAlert,
    AlertDescription as PrimitiveAlertDescription,
} from "@/components/shared/primitives/alert";
import { type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";
import { alertVariants } from "@/support/helpers/Variants";

type AlertVariantsProps = VariantProps<typeof alertVariants>;

type AlertProps = {
    action?: React.ReactNode,
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