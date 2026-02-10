import { z } from "zod";
import { __ } from "@wordpress/i18n";

const OnboardingSchema = z.object({
    brand: z.string(),
    credentials: z.object({
        email: z.email({
            error: () => __("Please enter a valid email address", "metricool"),
        }),
        password: z.string()
            .min(8, {
                error: () => __("Password must be at least 8 characters", "metricool"),
            })
            .max(20, {
                error: () => __("Password cannot be longer than 20 characters", "metricool"),
            })
            .refine((password) => /[A-Z]/.test(password), {
                error: () => __("Password must contain at least one upper case letter", "metricool"),
            })
            .refine((password) => /[a-z]/.test(password),{
                error: () => __("Password must contain at least one lower case letter", "metricool"),
            })
            .refine((password) => /[0-9]/.test(password), {
                error: () => __("Password must contain at least one number", "metricool"),
            })
            .refine((password) => /[!?@#$%^&*]/.test(password), {
                error: () => __("Password must contain at least one special character", "metricool"),
            })
        ,
    }),
    terms: z.boolean().refine((val) => val === true, {
        error: () => __("Please read and accept the Legal Terms", "metricool"),
    }),
    marketing: z.boolean(),
}).required();

export default OnboardingSchema;