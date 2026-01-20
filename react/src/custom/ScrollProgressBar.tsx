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

export default ScrollProgressBar;
