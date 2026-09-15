import { Input as PrimitiveInput } from "@/components/shared/primitives/input";
import {
    InputGroup as PrimitiveInputGroup,
    InputGroupAddon as PrimitiveInputGroupAddon,
    InputGroupButton as PrimitiveInputGroupButton,
    InputGroupInput as PrimitiveInputGroupInput,
    InputGroupText as PrimitiveInputGroupText,
} from "@/components/shared/primitives/input-group";
import { cn } from "@/support/functions/utils";
import * as React from "react";

const inputClasses = "mx-0! px-2 flex text-md md:text-md *:data-[slot=input-group-control]:text-md md:*:data-[slot=input-group-control]:text-md font-semibold min-h-10 min-h-10! aria-invalid:focus-visible:ring-rsp-error-dark/20 focus-visible:aria-invalid:ring-3 aria-invalid:border-rsp-error-dark";

/**
 *
 * @version 1.0.0
 */
const InputGroup = ({ className, children }: React.ComponentProps<"div">) => {
    return (
        <PrimitiveInputGroup
            className={cn(
                "has-disabled:opacity-50",
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
const InputGroupInput = ({ className, ...props }: React.ComponentProps<"input">) => {
    return (
        <PrimitiveInputGroupInput
            className={cn("pl-0 disabled:opacity-100! placeholder:font-normal placeholder:text-md", className)}
            {...props}
        >

        </PrimitiveInputGroupInput>
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
                "placeholder:font-normal placeholder:text-md",
                inputClasses,
                className,
            )}
            type={type ?? "text"}
            {...props}
        >
            {children}
        </PrimitiveInput>
    );
};

export {
    Input,
    InputGroup,
    InputGroupInput,
    PrimitiveInputGroupText as InputGroupText,
    PrimitiveInputGroupButton as InputGroupButton,
    PrimitiveInputGroupAddon as InputGroupAddon
};