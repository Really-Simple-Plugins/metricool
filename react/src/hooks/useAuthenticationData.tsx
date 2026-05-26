import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation } from "@tanstack/react-query";
import { showToast } from "@/components/shared";

const useAuthenticationData = () => {
    const { httpClient, dispatch } = useGlobalContext();

    const logoutMutation = useMutation({
        mutationFn: async () => {
            return httpClient.setRoute("logout").post();
        },
        onSuccess: (response) => {
            console.log(response);
            dispatch({
                dispatchType: "setOnboardingState",
                change: {
                    metricool: {
                        onboarding: {
                            state: {
                                completed: false,
                                authenticated: false,
                                blog_id_selected: false
                            },
                            mode: {
                                show_welcome_screen: false,
                                forced_login: false,
                            }
                        }
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