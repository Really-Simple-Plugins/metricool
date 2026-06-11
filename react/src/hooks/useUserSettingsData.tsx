import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation, useQuery } from "@tanstack/react-query";
import { showToast } from "@/components/shared";
import { z } from "zod";
import { __ } from "@wordpress/i18n";
import { queryClient } from "@/main.tsx";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import UserSettingsSchema from "@/support/form-schemas/UserSettingsSchema.ts";

/**
 * Hook for retrieving User Settings.
 *
 * Contains a {@link useQuery} which fetches the user settings.
 *
 * Contains a {@link useMutation} which updates the user settings and sets the
 * forms error states if updating fails.
 *
 * Contains a {@link useForm} which implements the {@link UserSettingsSchema}.
 */
const useUserSettingsData = () => {
    const { httpClient } = useGlobalContext();

    const userSettingsDataQuery = useQuery({
        queryKey: ["user_settings"],
        queryFn: () => httpClient.setRoute("user_settings").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
        select: (response): z.infer<typeof UserSettingsSchema> => ({
            sendToAlternativeEmail: response.data.sendToAlternativeEmail,
            alternativeEmail: response.data.alternativeEmail ?? "",
        })
    });

    const userSettingsFormData = useForm<z.infer<typeof UserSettingsSchema>>({
        resolver: zodResolver(UserSettingsSchema),
        defaultValues: {
            sendToAlternativeEmail: false,
            alternativeEmail: "",
        },
        values: userSettingsDataQuery.data,
    });

    const updateUserSettingsDataMutation = useMutation({
        mutationFn: async ({ sendToAlternativeEmail, alternativeEmail }: z.infer<typeof UserSettingsSchema>) => {
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
            fields?: Record<keyof z.infer<typeof UserSettingsSchema>, { message: string }>,
        }) => {
            showToast.error(__("There was an error updating your settings", "metricool"));
            if (data.fields) {
                try {
                    (Object.entries(data.fields) as [keyof z.infer<typeof UserSettingsSchema>, {
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
        userSettingsDataQuery: userSettingsDataQuery,
        userSettingsFormData: userSettingsFormData,
        updateUserSettingsDataMutation: updateUserSettingsDataMutation,
    };
};

export { useUserSettingsData };