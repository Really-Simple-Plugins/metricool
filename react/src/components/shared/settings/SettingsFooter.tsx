import { useCallback, useEffect, useRef, useState } from "react";
import { cn } from "@/support/functions/utils";

/**
 *
 * @version 1.0.0
 */
const ScrollProgressBar = () => {

    const componentRef = useRef<HTMLDivElement>(null);

    const currentScrollYValue = useRef(0);
    const framePending = useRef(false);
    const minimalProgress = 0.05;

    const updateScrollProgress = () => {
        framePending.current = false;

        const totalPossibleScrollValueInPixels = document.documentElement.scrollHeight - window.innerHeight;

        const normalisedProgressPercentage = currentScrollYValue.current / totalPossibleScrollValueInPixels;
        const clampedNormalisedProgressPercentage = (totalPossibleScrollValueInPixels > 0 && normalisedProgressPercentage > minimalProgress)
            ? Math.min(normalisedProgressPercentage, 1)
            : minimalProgress;

        if (componentRef.current) {
            componentRef.current.style.transform = `scaleX(${clampedNormalisedProgressPercentage})`;
        }
    }

    const onScroll = useCallback(() => {
        currentScrollYValue.current = window.scrollY;

        if (!framePending.current) {
            framePending.current = true;
            requestAnimationFrame(updateScrollProgress);
        }
    }, []);

    useEffect(() => {
        window.addEventListener('scroll', onScroll, { passive: true });

        return () => window.removeEventListener('scroll', onScroll);
    }, [onScroll]);

    return (
        <div className={"h-1 w-full bg-neutral-200"}>
            <div
                ref={componentRef}
                className={"h-full bg-blue-500 origin-left transition-all ease-in duration-[50]"}
                style={{ transform: `scaleX(${minimalProgress})` }}
            >
            </div>
        </div>
    );
};

/**
 *
 * @version 1.0.0
 */
const SettingsFooter = ({ children } : React.ComponentProps<"div">) => {
    const [isFormFooterSticky, setIsFormFooterSticky] = useState<boolean>(false);
    const [isPageScrollable, setIsPageScrollable] = useState<boolean>(document.documentElement.scrollHeight > window.innerHeight);

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

    return (
        <div
            id={"form-footer"}
            className={cn(
                "sticky min-h-12.5 bottom-0 inset-s-0 z-10 shadow-lg bg-gray-50 w-full transition-all ease-in-out duration-200 rounded-none",
                {
                    "rounded-b-md": !isFormFooterSticky
                },
            )}
        >
            {isPageScrollable && (
                <ScrollProgressBar />
            )}
            {children}
        </div>
    );
};

export { SettingsFooter };