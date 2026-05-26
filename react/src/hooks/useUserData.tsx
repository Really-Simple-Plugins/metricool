import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation, useQuery } from "@tanstack/react-query";
import { showToast } from "@/components/shared";
import { z } from "zod";
import { __ } from "@wordpress/i18n";
import { queryClient } from "@/main.tsx";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import OnboardingSchema from "@/support/form-schemas/OnboardingSchema.ts";

const userSettingsFormSchema = z.object({
    sendToAlternativeEmail: z.boolean(),
    alternativeEmail: z.email({
        error: () => __("Please enter a valid email address", "metricool"),
    }),
}).required();

/**
 * Hook for retrieving UserData.
 *
 * Contains a {@link useQuery} which fetches the user settings.
 *
 * Contains a {@link useMutation} which updates the user settings and sets the
 * forms error states if updating fails.
 *
 * Contains a {@link useForm} which implements the {@link userSettingsFormSchema}.
 */
const useUserData = () => {
    const { httpClient } = useGlobalContext();

    const connectedBrandsQuery = useQuery({
        queryKey: ["connected_brands"],
        queryFn: () => httpClient.setRoute("connected_brands").get(),
        staleTime: Infinity,
        select: (data): z.infer<typeof OnboardingSchema.shape.brand>[] => data.data,
    });

    const userSettingsDataQuery = useQuery({
        queryKey: ["user_settings"],
        queryFn: () => httpClient.setRoute("user_settings").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (data): z.infer<typeof userSettingsFormSchema> => ({
            sendToAlternativeEmail: data.data.sendToAlternativeEmail,
            alternativeEmail: data.data.alternativeEmail,
        })
    });

    const userSettingsFormData = useForm<z.infer<typeof userSettingsFormSchema>>({
        resolver: zodResolver(userSettingsFormSchema),
        defaultValues: {
            sendToAlternativeEmail: false,
            alternativeEmail: "",
        },
        values: userSettingsDataQuery.data,
    });

    const updateUserSettingsDataMutation = useMutation({
        mutationFn: async ({ sendToAlternativeEmail, alternativeEmail }: z.infer<typeof userSettingsFormSchema>) => {
            return httpClient.setRoute("user_settings").setPayload({
                "sendToAlternativeEmail": sendToAlternativeEmail,
                "alternativeEmail": alternativeEmail,
            }).post();
        },
        onSuccess: (response) => {
            queryClient.setQueryData(["user_settings"], { ...response });
            showToast.success(__("Settings have been saved", "metricool"));
        },
        onError: (data: {
            fields?: Record<keyof z.infer<typeof userSettingsFormSchema>, { message: string }>,
        }) => {
            showToast.error(__("There was an error updating your settings", "metricool"));
            if (data.fields) {
                try {
                    (Object.entries(data.fields) as [keyof z.infer<typeof userSettingsFormSchema>, {
                        message: string
                    }][]).forEach(([fieldKey, fieldContent]) => {
                        userSettingsFormData.setError(fieldKey, {
                            type: "custom",
                            message: fieldContent?.message,
                        });
                    });
                } catch (error) {
                    console.error("There was an error setting the form errors: " + error);
                }
            }
        }
    });

    return {
        connectedBrandsQuery: connectedBrandsQuery,
        userSettingsDataQuery: userSettingsDataQuery,
        userSettingsFormData: userSettingsFormData,
        updateUserSettingsDataMutation: updateUserSettingsDataMutation,
        userSettingsFormSchema: userSettingsFormSchema,
    };
};

export { useUserData };