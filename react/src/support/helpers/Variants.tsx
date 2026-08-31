import { cva } from "class-variance-authority";
import { cn } from "@/support/functions/utils";

/**
 * ======================================
 * ALERT
 * ======================================
 */
const alertVariantStyling = {
    success: "bg-rsp-success-light text-rsp-success-dark *:data-[slot=alert-description]:text-rsp-success-dark",
    warning: "bg-rsp-warning-light text-rsp-warning-dark *:data-[slot=alert-description]:text-rsp-warning-dark",
    error: "bg-rsp-error-light text-rsp-error-dark *:data-[slot=alert-description]:text-rsp-error-dark",
    info: "bg-rsp-info-light text-rsp-info-dark *:data-[slot=alert-description]:text-rsp-info-dark",
};

const alertVariants = cva(
    "gap-2! justify-between w-full rounded-md p-3 text-md text-center font-semibold leading-4",
    {
        variants: {
            variant: alertVariantStyling,
        },
        defaultVariants: {
            variant: "info",
        },
    }
);


/**
 * ======================================
 * BADGE
 * ======================================
 */
const badgeVariantStyling = {
    basis: "rounded-full text-[9px] font-semibold",
    variant: {
        primary: "bg-primary-light text-primary",
        gradient: "bg-(image:--gradient-brand) border-transparent bg-origin-border text-white",
    },
    size: {
        default: "h-5 px-2 py-1",
    },
};

const badgeVariants = cva(
    badgeVariantStyling.basis, {
        variants: {
            variant: badgeVariantStyling.variant,
            size: badgeVariantStyling.size,
        },
        defaultVariants: {
            variant: "primary",
            size: "default",
        }
    });


/**
 * ======================================
 * BLOCK
 * ======================================
 */
const blockVariants = cva(
    "w-full p-4 gap-[10px] shadow-lg",
    {
        variants: {
            variant: {
                "default": "border-none",
                "transparent": "bg-transparent shadow-none border-none"
            },
        },
        defaultVariants: {
            variant: "default",
        },
    });


/**
 * ======================================
 * DIALOG
 * ======================================
 */

/**
 * CSS variable `--removed-body-scroll-bar-size` is set and updated by the
 * RemoveScroll package we utilise, accurately keeping track of the width of the
 * scrollbar per browser so we can accommodate our sizing of the Dialog once the
 * scrollbar disappears.
 * `var(--spacing)*6` is used to compensate for the `p-6` class used in DialogVariants
 */
const rightPaddingClass = "pr-[calc(var(--spacing)*6+var(--removed-body-scroll-bar-size))]";
const calculatedWidthClass = "w-[calc(100%+var(--removed-body-scroll-bar-size))]";
const rightOffsetClass = "right-[calc(0-var(--removed-body-scroll-bar-size))]";

const dialogVariantStyling = {
    "default": "top-[calc(50vh+var(--scroll-progress-in-pixels))] left-[50%] w-full max-sm:max-w-[calc(100%-2rem)] sm:min-w-150 sm:max-w-150 min-h-100 translate-x-[-50%] translate-y-[-50%] rounded-xs border shadow-lg",
    "full-screen": cn("h-full top-(--scroll-progress-in-pixels) max-h-[calc(100dvh-var(--wp-admin--admin-bar--height))] max-[600px]:mt-(--wp-admin--admin-bar--height)", rightPaddingClass, calculatedWidthClass, rightOffsetClass),
};

const dialogVariants = cva(
    "font-sans selection:bg-primary selection:text-primary-foreground bg-background data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 absolute z-50 grid gap-4 p-6 duration-200",
    {
        variants: {
            variant: dialogVariantStyling
        },
        defaultVariants: {
            variant: "default",
        },
    }
);


/**
 * ======================================
 * HEADER
 * ======================================
 */
const headerVariantStyling = {
    "default": "bg-white",
    "transparent": "bg-transparent",
};

const headerVariants = cva(
    "min-w-full",
    {
        variants: {
            variant: headerVariantStyling
        },
        defaultVariants: {
            variant: "default",
        },
    }
);


/**
 * ======================================
 * NOTIFICATION
 * ======================================
 */
const notificationVariantStyling = {
    "info": "border-rsp-info-light bg-rsp-info-light",
    "warning": "border-rsp-error-light bg-rsp-error-light",
};

const notificationVariants = cva(
    "grid-cols-1 p-4 shadow-lg",
    {
        variants: {
            variant: notificationVariantStyling,
        },
        defaultVariants: {
            variant: "info",
        },
    }
);


/**
 * ======================================
 * TOGGLE
 * ======================================
 */
const toggleVariantStyling = {
    variant: {
        primary: cn(badgeVariantStyling.basis, badgeVariantStyling.variant.primary, "text-sm hover:bg-primary hover:text-primary-light data-[state=on]:bg-primary-dark data-[state=on]:text-primary-light"),
    },
    size: {
        default: cn(badgeVariantStyling.size.default, "h-6"),
    }
};

