import { Label as PrimitiveLabel } from "@/components/shared/primitives/label.tsx";
import { cn } from "@/functions/utils.ts";

/**
 *
 * @version 1.0.0
 */
const Label = ({ children, className, ...props }: React.ComponentProps<"label">) => {
    return (
        <PrimitiveLabel className={cn("font-semibold text-md text-black", className)} {...props}>
            {children}
        </PrimitiveLabel>
    );
};

export { Label };