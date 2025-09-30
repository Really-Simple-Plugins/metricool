import { clsx } from "clsx";
import { Icon } from "../components";

type MetricTileProps = {
    variant: "primary" | "primary-dark" | "secondary" | "tertiary" | "light-green",
    direction?: "up" | "down" | "stable",
    metric: string | number,
    metricTitle: string,
}

const MetricTile = ({ className, variant, direction, metric, metricTitle }: React.ComponentProps<"div"> & MetricTileProps) => {
    return (
        <div className={clsx(className, "flex flex-col px-4 py-2 rounded-sm min-w-[90px]",
            variant === "primary" && "bg-primary",
            variant === "primary-dark" && "bg-primary-dark",
            variant === "secondary" && "bg-secondary",
            variant === "tertiary" && "bg-tertiary",
            variant === "light-green" && "bg-light-green",
        )}>
            <div className={clsx("flex flex-row text-white text-xl", direction ? "justify-between" : "justify-center")}>
                {metric}
                {direction && <Icon icon={direction} iconClass={"text-white"}/>}
            </div>
            <div className={"flex w-full items-center justify-center text-white text-sm"}>{metricTitle}</div>
        </div>
    );
};

export default MetricTile;