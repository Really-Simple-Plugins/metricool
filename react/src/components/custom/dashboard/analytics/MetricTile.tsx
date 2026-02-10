import { clsx } from "clsx";
import { Button, FlexContainer, Icon } from "@/components/shared";
import { cva, type VariantProps } from "class-variance-authority";

const MetricTileVariants = cva(
    "flex flex-col px-3 py-2 rounded-sm",
    {
        variants: {
            variant: {
                "primary": "bg-primary",
                "primary-dark": "bg-primary-dark",
                "secondary": "bg-secondary",
                "tertiary": "bg-tertiary",
                "light-green": "bg-light-green"
            },
        },
        defaultVariants: {
            variant: "primary",
        },
    }
);

type MetricTileProps = {
    trend?: "up" | "down" | "stable",
    metric: string | number,
    inactive?: boolean,
    disabled?: boolean,
}

const MetricTile = ({
    variant,
    children,
    className,
    trend,
    metric,
    inactive,
    disabled,
    onClick,
}: React.ComponentProps<"button"> & MetricTileProps & VariantProps<typeof MetricTileVariants>) => {
    return (
        <Button
             className={clsx(
                 MetricTileVariants({ variant }),
                 className,
                 (onClick && !disabled) && "hover:cursor-pointer",
                 (inactive || disabled) && "opacity-35 transition-all ease-in-out duration-300",
             )}
             variant={"unstyled"}
             size={"icon"}
             onClick={onClick}
        >
            <FlexContainer direction={"row"} className={clsx("text-white text-[16px] items-center !gap-1.5 justify-center")}>
                {metric}
                {trend && <Icon icon={trend} className={"text-white size-3"}/>}
            </FlexContainer>
            <FlexContainer direction={"row"} className={"w-full !gap-0 items-center justify-center text-white text-xs"}>
                {children}
            </FlexContainer>
        </Button>
    );
};

export { MetricTile };