import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";
import { setLocaleData } from "@wordpress/i18n";

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
 * Function to generate reCAPTCHA token.
 * Await the returned promise to be able to get the actual token and use it
 * however needed.
 *
 * @param key
 * @param action
 */
export const generateRecaptchaToken = async (key: string, action: string): Promise<string> => (
    new Promise((resolve, reject) => {
        // @ts-expect-error grecaptcha globally defined through script
        if (typeof grecaptcha === "undefined") {
            return reject("'grecaptcha' is not defined");
        }
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

/**
 * Splits a comma-separated string of tags dropping any empty values.
 * Used to render each tag as a separate badge
 *
 * @param tags
 * @returns string[]
 */
export const parseTags = (tags?: string | null): string[] => {
    return tags?.split(",").filter(Boolean) ?? [];
};

/**
 * Sets the translations for the given text domain.
 * @param jsonTranslations - the WordPress translation strings in JSON format
 * @param textDomain - the text domain for which the translations should be set
 */
export const setTranslations = (jsonTranslations: string[], textDomain: string) => {
    jsonTranslations.forEach((translationString: string) => {
        try {
            const localeData = JSON.parse(translationString).locale_data?.messages ?? null;
            if (!localeData) {
                return;
            }
            setLocaleData(localeData, textDomain);
        } catch (error) {
            console.log("Error while loading translations");
            console.error(error);
        }
    });
};