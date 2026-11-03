import { clsx } from "clsx";
import { Button, FlexContainer, Icon } from "@/components/shared";
import { cva, type VariantProps } from "class-variance-authority";

const MetricTileVariantStyling = {
    "primary": "bg-primary hover:bg-primary/80",
    "primary-dark": "bg-primary-dark hover:bg-primary-dark/80",
    "secondary": "bg-secondary hover:bg-secondary/80",
    "tertiary": "bg-tertiary hover:bg-tertiary/80",
    "light-green": "bg-light-green hover:bg-light-green/80"
};

const MetricTileVariants = cva(
    "flex flex-col px-3 py-2 h-fit w-fit rounded-base",
    {
        variants: {
            variant: MetricTileVariantStyling,
        },
        defaultVariants: {
            variant: "primary",
        },
    }
);

export type MetricTileProps =
    React.ComponentProps<"button"> &
    VariantProps<typeof MetricTileVariants> &
    {
        trend?: "up" | "down" | "stable",
        metric: string | number,
        inactive?: boolean,
        disabled?: boolean,
    };

const MetricTile = ({
    variant,
    children,
    className,
    trend,
    metric,
    inactive,
    disabled,
    onClick,
}: MetricTileProps) => {
    return (
        <Button
             className={clsx(
                 variant && variant in MetricTileVariantStyling && MetricTileVariants({ variant }),
                 className,
                 (onClick && !disabled) && "hover:cursor-pointer",
                 (inactive || disabled) && "opacity-35 transition-all ease-in-out duration-300",
             )}
             variant={"unstyled"}
             onClick={onClick}
             disabled={disabled}
        >
            <FlexContainer direction={"row"} className={clsx("text-white text-[16px] items-center gap-1.5! justify-center")}>
                {metric}
                {trend && <Icon icon={trend} className={"text-white size-3"}/>}
            </FlexContainer>
            <FlexContainer direction={"row"} className={"w-full gap-0! items-center justify-center text-white text-xs"}>
                {children}
            </FlexContainer>
        </Button>
    );
};

export { MetricTile };