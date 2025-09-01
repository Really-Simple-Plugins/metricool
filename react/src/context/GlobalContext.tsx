import React, {
    createContext,
    useContext,
    useReducer,
    useEffect,
    type Dispatch,
} from 'react';

interface GlobalContext {
    globalState: GlobalState,
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
        // @ts-expect-error same as above
        // setting to undefined so it is no longer accessible in the devtools
        window.metricool = undefined;
    }, []);

    return (
        <GlobalContext.Provider
            value={{ globalState, dispatch }}
        >
            {children}
        </GlobalContext.Provider>
    );
};

type PartialGlobalState = Partial<GlobalState>;

interface ReducerAction {
    dispatchType: string,
    change: PartialGlobalState,
}

const globalStateReducer = (state: GlobalState, action: ReducerAction): GlobalState => {
    switch (action.dispatchType) {
        case 'setMetricoolVariables': {
            return { ...state, metricool: { ...action.change.metricool } };
        }
        default: {
            throw new Error('Unknown action: ' + action.dispatchType);
        }
    }
};

