import {
    Toggle as PrimitiveToggle,
    toggleVariants as PrimitiveToggleVariants
} from "@/components/shared/primitives/toggle";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";

const ToggleVariantStyling = {
    variant: {
        primary: "bg-primary-light text-primary rounded-full text-sm font-semibold hover:bg-primary hover:text-primary-light data-[state=on]:bg-primary-dark data-[state=on]:text-primary-light",
    },
    size: {
        default: "h-6 px-2 py-1",
    }

};

const ToggleVariants = cva(
    "max-w-[fit-content]",
    {
        variants: {
            variant: ToggleVariantStyling.variant,
            size: ToggleVariantStyling.size,
        },
        defaultVariants: {
            variant: "primary",
            size: "default",
        },
    }
);

export type ToggleVariantsProps =
    | VariantProps<typeof PrimitiveToggleVariants>
    | VariantProps<typeof ToggleVariants>;


const Toggle = ({
    pressed,
    onPressedChange,
    children,
    className,
    variant,
}: ToggleVariantsProps & Omit<React.ComponentProps<typeof PrimitiveToggle>, "variant">) => {
    return (
        <PrimitiveToggle
            // @ts-expect-error tsc can't verify type narrowing on variant
            className={cn(variant ? variant in ToggleVariantStyling.variant ? ToggleVariants({ variant }) : PrimitiveToggleVariants({ variant }) : "",
                className,
            )}
            pressed={pressed}
            onPressedChange={onPressedChange}
        >
            {children}
        </PrimitiveToggle>
    );
};

export { Toggle, ToggleVariantStyling };