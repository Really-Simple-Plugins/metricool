import { cn } from "@/support/functions/utils";

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
                {
                    "flex-row": (direction === "row"),
                    "flex-col": (direction === "column"),
                    "flex-row-reverse": (direction === "row-reverse"),
                    "flex-col-reverse": (direction === "column-reverse"),
                }
            )}
            {...props}
        >
            {children}
        </div>
    );
};

export { FlexContainer };