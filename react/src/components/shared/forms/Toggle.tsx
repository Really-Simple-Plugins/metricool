import {
    Toggle as PrimitiveToggle,
    toggleVariants as PrimitiveToggleVariants
} from "@/components/shared/primitives/toggle";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";
import { BadgeVariantStyling } from "@/components/shared/user-feedback/Badge";

const ToggleVariantStyling = {
    variant: {
        primary: cn(BadgeVariantStyling.basis, BadgeVariantStyling.variant.primary, "text-sm hover:bg-primary hover:text-primary-light data-[state=on]:bg-primary-dark data-[state=on]:text-primary-light"),
    },
    size: {
        default: cn(BadgeVariantStyling.size.default, "h-6"),
    }
};

const ToggleVariants = cva(
    "max-w-fit cursor-pointer",
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
    variant = "primary",
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