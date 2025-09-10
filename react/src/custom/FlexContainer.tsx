import { clsx } from "clsx";

const FlexContainer = ({
    className,
    children,
    direction,
    ...props
}: React.ComponentProps<"div"> & { direction: "row" | "column" }) => {
    return (
        <div className={clsx(
            "flex gap-4",
            className,
            direction === "row" && "flex-row",
            direction === "column" && "flex-col",
        )}
             {...props}>
            {children}
        </div>
    );
};

export default FlexContainer;