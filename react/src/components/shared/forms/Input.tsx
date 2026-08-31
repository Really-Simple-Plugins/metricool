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

const inputClasses = "data-[slot=input]:rounded-base has-[>[data-slot=input-group-control]]:rounded-base data-[slot=input]:mx-0! px-2 shadow-none flex bg-white text-md md:text-md *:data-[slot=input-group-control]:text-md md:*:data-[slot=input-group-control]:text-md font-semibold text-black min-h-10 data-[slot=input]:min-h-10! data-[slot=input]:aria-invalid:focus-visible:ring-rsp-error-dark/20 data-[slot=input]:focus-visible:aria-invalid:ring-3 data-[slot=input]:aria-invalid:border-rsp-error-dark data-[slot=input]:read-only:bg-neutral-100!";

/**
 *
 * @version 1.0.0
 */
const InputGroup = ({ className, children }: React.ComponentProps<"div">) => {
    return (
        <PrimitiveInputGroup
            className={cn(
                "has-disabled:opacity-50 has-[>[data-read-only=true]]:bg-neutral-100",
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
            type={type}
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