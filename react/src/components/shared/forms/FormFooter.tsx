import { Button, FlexContainer } from "@/components";
import { useEffect, useState } from "react";
import { __ } from "@wordpress/i18n";
import { cn, getScrollProgressPercent } from "@/functions/utils";

/**
 *
 * @version 1.0.0
 */
const ScrollProgressBar = ({ scrollProgress }: { scrollProgress: number }) => {
    return (
        <div className={"h-1 w-full bg-neutral-200"}>
            <div
                className={"h-full bg-blue-500"}
                style={{ width: `${Math.min(Math.max(scrollProgress, 5), 100)}%` }}
            >
            </div>
        </div>
    );
};

type FormFooterProps = {
    formHasUnsavedChanges: boolean,
    formIsSubmitting: boolean,
    formHasErrors: boolean,
};

/**
 *
 * @version 1.0.0
 */
const FormFooter = ({ formHasUnsavedChanges, formIsSubmitting, formHasErrors = false }: FormFooterProps) => {
    const [scrollProgressPercent, setScrollProgressPercent] = useState<number>(5);
    const [isFormFooterSticky, setIsFormFooterSticky] = useState<boolean>(false);
    const [isPageScrollable, setIsPageScrollable] = useState<boolean>(document.documentElement.scrollHeight > window.innerHeight);

    const updateScrollProgress = () => {
        setScrollProgressPercent(getScrollProgressPercent());
    };

    useEffect(() => {
        const resizeObserver = new ResizeObserver(() => {
            const isPageScrollableOnResize = document.documentElement.scrollHeight > window.innerHeight;
            setIsPageScrollable(isPageScrollableOnResize);
        });
        resizeObserver.observe(document.documentElement);
        return () => resizeObserver.disconnect();
    }, []);

    useEffect(() => {
        const roundedCornersObserver = new IntersectionObserver(([entry]) => {
            setIsFormFooterSticky(entry.intersectionRatio < 1);
        }, { threshold: [1], rootMargin: "0px 0px -1px 0px" });
        const form = document.getElementById("form-footer");
        if (form) {
            roundedCornersObserver.observe(form);
        }
        return () => roundedCornersObserver.disconnect();
    }, []);

    useEffect(() => {
        updateScrollProgress();
        if (isPageScrollable) {
            window.addEventListener("scroll", updateScrollProgress);
        } else {
            window.removeEventListener("scroll", updateScrollProgress);
        }
        return () => window.removeEventListener("scroll", updateScrollProgress);
    }, [isPageScrollable]);

    // Form states for Design page
    const settingsStates = [
        { condition: formIsSubmitting, message: __("Saving...", "{{TEXT_DOMAIN}}") },
        { condition: formHasErrors, message: __("Form contains errors", "{{TEXT_DOMAIN}}") },
        { condition: formHasUnsavedChanges, message: __("You have unsaved changes", "{{TEXT_DOMAIN}}") },
    ];

    return (
        <div
            id={"form-footer"}
            className={cn(
                "sticky bottom-0 start-0 z-10 shadow-lg bg-gray-50 w-full transition-all ease-in-out duration-200 rounded-none",
                !isFormFooterSticky && "rounded-b-md",
            )}
        >
            {isPageScrollable && <ScrollProgressBar scrollProgress={scrollProgressPercent}/>}
            <FlexContainer direction={"row"} className={"justify-end items-center p-2"}>
                {settingsStates.find(state => state.condition)?.message}
                <Button disabled={(!formHasUnsavedChanges || formIsSubmitting)} type={"submit"} variant={"black"}>
                    {__("Save changes", "{{TEXT_DOMAIN}}")}
                </Button>
            </FlexContainer>
        </div>
    );
};

export { FormFooter };