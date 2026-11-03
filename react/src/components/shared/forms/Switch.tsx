import { Switch as PrimitiveSwitch } from "@/components/shared/primitives/switch";
import * as React from "react";
import { cn } from "@/support/functions/utils";

/**
 *
 * @version 1.0.0
 */
const Switch = ({ className, ...props }: React.ComponentProps<typeof PrimitiveSwitch>) => {
    return (
        <PrimitiveSwitch className={cn(className)} {...props} />
    );
};

export { Switch };