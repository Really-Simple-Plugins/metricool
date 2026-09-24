import { Toggle as PrimitiveToggle } from "@/components/shared/primitives/toggle";
import { type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";
import { toggleVariants } from "@/support/helpers/Variants";

export type ToggleVariantsProps = VariantProps<typeof toggleVariants>;

/**
 *
 * Note: original variants from shadcn's {@link PrimitiveToggle} can no longer be
 * passed to this component.
 *
 * @version: 1.0.0
 */
const Toggle = ({
    pressed,
    onPressedChange,
    children,
    className,
    variant = "primary",
}: ToggleVariantsProps & Omit<React.ComponentProps<typeof PrimitiveToggle>, "variant">) => {
    return (
        <PrimitiveToggle
            className={cn(toggleVariants({ variant, className }))}
            pressed={pressed}
            onPressedChange={onPressedChange}
        >
            {children}
        </PrimitiveToggle>
    );
};

export { Toggle };