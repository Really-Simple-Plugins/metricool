import { FontAwesomeIcon, type FontAwesomeIconProps } from "@fortawesome/react-fontawesome";
import { cn } from "@/functions/utils.ts";
import {
    faArrowDown,
    faArrowRight,
    faArrowUp,
    faArrowUpRightFromSquare,
    faCheck,
    faCircle as faSolidCircle,
    faCircleCheck,
    faClose,
    faDash,
    faDownload,
    faFilePdf,
    faMessagesQuestion,
    faRocketLaunch,
    faRss,
    faSparkles,
    faSquareArrowUpRight,
    faTable,
    faMagnifyingGlass,
} from "@fortawesome/pro-solid-svg-icons";
import {
    faChevronDown,
    faChevronLeft,
    faChevronRight,
    faChevronUp,
    faCircle,
    faCircleInfo,
    faCircleSmall,
    faGear,
    faGem,
    faGlobe,
    faOctagonExclamation,
    faPenToSquare,
    faSort,
    faSpinnerThird,
    faTriangleExclamation,
} from "@fortawesome/pro-regular-svg-icons";
import {
    faBluesky,
    faFacebook,
    faInstagram,
    faLinkedin,
    faMeta,
    faPinterest,
    faThreads,
    faTiktok,
    faTwitch,
    faXTwitter,
    faYoutube
} from "@fortawesome/free-brands-svg-icons";

const iconMap = {
    "close": faClose,
    "expand": faChevronDown,
    "collapse": faChevronUp,
    "check": faCheck,
    "sparkle": faSparkles,
    "faq": faMessagesQuestion,
    "file": faFilePdf,
    "download": faDownload,
    "external-link": faArrowUpRightFromSquare,
    "inline-external-link": faSquareArrowUpRight,
    "upsell": faGem,
    "edit": faPenToSquare,
    "settings": faGear,
    "domain": faGlobe,
    "web": faRss,
    "linkedIn": faLinkedin,
    "youtube": faYoutube,
    "twitter": faXTwitter,
    "instagram": faInstagram,
    "facebook": faFacebook,
    "threads": faThreads,
    "pinterest": faPinterest,
    "bluesky": faBluesky,
    "tiktok": faTiktok,
    "twitch": faTwitch,
    "meta": faMeta,
    "up": faArrowUp,
    "down": faArrowDown,
    "left": faChevronLeft,
    "right": faChevronRight,
    "arrow-right": faArrowRight,
    "stable": faDash,
    "dot": faCircleSmall,
    "ring": faCircle,
    "circle": faSolidCircle,
    "sort": faSort,
    "success": faCircleCheck,
    "info": faCircleInfo,
    "warning": faTriangleExclamation,
    "error": faOctagonExclamation,
    "loading": faSpinnerThird,
    "rocket": faRocketLaunch,
    "table": faTable,
    "search": faMagnifyingGlass,
};

const Gbp = ({ className, ...props }: React.ComponentProps<"svg">) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="5.5 5.5 8 8"
        className={className}
        style={{
            height: "20px",
            width: "20px"
        }}
        {...props}
    >
        <path fill="currentColor" d="M13.219 8.198c0 .51-.417.93-.93.93a.932.932 0 0 1-.93-.93c0 .51-.416.93-.93.93a.932.932 0 0 1-.929-.93c0 .51-.416.93-.93.93a.932.932 0 0 1-.93-.93c0 .51-.416.93-.93.93a.932.932 0 0 1-.929-.93l.517-2.015s.108-.402.487-.402h5.43c.379 0 .487.402.487.402l.517 2.015Zm-.372 1.376v2.901c0 .41-.335.744-.744.744H6.897a.746.746 0 0 1-.744-.744v-2.9a1.473 1.473 0 0 0 1.487-.216 1.489 1.489 0 0 0 1.86 0 1.49 1.49 0 0 0 1.86 0 1.48 1.48 0 0 0 1.487.215Zm-.744 1.908c0-.074 0-.152-.018-.234l-.012-.06H10.97v.435h.673a.52.52 0 0 1-.115.231.657.657 0 0 1-.469.19.729.729 0 0 1-.502-.209.687.687 0 0 1 .008-.959c.256-.26.68-.26.948-.011l.052.048.312-.316-.06-.052a1.134 1.134 0 0 0-.773-.301h-.003c-.302 0-.584.115-.796.323a1.11 1.11 0 0 0-.342.792c0 .298.115.573.327.778.216.212.517.338.825.338h.008c.297 0 .561-.108.755-.297.175-.179.286-.447.286-.696h-.001Z"></path>
    </svg>
);

