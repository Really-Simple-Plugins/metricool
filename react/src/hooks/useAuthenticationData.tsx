import { type MetricoolData, useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation } from "@tanstack/react-query";
import { showToast } from "@/components/shared";
import { z } from "zod";
import { generateRecaptchaToken } from "@/support/functions/utils.ts";
import OnboardingSchema from "@/support/form-schemas/OnboardingSchema.ts";

type AuthenticationDataProps = {
    reloadOnLogout?: boolean,
    logoutCallback?: () => void,
    signUpCallbacks?: {
        beforeSignUpCallback: () => void,
        onSignUpSuccessCallback: (onboarding: MetricoolData["onboarding"]) => void,
        onSignUpErrorCallback: () => void,
    }
}

/**
 * Hook for retrieving all data related to User Authentication.
 *
 * Contains a {@link useMutation} to make the sign-up request.
 */
const useAuthenticationData = ({ reloadOnLogout, logoutCallback, signUpCallbacks }: AuthenticationDataProps = {}) => {
    const { httpClient, dispatch, metricool } = useGlobalContext();

    const signUpMutation = useMutation({
        onMutate: () => {
            signUpCallbacks?.beforeSignUpCallback();
        },
        mutationFn: async (formValues: Omit<z.infer<typeof OnboardingSchema>, "brand">) => {
            const token = await generateRecaptchaToken(metricool.google_recaptcha_key, "signup");

            return await httpClient.setRoute("onboarding/create_account").setPayload({
                email: formValues.credentials.email,
                password: formValues.credentials.password,
                marketing: formValues.marketing,
                captcha: token,
                terms: formValues.terms,
            }).post();
        },
        onSuccess: async (response) => {
            signUpCallbacks?.onSignUpSuccessCallback(response.data.onboarding);
        },
        onError: (error) => {
            signUpCallbacks?.onSignUpErrorCallback();
            console.error(error);
        }
    });

    const finishOnboardingMutation = useMutation({
        mutationFn: async (formValues: z.infer<typeof OnboardingSchema.shape.brand>) => {
            return httpClient.setRoute("onboarding/finish_onboarding").setPayload({
                blogId: formValues.id,
            }).post();
        },
        onSuccess: (response) => {
            dispatch({
                dispatchType: "setOnboardingState",
                change: { metricool: { onboarding: { ...response.data.onboarding } } }
            });
        },
        onError: (error) => {
            console.error(error);
        }
    });

    const signInRedirectUrlMutation = useMutation({
        mutationFn: async () => {
            return httpClient.setRoute("onboarding/oauth_redirect").get();
        },
        onSuccess: (response) => {
            window.location = response.data.redirect_url;
        },
        onError: (error) => {
            console.error(error);
        }
    });

    const logoutMutation = useMutation({
        mutationFn: async () => {
            return httpClient.setRoute("logout").post();
        },
        onSuccess: (response) => {
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
        signUpMutation: signUpMutation,
        finishOnboardingMutation: finishOnboardingMutation,
        signInRedirectUrlMutation: signInRedirectUrlMutation,
        logoutMutation: logoutMutation
    };
};

export { useAuthenticationData };