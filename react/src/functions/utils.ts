import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export const getScrollProgressPercent = () => {
    const totalScrollableHeightInPixels =
        document.documentElement.scrollHeight - window.innerHeight;
    const roundedScrollPercentage =
        Math.round((Math.ceil(window.scrollY) / totalScrollableHeightInPixels) * 100);
    return roundedScrollPercentage;
};

export const capitalizeFirstCharacter = (string: string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
};

export const isFirstCharacterAVowel = (string: string) => {
    return (/^[aeiou]$/i).test(string.charAt(0));
};

export const camelCaseToHyphenated = (string: string) => {
    let hyphenatedString = "";
    for (let i = 0; i < string.length; i++) {
        if (i != 0 && /^[A-Z]*$/.test(string.charAt(i))) {
            hyphenatedString += "-";
        }
        hyphenatedString += string.charAt(i).toLowerCase();
    }
    return hyphenatedString.replace(".", "-");
}