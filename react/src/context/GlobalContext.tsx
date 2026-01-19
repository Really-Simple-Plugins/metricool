import React, { createContext, type Dispatch, useContext, useEffect, useReducer, } from "react";
import HttpClient from "../api/HttpClient.tsx";
import { setLocaleData } from "@wordpress/i18n";
import type { PeriodFilterOption } from "../custom/AnalyticsTab.tsx";
import DynamicUrl from "../helpers/DynamicUrl.tsx";

// @ts-expect-error the metricool variable is globally set in the DashboardController
// but the tsc complains it can't find it
const METRICOOL_DATA = window.metricool.values;
const MC_API_URL = METRICOOL_DATA.rest_url + METRICOOL_DATA.rest_namespace + "/" + METRICOOL_DATA.rest_version + "/";
// @ts-expect-error same as above
// setting to undefined so it is no longer accessible in the browser devtools console
window.metricool = undefined;

interface GlobalContext {
    globalState: GlobalState,
    metricool: typeof defaultMetricoolData,
    httpClient: GlobalState["httpClient"],
    dispatch: Dispatch<ReducerAction>,
    dashboardSettings: GlobalState["dashboardSettings"],
    metricoolDynamicUrl: GlobalState["metricoolDynamicUrl"],
}

const defaultMetricoolData = {
    nonce: "",
    x_wp_nonce: "",
    ajax_url: "",
    rest_url: "",
    rest_namespace: "",
    rest_version: "",
    site_url: "",
    assets_url: "",
    json_translations: [],
    trusted_urls: {
        legal_terms: "",
        new_support_ticket: "",
    },
    is_onboarding_completed: false,
    was_dashboard_modal_closed: false,
    support: "",
    metricool_base_url: "",
    metricool_help_url: "",
    locale: "",
    blogId: "",
    userId: "",
};

interface GlobalState {
    metricool: typeof defaultMetricoolData;
    httpClient: HttpClient;
    dashboardSettings: {
        analytics?: {
            activePeriodFilter?: PeriodFilterOption,
        }
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
    metricool: defaultMetricoolData,
    httpClient: new HttpClient({
        NONCE: METRICOOL_DATA.nonce,
        X_WP_NONCE: METRICOOL_DATA.x_wp_nonce,
        MC_API_URL: MC_API_URL,
    }),
    dashboardSettings: {},
    metricoolDynamicUrl: new DynamicUrl({
        baseUrl: METRICOOL_DATA.metricool_base_url,
    }).setSearchParams({
        blogId: METRICOOL_DATA.blogId,
        userId: METRICOOL_DATA.userId,
    }),
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
        dispatch({ dispatchType: "setMetricoolVariables", change: { metricool: { ...METRICOOL_DATA } } });
        dispatch({ dispatchType: "setTranslations" });
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

type PartialGlobalState = Partial<GlobalState>;

interface ReducerAction {
    dispatchType: string,
    change?: PartialGlobalState,
}

const globalStateReducer = (state: GlobalState, action: ReducerAction): GlobalState => {
    switch (action.dispatchType) {
        case "setMetricoolVariables": {
            if (!action.change) {
                throw new Error("No new values provided");
            }
            if (!action.change.metricool) {
                return { ...state };
            }
            return { ...state, metricool: { ...action.change.metricool } };
        }
        case "setOnboardingComplete": {
            return { ...state, metricool: { ...state.metricool, is_onboarding_completed: true } };
        }
        case "setDashboardModalClosed": {
            return { ...state, metricool: { ...state.metricool, was_dashboard_modal_closed: true } };
        }
        case "setTranslations": {
            if (!state.metricool) {
                throw new Error("No metricool data");
            }
            state.metricool.json_translations.forEach((translationString) => {
                const translations = JSON.parse(translationString);
                const localeData = translations.locale_data?.metricool;
                if (!localeData) {
                    return;
                }
                localeData[""].domain = "metricool";
                setLocaleData(localeData, "metricool");
            });
            return { ...state };
        }
        case "setDashboardSetting": {
            if (!action.change) {
                throw new Error("No new values provided");
            }
            return {
                ...state,
                dashboardSettings: { ...state.dashboardSettings, ...action?.change?.dashboardSettings },
            };
        }
        default: {
            throw new Error("Unknown action: " + action.dispatchType);
        }
    }
};

