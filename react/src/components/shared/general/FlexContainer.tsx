import { cn } from "@/support/functions/utils.ts";

/**
 *
 * @version 1.0.0
 */
const FlexContainer = ({
    className,
    children,
    direction,
    ...props
}: React.ComponentProps<"div"> & { direction: "row" | "column" | "row-reverse" | "column-reverse" }) => {
    return (
        <div
            className={cn(
                "flex gap-4",
                className,
                direction === "row" && "flex-row",
                direction === "column" && "flex-col",
                direction === "row-reverse" && "flex-row-reverse",
                direction === "column-reverse" && "flex-col-reverse",
            )}
            {...props}
        >
            {children}
        </div>
    );
};

export { FlexContainer };