const GoogleAds = ({ className, ...props }: React.ComponentProps<"svg">) => (
    className?.includes("text-white") ? (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 256 256"
            className={className}
            style={{
                height: "20px",
                width: "20px"
            }}
            {...props}
        >
            <g>
                <path d="M5.888,166.405103 L90.88,20.9 C101.676138,27.2558621 156.115862,57.3844138 164.908138,63.1135172 L79.9161379,208.627448 C70.6206897,220.906621 -5.888,185.040138 5.888,166.396276 L5.888,166.405103 Z" fill="#ffffff90"></path>
                <path d="M250.084224,166.401789 L165.092224,20.9055131 C153.210293,1.13172 127.619121,-6.05393517 106.600638,5.62496138 C85.582155,17.3038579 79.182155,42.4624786 91.0640861,63.1190303 L176.056086,208.632961 C187.938017,228.397927 213.52919,235.583582 234.547672,223.904686 C254.648086,212.225789 261.966155,186.175582 250.084224,166.419444 L250.084224,166.401789 Z" fill="currentColor"></path>
                <ellipse fill="currentColor" cx="42.6637241" cy="187.924414" rx="42.6637241" ry="41.6044138"></ellipse>
            </g>
        </svg>
    ) : (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" className={className} style={{
            height: "20px",
            width: "20px"
        }}>
            <g>
                <path d="M5.888,166.405103 L90.88,20.9 C101.676138,27.2558621 156.115862,57.3844138 164.908138,63.1135172 L79.9161379,208.627448 C70.6206897,220.906621 -5.888,185.040138 5.888,166.396276 L5.888,166.405103 Z" fill="#fbbc04"></path>
                <path d="M250.084224,166.401789 L165.092224,20.9055131 C153.210293,1.13172 127.619121,-6.05393517 106.600638,5.62496138 C85.582155,17.3038579 79.182155,42.4624786 91.0640861,63.1190303 L176.056086,208.632961 C187.938017,228.397927 213.52919,235.583582 234.547672,223.904686 C254.648086,212.225789 261.966155,186.175582 250.084224,166.419444 L250.084224,166.401789 Z" fill="#4285f4"></path>
                <ellipse fill="#34a853" cx="42.6637241" cy="187.924414" rx="42.6637241" ry="41.6044138"></ellipse>
            </g>
        </svg>
    ));

export type IconProps = {
    icon: keyof typeof iconMap | "gbp" | "googleAds",
    inverse?: boolean,
} & Omit<FontAwesomeIconProps, "icon" | "size">;

/**
 *
 * @version 1.0.0
 */
const Icon = ({ icon, className, inverse, ...props }: IconProps) => {
    return (<>
        {icon && (
            icon === "gbp" ? (<>
                {/*@ts-expect-error type mismatch - FontAwesome icon props aren't allowed on an svg - will be solved with FA kit*/}
                <Gbp className={className} {...props}/>
            </>) : icon === "googleAds" ? (<>
                {/*@ts-expect-error type mismatch - FontAwesome icon props aren't allowed on an svg - will be solved with FA kit*/}
                <GoogleAds className={className} {...props}/>
            </>) : (
                <FontAwesomeIcon {...(inverse && { inverse: true })} icon={iconMap[icon]} className={cn(icon === "loading" && "animate-spin", className)} {...props} />
            )
        )}
    </>);
};

export { Icon };
