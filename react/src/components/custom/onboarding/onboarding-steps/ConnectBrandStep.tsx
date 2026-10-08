import {
    Alert,
    Button,
    DialogHeader,
    DialogTitle,
    EmptyDataState,
    FieldWrapper,
    FlexContainer,
    Icon,
    Select,
    SelectOption,
} from "@/components/shared";
import { __ } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { clsx } from "clsx";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import OnboardingSchema from "@/support/form-schemas/OnboardingSchema.ts";
import DOMPurify from "dompurify";
import { useAuthenticationData } from "@/hooks/useAuthenticationData";
import { type Dispatch, type SetStateAction } from "react";
import { queryClient } from "@/main.tsx";
import { useConnectedBrandsData } from "@/hooks/useConnectedBrandsData.tsx";
import { createInterpolateElement } from "@wordpress/element";

type ConnectBrandStepProps = {
    setModalOpen?: Dispatch<SetStateAction<boolean>>,
    resetSignInSteps?: () => void,
}

const ConnectBrandStep = ({ setModalOpen, resetSignInSteps }: ConnectBrandStepProps) => {
    const { metricool } = useGlobalContext();

    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof OnboardingSchema.shape.brand>>({
        resolver: zodResolver(OnboardingSchema.shape.brand),
        defaultValues: {
            id: "",
        },
    });

    const {
        connectedBrandsQuery: {
            data: connectedBrands,
            isLoading,
            errorUpdateCount,
            error,
            refetch,
        }
    } = useConnectedBrandsData();

    const {
        logoutMutation: { mutate: logoutUser },
        finishOnboardingMutation: { mutate: onSubmit, error: submitError },
    } = useAuthenticationData({
        ...(setModalOpen && { logoutCallback: () => setModalOpen(false) }),
        ...(!resetSignInSteps && { reloadOnLogout: true })
    });

    const onCancel = () => {
        logoutUser();
        // For some reason adding clear() to the onSuccess for the logout mutation
        // reloads the page, so adding it here.
        queryClient.getQueryCache().clear();
        if (resetSignInSteps) {
            resetSignInSteps();
        }
    };

    const missingBrandNotice = createInterpolateElement(
        /* translators: Please include the opening and closing <a> tags in your translation */
        __("Don’t see the brand you’re looking for? <a>Go to Metricool.com</a> and make sure you’re logged into the correct account.", "metricool"),
        {
            a: <a className={"underline"} onClick={onCancel} href={metricool.trusted_urls.base_url} target={"_blank"} rel={"noopener noreferrer"}/>,
        }
    );

    return (
        <FlexContainer direction={"column"} className={"justify-center gap-6! items-center"}>
            <FlexContainer direction={"column"} className={"w-full gap-2!"}>
                <DialogHeader className={"justify-center items-center"}>
                    <img src={`${metricool.assets_url}img/onboarding-connect-brand.svg`} alt={__("Link icon", "metricool")}/>
                    <DialogTitle className={"font-bold font-nunito m-0 text-2xl"}>
                        {__("Connect your brand", "metricool")}
                    </DialogTitle>
                </DialogHeader>
                <div className={"text-base text-center"}>
                    {__("Choose the brand that you want to connect to this website", "metricool")}
                </div>
                <Alert
                    variant={"info"}
                    className={"text-left"}
                >
                    <div>{missingBrandNotice}</div>
                </Alert>
            </FlexContainer>
            {submitError && (
                <Alert variant={"error"}>
                    <div
                        dangerouslySetInnerHTML={{
                            __html: DOMPurify.sanitize(submitError.message, { ADD_ATTR: ["target"] })
                        }}
                    />
                </Alert>
            )}
            <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col items-center justify-center gap-6 w-full"}>
                {!connectedBrands ? (
                    <EmptyDataState
                        error={error}
                        isLoading={isLoading}
                        errorUpdateCount={errorUpdateCount}
                        refetch={refetch}
                        supportTicketLink={metricool.trusted_urls.new_support_ticket}
                        emptyStateTitle={__("No connected brands", "metricool")}
                        emptyStateDescription={__("There appears to be no connected brands available.", "metricool")}
                    />
                ) : (
                    <FieldWrapper
                        label={__("Choose your brand", "metricool")}
                        control={control}
                        name={"id"}
                        uniqueIdSuffix={"connected-brand"}
                        render={(props) => (
                            <Select
                                {...props}
                                onValueChange={props.onChange}
                                className={"border-neutral-200 font-semibold text-black!"}
                                placeholder={__("Select a brand", "metricool")}
                            >
                                {connectedBrands.map((brand) => (
                                    <SelectOption
                                        value={brand.id}
                                        className={clsx("font-semibold hover:bg-primary-light/50 focus:bg-primary-light/50")}
                                    >
                                        {brand.label ?? __("Empty Brand", "metricool")}
                                    </SelectOption>
                                ))}
                            </Select>
                        )}
                    />
                )}
                <FlexContainer direction={"row"}>
                    <Button
                        variant={"black-ghost"}
                        className={"border-0"}
                        onClick={onCancel}
                    >
                        {__("Cancel", "metricool")}
                    </Button>
                    <Button
                        variant={"black"}
                        type={"submit"}
                        disabled={!dirtyFields.id}
                    >
                        <FlexContainer direction={"row"} className={"gap-2! items-center"}>
                            {__("Finish", "metricool")}
                            <Icon icon={"arrow-right"}/>
                        </FlexContainer>
                    </Button>
                </FlexContainer>
            </form>
        </FlexContainer>
    );
};

export { ConnectBrandStep };