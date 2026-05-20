import { Input as PrimitiveInput } from "@/components/shared/primitives/input";
import { InputGroup as PrimitiveInputGroup } from "@/components/shared/primitives/input-group";
import { cn } from "@/support/functions/utils";
import * as React from "react";

const inputClasses = "data-[slot=input]:rounded-xs has-[>[data-slot=input-group-control]]:rounded-xs px-2 shadow-none flex bg-white text-md md:text-md *:data-[slot=input-group-control]:text-md md:*:data-[slot=input-group-control]:text-md font-semibold text-black data-[slot=input]:!min-h-10 data-[slot=input]:aria-invalid:focus-visible:ring-rsp-error-dark/20 data-[slot=input]:focus-visible:aria-invalid:ring-3 data-[slot=input]:aria-invalid:border-rsp-error-dark";

/**
 *
 * @version 1.0.0
 */
const InputGroup = ({ className, children }: React.ComponentProps<"div">) => {
    return (
        <PrimitiveInputGroup className={cn(
            inputClasses,
            className,
        )}>
            {children}
        </PrimitiveInputGroup>
    );
};

/**
 *
 * @version 1.0.0
 */
const Input = ({ className, type, children, ...props }: React.ComponentProps<"input">) => {
    return (
        <PrimitiveInput
            className={cn(
                inputClasses,
                className,
            )}
            type={type}
            {...props}
        >
            {children}
        </PrimitiveInput>
    );
};

export { Input, InputGroup };