import {
    Button as PrimitiveButton,
    buttonVariants as PrimitiveButtonVariants
} from "@/components/shared/primitives/button";
import { ToggleVariantStyling } from "@/components/shared/forms/Toggle";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";
import { BadgeVariantStyling } from "@/components/shared/user-feedback/Badge";

const ButtonVariantStyling = {
    "primary": "bg-primary border-primary hover:bg-primary-light hover:text-primary hover:border-primary-light",
    "primary-ghost": "bg-transparent text-primary border-solid border-primary hover:bg-primary hover:border-primary hover:text-white",
    "primary-gradient": "bg-(image:--gradient-brand) border-transparent bg-origin-border hover:brightness-115",
    "primary-gradient-ghost": "gradient-button-ghost hover:brightness-115 leading-loose",
    "secondary": "bg-secondary-dark border-secondary-dark hover:bg-secondary-light hover:text-secondary hover:border-secondary-light",
    "secondary-ghost": "bg-transparent text-secondary border-solid border-secondary hover:bg-secondary hover:border-secondary hover:text-white",
    "tertiary": "bg-tertiary border-tertiary hover:bg-tertiary-light hover:text-tertiary hover:border-tertiary-light",
    "tertiary-ghost": "bg-transparent text-tertiary border-solid border-tertiary hover:bg-tertiary-light hover:border-tertiary-light hover:text-tertiary-dark",
    "icon": "border-none p-0 has-[>svg]:p-0 m-0 bg-transparent hover:bg-transparent text-gray-600",
    "upsell": "bg-upsell border-upsell text-black hover:bg-upsell hover:text-black",
    "upsell-ghost": "bg-white border-neutral-200 text-black hover:bg-white hover:text-black",
    "black": "bg-black border-black text-white hover:bg-black hover:text-white hover:invert",
    "black-ghost": "bg-transparent text-black border-black border hover:bg-black hover:text-white",
    "link": "p-0 border-none text-black hover:text-wordpress-link-hover bg-transparent hover:bg-transparent font-normal leading-4 underline h-fit!",
    "unstyled": "p-0 border-none rounded-none font-normal font-base min-h-fit min-w-fit",
    "toggle": cn(ToggleVariantStyling.variant.primary, "max-w-fit border-none"),
    "badge": cn(BadgeVariantStyling.basis, BadgeVariantStyling.variant.primary, "text-sm hover:bg-primary hover:text-primary-light max-w-fit border-none active:bg-primary-dark active:text-primary-light"),
};

const ButtonVariants = cva(
    "px-3 py-0 border-2 font-semibold text-md cursor-pointer size-fit leading-(--text-md)",
    {
        variants: {
            variant: ButtonVariantStyling,
            size: {
                default: "h-7.5 rounded-base!",
                xs: "text-xs h-5 px-2 py-1 has-[>svg]:px-2 rounded-base!",
                sm: "text-sm h-6 rounded-base!",
                lg: "text-md h-10 rounded-base!",
                icon: "h-fit w-fit rounded-full",
                toggle: ToggleVariantStyling.size.default,
                badge: cn(BadgeVariantStyling.size.default, "h-6"),
                link: "h-7.5 rounded-none",
            }
        },
        defaultVariants: {
            variant: "primary",
            size: "default"
        },
    }
);

type ButtonVariantsProps =
    | VariantProps<typeof PrimitiveButtonVariants>
    | VariantProps<typeof ButtonVariants>;

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
    link?: never,
    target?: never,
    onClick?: never,
} | {
    type?: "button",
    link?: never,
    target?: never,
    onClick: React.ComponentProps<"button">["onClick"],
} | {
    type?: "button",
    link: string,
    target?: React.ComponentProps<"a">["target"],
    onClick?: React.MouseEventHandler<HTMLElement>,
});

type ButtonProps =
    Omit<React.ComponentProps<"button">, "type"> &
    Required<Pick<ButtonVariantsProps, "variant">> &
    ButtonVariantsProps &
    ActionProps;

/**
 * Custom extension of shadcn's {@link PrimitiveButton} component.
 *
 * In addition to the named props, it accepts all other possible props
 * for a `button` element, passed to Button through a `...props` rest object
 * @uses {@link PrimitiveButton} from primitives
 * @uses {@link Icon}
 *
 * @version 1.0.0
 */
const Button = ({
    variant,
    children,
    className,
    size,
    link,
    onClick,
    target = "_blank",
    type = "button",
    ...props
}: ButtonProps) => {
    const isCustomVariant = variant != null && variant in ButtonVariantStyling;

    const variantClasses = isCustomVariant
        //@ts-expect-error tsc can't verify type narrowing on variant
        ? ButtonVariants({ variant, size })
        //@ts-expect-error tsc can't verify type narrowing on variant
        : PrimitiveButtonVariants({ variant, size });

    const classes = cn(
        variantClasses,
        className
    );

    const StyledButton = () => (
        <PrimitiveButton
            className={classes}
            {...(onClick && { onClick: onClick })}
            // we need to pass "button" if the type prop has "trigger" as value because "trigger" isn't a valid type for the base HTML button element
            type={type !== "trigger" ? type : "button"}
            {...props}
        >
            {/*span required for gradient text color to work*/}
            {variant === "primary-gradient-ghost" ?
                <span className={"inline-flex items-center justify-center gap-2"}>
                    {children}
                </span>
                :
                children
            }
        </PrimitiveButton>
    );

    if (variant === "link" && link) {
        return (
            <a
                href={link}
                target={target}
                className={classes}
                rel={"noopener noreferrer"}
                onClick={onClick}
            >
                {children}
            </a>
        );
    }

    if (type === "button" && link) {
        return (
            <a
                href={link}
                target={target}
                className={"flex max-w-fit"}
                rel={"noopener noreferrer"}
            >
                <StyledButton/>
            </a>
        );
    }

    return (
        <StyledButton/>
    );
};

export { Button };