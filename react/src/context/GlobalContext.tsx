import React, { createContext, type Dispatch, useContext, useEffect, useReducer, } from "react";
import HttpClient from "@/api/HttpClient.tsx";
import { setLocaleData } from "@wordpress/i18n";
import type { PeriodFilterOption } from "@/components/custom";
import DynamicUrl from "@/support/helpers/DynamicUrl.tsx";

// @ts-expect-error the metricool variable is globally set in the DashboardController
// but the tsc complains it can't find it
const METRICOOL_DATA: MetricoolData = window.metricool.values;
const METRICOOL_API_URL = METRICOOL_DATA.api_url;

export interface GlobalContext {
    globalState: GlobalState,
    metricool: MetricoolData,
    httpClient: GlobalState["httpClient"],
    dispatch: Dispatch<ReducerAction>,
    dashboardSettings: GlobalState["dashboardSettings"],
    metricoolDynamicUrl: GlobalState["metricoolDynamicUrl"],
}

export type MetricoolData = {
    nonce: string,
    x_wp_nonce: string,
    ajax_url: string,
    rest_url: string,
    rest_namespace: string,
    rest_version: string,
    api_url: string,
    dashboard_url: string,
    site_url: string,
    assets_url: string,
    json_translations: string[],
    trusted_urls: {
        legal_terms: string,
        new_support_ticket: string,
        google_privacy_policy_url: string,
        google_terms_url: string,
        base_url: string,
    },
    onboarding: {
        state: {
            completed: boolean,
            authenticated: boolean,
            blog_id_selected: boolean
        },
        mode: {
            show_welcome_screen: boolean,
            forced_login: boolean,
        }
    }
    support: string,
    metricool_base_url: string,
    metricool_help_url: string,
    locale: string,
    account?: {
        blog_id: string,
        user_id: string,
        is_premium: boolean,
        user: {
            id: number,
            mail: string,
            language: string,
            name: string,
            lastName: string,
            timezone: string,
        }
    },
    google_recaptcha_url: string,
    google_recaptcha_key: string,
};

interface GlobalState {
    metricool: MetricoolData;
    httpClient: HttpClient;
    dashboardSettings: {
        activePeriodFilter?: PeriodFilterOption,
        activeWebsiteAnalyticsTab?: number,
        activeProgressTab?: number,
    };
    metricoolDynamicUrl: DynamicUrl,
}

/**
 * Context for global variables.
 * Used by the whole app to access the global state.
 */
const GlobalContext = createContext<GlobalContext | null>(null);

/**
 * Hook to access the Global context.
 * @returns The Global context value
 */
export const useGlobalContext = () => {
    const context = useContext(GlobalContext);
    if (!context) {
        throw Error("useGlobalContext can only be used by child components inside the GlobalContextProvider");
    }
    return context;
};

const initialGlobalState: GlobalState = {
    metricool: METRICOOL_DATA,
    httpClient: new HttpClient({
        NONCE: METRICOOL_DATA.nonce,
        X_WP_NONCE: METRICOOL_DATA.x_wp_nonce,
        METRICOOL_API_URL: METRICOOL_API_URL,
    }),
    dashboardSettings: {},
    metricoolDynamicUrl: new DynamicUrl({
        baseUrl: METRICOOL_DATA.metricool_base_url,
    }).setSearchParams({
        ...(METRICOOL_DATA.account?.blog_id && { blogId: METRICOOL_DATA.account.blog_id }),
        ...(METRICOOL_DATA.account?.user_id && { userId: METRICOOL_DATA.account.user_id }),
    }),
};

const setTranslations = () => {
    METRICOOL_DATA.json_translations.forEach((translationString: string) => {
        try {
            const translations = JSON.parse(translationString);
            const localeData = translations.locale_data?.metricool;
            if (!localeData) {
                return;
            }
            localeData[""].domain = "metricool";
            setLocaleData(localeData, "metricool");
        } catch (error) {
            console.error(error);
        }
    });
};

/**
 * The main Global Context Component
 * Gives its children access to all items specified in the GlobalContext interface
 * @returns The Context Provider component
 */
export const GlobalContextProvider = ({ children }: { children: React.ReactNode }) => {
    const [globalState, dispatch] = useReducer(
        globalStateReducer,
        initialGlobalState
    );

    useEffect(() => {
        setTranslations();

        const metricoolScriptElement = document.querySelector("script[id='metricool-main-script-js-extra']");
        if (metricoolScriptElement) {
            metricoolScriptElement.remove();
        }
        // @ts-expect-error same as above
        // setting to undefined so it is no longer accessible in the browser devtools console
        window.metricool = undefined;
    }, []);

    return (
        <GlobalContext.Provider
            value={{
                globalState,
                metricool: globalState.metricool,
                httpClient: globalState.httpClient,
                dispatch,
                dashboardSettings: globalState.dashboardSettings,
                metricoolDynamicUrl: globalState.metricoolDynamicUrl,
            }}
        >
            {children}
        </GlobalContext.Provider>
    );
};

type PartialGlobalState = Partial<Omit<GlobalState, "metricool"> & { metricool: Partial<MetricoolData> }>;

interface ReducerAction {
    dispatchType: "setOnboardingState" | "resetAccountData" | "setDashboardModalClosed" | "setDashboardSetting",
    change?: PartialGlobalState,
}

const globalStateReducer = (state: GlobalState, action: ReducerAction): GlobalState => {
    switch (action.dispatchType) {
        case "setOnboardingState": {
            return {
                ...state,
                ...(action?.change?.metricool && {
                    metricool: {
                        ...state.metricool,
                        ...(action?.change?.metricool.onboarding && { onboarding: { ...action?.change?.metricool.onboarding } })
                    }
                })
            };
        }
        case "resetAccountData": {
            const metricool = state.metricool;
            delete metricool.account;
            return {
                ...state,
                metricool: metricool,
            }
        }
        case "setDashboardModalClosed": {
            return {
                ...state,
                metricool: {
                    ...state.metricool,
                    onboarding: {
                        ...state.metricool.onboarding,
                        mode: { ...state.metricool.onboarding.mode, show_welcome_screen: false }
                    }
                }
            };
        }
        case "setDashboardSetting": {
            if (!action.change) {
                throw new Error("No new values provided");
            }
            return {
                ...state,
                dashboardSettings: {
                    ...state.dashboardSettings,
                    ...action?.change?.dashboardSettings
                },
            };
        }
        default: {
            throw new Error("Unknown action: " + action.dispatchType);
        }
    }
};