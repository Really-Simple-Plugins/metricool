import { Input as PrimitiveInput } from "@/components/shared/primitives/input.tsx";
import { cn } from "@/support/functions/utils.ts";

/**
 *
 * @version 1.0.0
 */
const Input = ({ className, type, children, ...props }: React.ComponentProps<"input">) => {
    return (
        <PrimitiveInput
            className={cn(
                "data-[slot=input]:rounded-xs px-2 shadow-none flex bg-white text-md font-semibold text-black data-[slot=input]:!min-h-9",
                "data-[slot=input]:aria-invalid:focus-visible:ring-rsp-error-dark/20 data-[slot=input]:focus-visible:aria-invalid:ring-3 data-[slot=input]:aria-invalid:border-rsp-error-dark",
                className,
            )}
            type={type}
            {...props}
        >
            {children}
        </PrimitiveInput>
    );
};

export { Input };