import { z } from "zod";
import { __ } from "@wordpress/i18n";

const OnboardingSchema = z.object({
    brand: z.string(),
    credentials: z.object({
        email: z.email({
            error: () => __("Please enter a valid email address", "metricool"),
        }),
        password: z.string().min(8, {
            error: () => __("Password must be at least 8 characters", "metricool"),
        }),
    }),
    terms: z.boolean().refine((val) => val === true, {
        error: () => __("Please read and accept the Legal Terms", "metricool"),
    }),
    marketing: z.boolean(),
}).required();

export default OnboardingSchema;