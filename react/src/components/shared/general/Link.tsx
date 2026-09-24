import { Link as RouterLink, type LinkProps as TanstackRouterLinkProps } from "@tanstack/react-router";
import type { VariantProps } from "class-variance-authority";
import {
    baseLinkClasses,
    buttonVariants,
    defaultLinkStyling,
    ButtonVariantChildrenWrapper
} from "@/support/helpers/Variants";
import { cn } from "@/support/functions/utils";
import type { RequiredProperty } from "@/types/utility/RequiredProperty";

type BaseLinkProps =
    RequiredProperty<Pick<React.ComponentProps<"a">, "href" | "target" | "onClick" | "className">, "href">
    & {
    route?: never,
}

type RouterLinkProps =
    Omit<React.ComponentProps<typeof RouterLink>, "to" | "children">
    & {
    href?: never,
    route: RequiredProperty<TanstackRouterLinkProps, "to">["to"],
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
     * `unstyled` is one of the possible buttonVariants. When passed to this Link
     * component, it's used to skip overwriting the `classes`, so the only classes
     * a <Link variant={"unstyled"}/> receives is "cursor-pointer" and any other
     * classes passed directly.
     * This does make it impossible to specifically style a Link as the "unstyled"
     * buttonVariant.
     */
    if (variant !== "unstyled") {
        classes = cn(
            baseLinkClasses,
            variant
                ? cn(buttonVariants({ variant, size }), "no-underline!")
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
                <ButtonVariantChildrenWrapper>
                    {children}
                </ButtonVariantChildrenWrapper>
            </a>
        );
    }

    return (
        <RouterLink
            to={route}
            className={classes}
            target={target}
            onClick={onClick}
            {...props}
        >
            <ButtonVariantChildrenWrapper>
                {children}
            </ButtonVariantChildrenWrapper>
        </RouterLink>
    );
};

export { Link };