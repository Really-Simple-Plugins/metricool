import { Link as RouterLink, type LinkProps as TanstackRouterLinkProps } from "@tanstack/react-router";
import type { VariantProps } from "class-variance-authority";
import {
    baseLinkClasses,
    buttonVariants,
    defaultLinkStyling,
    PrimaryGradientGhostVariantWrapper
} from "@/support/helpers/Variants";
import { cn } from "@/support/functions/utils";

type BaseLinkProps =
    Pick<React.ComponentProps<"a">, "href" | "target" | "onClick" | "className">
    & {
    route?: never,
}

type RouterLinkProps =
    Omit<React.ComponentProps<typeof RouterLink>, "to" | "children">
    & {
    href?: never,
    route: TanstackRouterLinkProps["to"],
}

export type LinkProps =
    Pick<React.ComponentProps<"a">, "children">
    & (BaseLinkProps | RouterLinkProps);

/**
 *
 * @version 1.0.0
 */
const Link = ({
    href,
    target,
    onClick,
    className,
    children,
    variant,
    size,
    route,
    ...props
}: LinkProps & VariantProps<typeof buttonVariants>) => {
    let classes = cn("cursor-pointer", className);

    /**
     * This makes it impossible to specifically style a Link as the "unstyled" Button variant, but needed to create a default styling
     */
    if (!(variant === "unstyled")) {
        classes = cn(
            baseLinkClasses,
            variant
                ? cn(buttonVariants({ variant, size }))
                : defaultLinkStyling,
            className,
        );
    }

    if (!route) {
        return (
            <a
                href={href}
                target={target ?? "_blank"}
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
        )
    }

    return (
        <RouterLink
            to={route}
            className={classes}
            target={target}
            {...props}
        >
            {variant === "primary-gradient-ghost" ?
                <PrimaryGradientGhostVariantWrapper>
                    {children}
                </PrimaryGradientGhostVariantWrapper>
                : children
            }
        </RouterLink>
    );
};

export { Link };