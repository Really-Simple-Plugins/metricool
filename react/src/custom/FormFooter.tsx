import { clsx } from "clsx";
import FlexContainer from "./FlexContainer.tsx";
import { Button } from "../components";
import { useEffect, useState } from "react";
import ScrollProgressBar from "./ScrollProgressBar.tsx";

const FormFooter = () => {
    const [scrollProgressPercent, setScrollProgressPercent] = useState<number>(5);
    const [isPageScrollable, setIsPageScrollable] = useState<boolean>(document.documentElement.scrollHeight > window.innerHeight);

    const updateScrollProgress = () => {
        const totalScrollableHeightInPixels =
            document.documentElement.scrollHeight - window.innerHeight;
        const roundedScrollPercentage =
            Math.round((window.scrollY / totalScrollableHeightInPixels) * 100);
        setScrollProgressPercent(roundedScrollPercentage);
    };

    useEffect(() => {
        const observer = new ResizeObserver(() => {
            const isPageScrollableOnResize = document.documentElement.scrollHeight > window.innerHeight;
            setIsPageScrollable(isPageScrollableOnResize);
        });
        observer.observe(document.documentElement);
        return () => observer.disconnect();
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

    return (
        <div className={clsx("sticky bottom-0 start-0 z-10 shadow-md bg-gray-50 w-full transition-all ease-in-out duration-200 rounded-none",
            (!isPageScrollable || scrollProgressPercent >= 88) && "rounded-b-md",
        )}>
            {isPageScrollable && <ScrollProgressBar scrollProgress={scrollProgressPercent}/>}
            <FlexContainer direction={"row"} className={"justify-end items-center p-2"}>
                <Button type={"submit"} variant={"black"}>Save changes</Button>
            </FlexContainer>
        </div>
    );
};

export default FormFooter;