const toggleVariants = cva(
    "max-w-fit cursor-pointer",
    {
        variants: {
            variant: toggleVariantStyling.variant,
            size: toggleVariantStyling.size,
        },
        defaultVariants: {
            variant: "primary",
            size: "default",
        },
    }
);


/**
 * ======================================
 * BUTTON
 * ======================================
 */
const buttonVariantStyling = {
    "primary": "bg-primary border-primary hover:bg-primary-light hover:text-primary hover:border-primary-light",
    "primary-ghost": "bg-transparent text-primary border-solid border-primary hover:bg-primary hover:border-primary hover:text-white font-bold",
    "primary-gradient": "bg-(image:--gradient-brand) border-transparent bg-origin-border text-white hover:text-white hover:brightness-115",
    "primary-gradient-ghost": "gradient-button-ghost hover:brightness-115 leading-loose font-bold",
    "secondary": "bg-secondary-dark border-secondary-dark hover:bg-secondary-light hover:text-secondary hover:border-secondary-light",
    "secondary-ghost": "bg-transparent text-secondary border-solid border-secondary hover:bg-secondary hover:border-secondary hover:text-white font-bold",
    "tertiary": "bg-tertiary border-tertiary hover:bg-tertiary-light hover:text-tertiary hover:border-tertiary-light",
    "tertiary-ghost": "bg-transparent text-tertiary border-solid border-tertiary hover:bg-tertiary-light hover:border-tertiary-light hover:text-tertiary-dark font-bold",
    "icon": "border-none p-0 has-[>svg]:p-0 m-0 bg-transparent hover:bg-transparent text-gray-600 hover:text-gray-800",
    "upsell": "bg-upsell border-upsell text-black hover:bg-upsell hover:text-black",
    "upsell-ghost": "bg-white border-neutral-200 text-black hover:bg-white hover:text-black",
    "black": "bg-black border-black text-white hover:bg-black hover:text-white hover:invert",
    "black-ghost": "bg-transparent text-black border-black border hover:bg-black hover:text-white",
    "unstyled": "p-0 border-none rounded-none font-normal text-md min-h-fit min-w-fit",
    "toggle": cn(toggleVariantStyling.variant.primary, "max-w-fit border-none"),
    "badge": cn(badgeVariantStyling.basis, badgeVariantStyling.variant.primary, "text-sm hover:bg-primary hover:text-primary-light max-w-fit border-none active:bg-primary-dark active:text-primary-light"),
};

const defaultPrimitiveButtonClasses = "inline-flex shrink-0 items-center justify-center gap-2 rounded-md text-sm font-medium whitespace-nowrap transition-all outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4";

const buttonVariants = cva(
    [defaultPrimitiveButtonClasses, "px-3 py-0 border-2 font-semibold text-md cursor-pointer size-fit leading-(--text-md) no-underline!"],
    {
        variants: {
            variant: buttonVariantStyling,
            size: {
                default: "h-7.5 rounded-base!",
                xs: "text-xs h-5 px-2 py-1 has-[>svg]:px-2 rounded-base!",
                sm: "text-sm h-6 rounded-base!",
                lg: "text-md h-10 rounded-base!",
                icon: "h-fit w-fit rounded-full",
                toggle: toggleVariantStyling.size.default,
                badge: cn(badgeVariantStyling.size.default, "h-6"),
            }
        },
        defaultVariants: {
            variant: "primary",
            size: "default"
        },
    }
);

/**
 * Additional span is required for gradient text color to work
 */
const PrimaryGradientGhostVariantWrapper = ({ children, className, ...props }: React.ComponentProps<"span">) => (
    <span
        className={cn("inline-flex items-center justify-center gap-2", className)}
        {...props}
    >
        {children}
    </span>
);


/**
 * ======================================
 * UTILITY FUNCTION
 * isCustomVariant
 * ======================================
 */
const isCustomVariant = (variant: string, component: string) => {
    switch (component) {
        case "alert": {
            return (variant in alertVariantStyling);
        }
        case "button": {
            return (variant in buttonVariantStyling);
        }
        case "notification": {
            return (variant in notificationVariantStyling);
        }
        case "toggle": {
            return (variant in toggleVariantStyling.variant);
        }
        default: {
            throw new Error("Invalid variant");
        }
    }
};

export {
    alertVariants,
    badgeVariants,
    blockVariants,
    buttonVariants,
    dialogVariants,
    rightPaddingClass,
    calculatedWidthClass,
    headerVariants,
    notificationVariants,
    toggleVariants,
    isCustomVariant,
    defaultPrimitiveButtonClasses,
    PrimaryGradientGhostVariantWrapper,
};