import { clsx } from "clsx";
import { Icon } from "../components";

type MetricTileProps = {
    variant?: string,
    trend?: "up" | "down" | "stable",
    metric: string | number,
    inactive?: boolean,
    disabled?: boolean,
}

const MetricTile = ({ children, className, variant, trend, metric, inactive, disabled, ...props }: React.ComponentProps<"div"> & MetricTileProps) => {
    return (
        <div {...props} className={clsx(className, "flex flex-col px-4 py-2 rounded-sm min-w-[80px]",
            variant === "primary" && "bg-primary",
            variant === "primary-dark" && "bg-primary-dark",
            variant === "secondary" && "bg-secondary",
            variant === "tertiary" && "bg-tertiary",
            variant === "light-green" && "bg-light-green",
            (props.onClick && !disabled) && "hover:cursor-pointer",
            (inactive || disabled) && "opacity-35 transition-all ease-in-out duration-300",
        )}>
            <div className={clsx("flex flex-row text-white text-xl items-center gap-2", trend ? "justify-between" : "justify-center")}>
                {metric}
                {trend && <Icon icon={trend} iconClass={"text-white"}/>}
            </div>
            <div className={"flex w-full items-center justify-center text-white text-sm"}>{children}</div>
        </div>
    );
};

export default MetricTile;