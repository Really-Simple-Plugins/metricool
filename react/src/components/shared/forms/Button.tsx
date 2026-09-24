import { Button as PrimitiveButton } from "@/components/shared/primitives/button";
import { type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";
import { buttonVariants, ButtonVariantChildrenWrapper } from "@/support/helpers/Variants";
import type { RequiredProperty } from "@/types/utility/RequiredProperty";

type ButtonVariantsProps = VariantProps<typeof buttonVariants>;

/**
 * A 'discriminating union type' which ensures that if the type is "button" (default)
 * a Button component can have a link, an onClick prop, or both,
 * and with type "submit" or "trigger" it can have neither.
 *
 * Type "trigger" was added as several shadcn components use buttons as triggers
 * for certain interactivity. These are given an action by the component itself
 * so they should not be given either a link or an onClick prop, which this
 * union type only allowed with type "submit", which was not right for the
 * behaviour.
 */
type ActionProps = ({
    type: "submit" | "trigger",
    onClick?: never,
} | {
    type?: "button",
    onClick: React.ComponentProps<"button">["onClick"],
});

type ButtonProps =
    Omit<React.ComponentProps<"button">, "type"> &
    RequiredProperty<ButtonVariantsProps, "variant"> &
    ActionProps;

/**
 * Custom extension of shadcn's {@link PrimitiveButton} component.
 *
 * In addition to the named props, it accepts all other possible props
 * for a `button` element, passed to Button through a `...props` rest object
 * @uses {@link PrimitiveButton} from primitives
 * @uses {@link Icon}
 *
 * Note: original variants from shadcn's {@link PrimitiveButton} can no longer be
 * passed to this component.
 *
 * @version 1.0.0
 */
const Button = ({
    variant,
    children,
    className,
    size,
    onClick,
    type = "button",
    ...props
}: ButtonProps) => {

    return (
        <PrimitiveButton
            className={cn(buttonVariants({ variant, size, className }))}
            {...(onClick && { onClick: onClick })}
            // we need to pass "button" if the type prop has "trigger" as value because "trigger" isn't a valid type for the base HTML button element
            type={type !== "trigger" ? type : "button"}
            {...props}
        >
            <ButtonVariantChildrenWrapper variant={variant}>
                {children}
            </ButtonVariantChildrenWrapper>
        </PrimitiveButton>
    );
};

export { Button };