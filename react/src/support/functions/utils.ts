import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

/**
 * Function defined by shadcn, should not be renamed.
 * Combines {@link clsx} with {@link twMerge}.
 * Returns one string of classes, taking conditional logic into account,
 * with no duplicates.
 * @param inputs
 */
export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

/**
 * Function to get the rounded ScrollProgressPercent as an integer.
 * Used in the {@link FormFooter} to set the width of the progress bar.
 */
export const getScrollProgressPercent = () => {
    const totalScrollableHeightInPixels =
        document.documentElement.scrollHeight - window.innerHeight;
    const roundedScrollPercentage =
        Math.round((Math.ceil(window.scrollY) / totalScrollableHeightInPixels) * 100);
    return roundedScrollPercentage;
};

/**
 * Capitalizes first character of the given string.
 * @param string
 * @returns string
 */
export const capitalizeFirstCharacter = (string: string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
};

/**
 * Checks if the first character of the given string is a vowel.
 * Not case-sensitive.
 * Used in Metricool for the Instagram AccountTile to render "an" as an article
 * instead of "a".
 * @param string
 * @returns boolean
 */
export const isFirstCharacterAVowel = (string: string) => {
    return (/^[aeiou]$/i).test(string.charAt(0));
};

/**
 * Used in {@link FieldWrapper} to turn the name prop into a hyphenated string
 * used as the "id" and "htmlFor".
 * Inserts a hyphen before each character if it is an uppercase letter, then inserts
 * that char as a lowercase character. Lastly replaces all full stops with
 * hyphens too.
 * @param string
 * @returns string
 */
export const camelCaseToHyphenated = (string: string) => {
    let hyphenatedString = "";
    for (let i = 0; i < string.length; i++) {
        if (i != 0 && /^[A-Z]*$/.test(string.charAt(i))) {
            hyphenatedString += "-";
        }
        hyphenatedString += string.charAt(i).toLowerCase();
    }
    return hyphenatedString.replace(".", "-");
};

/**
 *
 * @param key
 * @param action
 */
export const generateRecaptchaToken = async (key: string, action: string): Promise<string> => (
    new Promise((resolve) => {
        // @ts-expect-error grecaptcha globally defined through script
        grecaptcha.enterprise.ready(
            () =>
                void (async () => {
                    // @ts-expect-error grecaptcha globally defined through script
                    const token = await grecaptcha.enterprise.execute(key, { action: action });
                    resolve(token);
                })(),
        );
    })
);