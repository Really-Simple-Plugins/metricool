import {
    Dialog as PrimitiveDialog,
    DialogContent as PrimitiveDialogContent,
    DialogDescription as PrimitiveDialogDescription,
    DialogHeader as PrimitiveDialogHeader,
    DialogPortal as PrimitiveDialogPortal,
    DialogTitle as PrimitiveDialogTitle,
    DialogTrigger as PrimitiveDialogTrigger,
} from "@/components/shared/primitives/dialog";
import { RemoveScroll } from "react-remove-scroll";
import { cn } from "@/support/functions/utils";
import * as React from "react";
import { useCallback, useEffect } from "react";
import { Close as RadixDialogClose, Content as RadixDialogContent } from "@radix-ui/react-dialog";
import { Icon } from "@/components/shared/user-feedback/Icon";
import { type VariantProps } from "class-variance-authority";
import { dialogVariants, calculatedWidthClass, rightPaddingClass } from "@/support/helpers/Variants";

/**
 * Custom overlay component.
 * Radix' RadixDialog.Overlay disables pointer events on the entire
 * document's <body> tag, blocking all interactivity which disrupts WordPress
 * functionality, so we cannot use it and have created our own overlay instead.
 *
 * @param {string} [className] - classes to be added in addition to default styling
 * @param props - rest variable, accepts all props available on a div element
 *
 * @version 1.0.0
 */
const Overlay = React.forwardRef<HTMLDivElement, React.ComponentProps<"div">>(function Overlay({ className, ...props }, ref) {
    return (
        <div
            data-slot={"dialog-overlay"}
            ref={ref}
            className={cn("data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 absolute inset-0 z-50 bg-black/50 min-h-screen", rightPaddingClass, calculatedWidthClass, className)}
            {...props}
        >
        </div>
    );
});

type DialogProps = {
    renderRadixDialogOverlayForce?: boolean,
} & VariantProps<typeof dialogVariants>

/**
 * Our custom extension of shadcn's Dialog component.
 *
 * Accepts all props shadcn's Dialog and DialogContent do, except for "modal",
 * which needs to be set to false so the RadixDialog.Overlay doesn't get rendered.
 * @see {Overlay} for further explanation.
 * Instead, the renderDialogOverlayForce prop is added, which should NOT be used
 * unless the consequences have been carefully considered.
 *
 * @version 1.0.0
 */
const Dialog = ({
    open,
    onOpenChange,
    className,
    children,
    renderRadixDialogOverlayForce,
    showCloseButton = false,
    variant,
    ...props
}: React.ComponentProps<typeof PrimitiveDialogContent> & Omit<React.ComponentProps<typeof PrimitiveDialog>, "modal"> & DialogProps) => {
    const RENDER_DIALOG_OVERLAY = renderRadixDialogOverlayForce ?? false;
    const container = document.getElementById("wpbody") ?? undefined;
    const appContainer = document.getElementById("rsp-app-root") ?? undefined;
    const wpFooter = document.getElementById("wpfooter") ?? undefined;

    useEffect(() => {
        if (open) {
            appContainer?.classList.add("pointer-events-none");
            wpFooter?.classList.add("pointer-events-none");
        } else {
            appContainer?.classList.remove("pointer-events-none");
            wpFooter?.classList.remove("pointer-events-none");
        }

        return () => {
            appContainer?.classList.remove("pointer-events-none");
            wpFooter?.classList.remove("pointer-events-none");
        };
    }, [appContainer, wpFooter, open]);

    const setScrollPixelProgressCssVar = () => {
        document.documentElement.style.setProperty("--scroll-progress-in-pixels", `${window.scrollY}px`);
    };

    const onScroll = useCallback(() => {
        requestAnimationFrame(setScrollPixelProgressCssVar);
    }, []);

    useEffect(() => {
        window.addEventListener("scroll", onScroll, { passive: true });

        return () => window.addEventListener("scroll", onScroll, { passive: true });
    }, [onScroll]);

    return (
        <PrimitiveDialog open={open} onOpenChange={onOpenChange} modal={RENDER_DIALOG_OVERLAY}>
            <PrimitiveDialogPortal data-slot={"dialog-portal"} container={container}>
                <Overlay data-state={open ? "open" : "closed"}/>
                <RemoveScroll>
                    <RadixDialogContent
                        data-slot={"dialog-content"}
                        className={cn(dialogVariants({ variant, className }))}
                        onOpenAutoFocus={(event: Event) => {
                            event.preventDefault();
                        }}
                        data-state={open ? "open" : "closed"}
                        {...props}
                    >
                        {children}
                        {showCloseButton && (
                            <RadixDialogClose
                                data-slot={"dialog-close"}
                                data-state={open ? "open" : "closed"}
                                className={"focus:ring-ring data-[state=open]:text-muted-foreground absolute top-4 right-[calc(var(--spacing)*4+var(--removed-body-scroll-bar-size))] rounded-xs opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 hover:cursor-pointer"}
                            >
                                <Icon icon={"close"} className={"text-black size-8"}/>
                                <span className={"sr-only"}>Close</span>
                            </RadixDialogClose>
                        )}
                    </RadixDialogContent>
                </RemoveScroll>
            </PrimitiveDialogPortal>
            <PrimitiveDialogDescription/>
        </PrimitiveDialog>
    );
};

export {
    Dialog,
    Overlay,
    PrimitiveDialogHeader as DialogHeader,
    PrimitiveDialogTitle as DialogTitle,
    PrimitiveDialogTrigger as DialogTrigger,
};