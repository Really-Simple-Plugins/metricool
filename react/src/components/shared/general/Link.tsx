import type { VariantProps } from "class-variance-authority";
import { buttonVariants, PrimaryGradientGhostVariantWrapper } from "@/support/helpers/Variants";
import { cn } from "@/support/functions/utils";

type LinkProps = Pick<React.ComponentProps<"a">, "href" | "target" | "onClick" | "className" | "children">;

/**
 *
 * v1.0.0
 */
const Link = ({
    href,
    target,
    onClick,
    className,
    children,
    variant,
    size
}: LinkProps & VariantProps<typeof buttonVariants>) => {

    const classes = cn(
        "h-7.5 rounded-none text-md cursor-pointer",
        variant
            ? cn(buttonVariants({ variant, size }))
            : "p-0 border-none text-black hover:text-wordpress-link-hover bg-transparent hover:bg-transparent font-normal leading-4 underline h-fit!",
        className,
    );

    return (
        <a
            href={href}
            target={target}
            className={classes}
            rel={"noopener noreferrer"}
            onClick={onClick}
        >
            {variant === "primary-gradient-ghost" ?
                <PrimaryGradientGhostVariantWrapper>
                    {children}
                </PrimaryGradientGhostVariantWrapper>
                : children
            }
        </a>
    );
};

export { Link };