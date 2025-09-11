export const capitalizeFirstCharacter = (string: string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
};

export const getScrollProgressPercent = () => {
    const totalScrollableHeightInPixels =
        document.documentElement.scrollHeight - window.innerHeight;
    const roundedScrollPercentage =
        Math.round((window.scrollY / totalScrollableHeightInPixels) * 100);
    return roundedScrollPercentage;
};