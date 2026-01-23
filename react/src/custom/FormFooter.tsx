import { clsx } from "clsx";
import { Button, FlexContainer } from "../components";
import { useEffect, useState } from "react";
import ScrollProgressBar from "./ScrollProgressBar.tsx";
import { __ } from "@wordpress/i18n";
import { getScrollProgressPercent } from "../functions/utils.tsx";

type FormFooterProps = {
    formHasUnsavedChanges: boolean,
    formIsSubmitting: boolean,
    formHasErrors: boolean,
};

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
        { condition: formIsSubmitting, message: __("Saving...", "metricool") },
        { condition: formHasErrors, message: __("Form contains errors", "metricool") },
        { condition: formHasUnsavedChanges, message: __("You have unsaved changes", "metricool") },
    ];

    return (
        <div
            id={"form-footer"}
            className={clsx(
                "sticky bottom-0 start-0 z-10 shadow-lg bg-gray-50 w-full transition-all ease-in-out duration-200 rounded-none",
                !isFormFooterSticky && "rounded-b-md",
            )}
        >
            {isPageScrollable && <ScrollProgressBar scrollProgress={scrollProgressPercent}/>}
            <FlexContainer direction={"row"} className={"justify-end items-center p-2"}>
                {settingsStates.find(state => state.condition)?.message}
                <Button disabled={(!formHasUnsavedChanges || formIsSubmitting)} type={"submit"} variant={"black"}>
                    {__("Save changes", "metricool")}
                </Button>
            </FlexContainer>
        </div>
    );
};

export default FormFooter;