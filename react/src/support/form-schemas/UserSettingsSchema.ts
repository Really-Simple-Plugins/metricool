import { z } from "zod";
import { __ } from "@wordpress/i18n";

const userSettingsFormSchema = z.object({
    sendToAlternativeEmail: z.boolean(),
    alternativeEmail: z.email({
        error: () => __("Please enter a valid email address", "metricool"),
    }),
}).required();

export default userSettingsFormSchema;