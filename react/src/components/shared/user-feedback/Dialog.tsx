import {
    Dialog as PrimitiveDialog,
    DialogContent as PrimitiveDialogContent,
    DialogDescription as PrimitiveDialogDescription,
    DialogHeader as PrimitiveDialogHeader,
    DialogPortal as PrimitiveDialogPortal,
    DialogTitle as PrimitiveDialogTitle,
    DialogTrigger as PrimitiveDialogTrigger,
} from "@/components/shared/primitives/dialog.tsx";
import { RemoveScroll } from "react-remove-scroll";
import { cn } from "@/functions/utils.ts";
import * as React from "react";
import { useEffect } from "react";
import { Close as RadixDialogClose, Content as RadixDialogContent } from "@radix-ui/react-dialog";
import { Icon } from "@/components/shared/user-feedback/Icon.tsx";

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
const Overlay = ({ className, ...props }: React.ComponentProps<"div">) => {
    return (
        <div
            data-slot={"dialog-overlay"}
            className={cn("data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 absolute inset-0 z-50 bg-black/50 min-h-screen", className)}
            {...props}
        >
        </div>
    );
};

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
    ...props
}: React.ComponentProps<typeof PrimitiveDialogContent> & Omit<React.ComponentProps<typeof PrimitiveDialog>, "modal"> & {
    renderRadixDialogOverlayForce?: boolean,
}) => {
    const RENDER_DIALOG_OVERLAY = renderRadixDialogOverlayForce ?? false;
    const container = document.getElementById("wpbody") ?? undefined;
    const appContainer = document.getElementById("metricool_app") ?? undefined;
    const wpFooter = document.getElementById("wpfooter") ?? undefined;

    useEffect(() => {
        if (open) {
            appContainer?.classList.add("pointer-events-none");
            wpFooter?.classList.add("pointer-events-none");
        } else {
            appContainer?.classList.remove("pointer-events-none");
            wpFooter?.classList.remove("pointer-events-none");
        }
    }, [appContainer, wpFooter, open]);

    return (
        <PrimitiveDialog open={open} onOpenChange={onOpenChange} modal={RENDER_DIALOG_OVERLAY}>
            <PrimitiveDialogPortal data-slot={"dialog-portal"} container={container}>
                <Overlay data-state={open ? "open" : "closed"}/>
                <RemoveScroll>
                    <RadixDialogContent
                        data-slot={"dialog-content"}
                        className={cn(
                            "selection:bg-primary selection:text-primary-foreground",
                            "bg-background data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 absolute top-[calc(50vh+var(--scroll-progress-in-pixels))] left-[50%] z-50 grid w-full max-sm:max-w-[calc(100%-2rem)] sm:min-w-[600px] sm:max-w-[600px] min-h-[400px] translate-x-[-50%] translate-y-[-50%] gap-4 rounded-xs border p-6 shadow-lg duration-200",
                            className
                        )}
                        {...props}
                    >
                        {children}
                        {showCloseButton && (
                            <RadixDialogClose
                                data-slot={"dialog-close"}
                                className={"focus:ring-ring data-[state=open]:bg-accent data-[state=open]:text-muted-foreground absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 hover:cursor-pointer"}
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