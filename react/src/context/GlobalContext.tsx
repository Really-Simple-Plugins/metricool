import React, {
    createContext,
    useContext,
    useReducer,
    useEffect,
    type Dispatch,
} from 'react';
import HttpClient from "../api/HttpClient.tsx";

interface GlobalContext {
    globalState: GlobalState,
    httpClient: GlobalState["httpClient"],
    dispatch: Dispatch<ReducerAction>,
}


interface GlobalState {
    metricool: {
        nonce?: string,
        x_wp_nonce?: string,
        ajax_url?: string,
        rest_url?: string,
        rest_namespace?: string,
        rest_version?: string,
        site_url?: string,
        assets_url?: string,
        json_translations?: string[],
        is_onboarding_completed?: boolean,
        support?: string | null,
    };
    httpClient: HttpClient | null,
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
    metricool: {},
    httpClient: null,
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
        // @ts-expect-error the metricool variable is globally set in the DashboardController
        // but the tsc complains it can't find it
        dispatch({ dispatchType: 'setMetricoolVariables', change: { metricool: { ...window.metricool.values } } });
        dispatch({ dispatchType: 'initialiseHttpClient'});

        // @ts-expect-error same as above
        // setting to undefined so it is no longer accessible in the devtools
        window.metricool = undefined;
    }, []);

    return (
        <GlobalContext.Provider
            value={{ globalState, httpClient: globalState.httpClient, dispatch }}
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
        case 'setMetricoolVariables': {
            if (!action.change) {
                throw new Error("No new values provided");
            }
            if (!action.change.metricool){
                return {...state}
            }
            return { ...state, metricool: { ...action.change.metricool, is_onboarding_completed: true } };
        }
        case 'initialiseHttpClient': {
            if (state.metricool.rest_url && state.metricool.rest_namespace && state.metricool.rest_version && state.metricool.x_wp_nonce && state.metricool.nonce){
                const MC_API_URL = state.metricool.rest_url + state.metricool.rest_namespace + "/" + state.metricool.rest_version + "/";
                const httpClient: HttpClient = new HttpClient({
                    NONCE: state.metricool.nonce,
                    X_WP_NONCE: state.metricool.x_wp_nonce,
                    MC_API_URL: MC_API_URL
                })
                return { ...state, httpClient: httpClient };
            }
            return {...state}
        }
        default: {
            throw new Error('Unknown action: ' + action.dispatchType);
        }
    }
};

