import {
    Button as PrimitiveButton,
    buttonVariants as PrimitiveButtonVariants
} from "@/components/shared/primitives/button.tsx";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/functions/utils.ts";

const ButtonVariantStyling = {
    "primary": "bg-primary border-primary hover:bg-primary-light hover:text-primary hover:border-primary-light",
    "primary-ghost": "bg-transparent text-primary border-solid border-primary hover:bg-primary-light hover:border-primary-light hover:text-primary-dark",
    "primary-gradient": "bg-[image:var(--gradient-brand)] border-transparent [background-origin:border-box] hover:brightness-115",
    "primary-gradient-ghost": "gradient-button-ghost hover:brightness-115",
    "secondary": "bg-secondary-dark border-secondary-dark hover:bg-secondary-light hover:text-secondary hover:border-secondary-light",
    "secondary-ghost": "bg-transparent text-secondary border-solid border-secondary hover:text-accent-foreground hover:bg-secondary-light hover:border-secondary-light hover:text-secondary-dark",
    "tertiary": "bg-tertiary border-tertiary hover:bg-tertiary-light hover:text-tertiary hover:border-tertiary-light",
    "tertiary-ghost": "bg-transparent text-tertiary border-solid border-tertiary hover:text-accent-foreground hover:bg-tertiary-light hover:border-tertiary-light hover:text-tertiary-dark",
    "icon": "rounded-full border-none p-0 has-[>svg]:p-0 m-0 bg-transparent hover:bg-transparent text-gray-600",
    "upsell": "bg-upsell border-upsell text-black hover:bg-upsell hover:text-black",
    "upsell-ghost": "bg-white border-neutral-200 text-black hover:bg-white hover:text-black",
    "black": "bg-black border-black text-white hover:bg-black hover:text-white hover:invert",
    "link": "p-0 border-none text-black hover:text-wordpress-link-hover bg-transparent hover:bg-transparent font-normal underline !h-[fit-content]",
    "unstyled": "p-0 border-none rounded-none font-normal font-base min-h-[fit-content] min-w-[fit-content]",
};

const ButtonVariants = cva(
    "rounded-xs px-3 border-2 font-semibold text-md cursor-pointer size-fit",
    {
        variants: {
            variant: ButtonVariantStyling,
            size: {
                default: "h-8",
                xs: "text-xs h-5 px-2 py-1 has-[>svg]:px-2",
                sm: "text-sm h-6",
                lg: "text-lg h-10",
                icon: "h-[fit-content] w-[fit-content]",
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

type ActionProps = ({
    type: "submit",
    link?: never,
    onClick?: never,
} | {
    type?: "button",
    link?: never,
    onClick: React.ComponentProps<"button">["onClick"],
} | {
    type?: "button",
    link: string,
    onClick?: never,
});


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
    type = "button",
}: React.ComponentProps<"button"> & Required<Pick<ButtonVariantsProps, "variant">> & ButtonVariantsProps & ActionProps) => {
    //@ts-expect-error tsc can't verify type narrowing on variant
    const classes = cn(variant in ButtonVariantStyling ? ButtonVariants({ variant, size }) : PrimitiveButtonVariants({ variant, size }), className);

    const StyledButton = () => (
        <PrimitiveButton
            className={classes}
            {...(onClick && { onClick: onClick })}
        >
            {/*span required for gradient text color to work*/}
            {variant === "primary-gradient-ghost" ?
                <span>{children}</span>
            :
                children
            }
        </PrimitiveButton>
    );

    if (variant === "link" && link) {
        return (
            <a href={link} target={"_blank"} className={classes}>
                {children}
            </a>
        );
    }

    if (type === "button" && link) {
        return (
            <a href={link} target={"_blank"} className={"flex max-w-[fit-content]"}>
                <StyledButton/>
            </a>
        );
    }

    return (
        <StyledButton/>
    );
};

export { Button };