import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation } from "@tanstack/react-query";
import { showToast } from "@/components/shared";

type AuthenticationDataProps = {
    reloadOnLogout?: boolean,
    logoutCallback?: () => void,
}

const useAuthenticationData = ({ reloadOnLogout, logoutCallback }: AuthenticationDataProps = {}) => {
    const { httpClient, dispatch } = useGlobalContext();

    const logoutMutation = useMutation({
        mutationFn: async () => {
            return httpClient.setRoute("logout").post();
        },
        onSuccess: (response) => {
            console.log(response);
            if (logoutCallback) {
                logoutCallback();
            }
            if (reloadOnLogout) {
                window.location.reload();
            }
            dispatch({
                dispatchType: "setOnboardingState",
                change: {
                    metricool: {
                        onboarding: response.data
                    }
                }
            });
        },
        onError: (error) => {
            console.error(error);
            showToast.error(error.message);
        }
    });

    return {
        logoutMutation: logoutMutation
    };
};

export { useAuthenticationData };