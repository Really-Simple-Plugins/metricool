import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";
import { z } from "zod";
import OnboardingSchema from "@/support/form-schemas/OnboardingSchema.ts";

/**
 * Hook for retrieving Connected Brands.
 *
 */
const useConnectedBrandsData = () => {
    const { httpClient } = useGlobalContext();

    const connectedBrandsQuery = useQuery({
        queryKey: ["connected_brands"],
        queryFn: () => httpClient.setRoute("connected_brands").get(),
        staleTime: Infinity,
        select: (response): z.infer<typeof OnboardingSchema.shape.brand>[] => response.data,
    });

    return {
        connectedBrandsQuery: connectedBrandsQuery,
    };
};

export { useConnectedBrandsData };