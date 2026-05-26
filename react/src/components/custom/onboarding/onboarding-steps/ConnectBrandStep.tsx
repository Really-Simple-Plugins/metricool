import {
    Alert,
    Button,
    DialogHeader,
    DialogTitle,
    FieldWrapper,
    FlexContainer,
    Icon,
    LoadingAndErrorState,
    Select,
    SelectOption,
    showToast,
} from "@/components/shared";
import { __, sprintf } from "@wordpress/i18n";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { clsx } from "clsx";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import OnboardingSchema from "@/support/form-schemas/OnboardingSchema.ts";
import { useMutation, useQuery } from "@tanstack/react-query";
import DOMPurify from "dompurify";

const brandSchema = OnboardingSchema.shape.brand;

const ConnectBrandStep = () => {
    const { httpClient, dispatch, metricool } = useGlobalContext();

    const { data: connectedBrands, isLoading, errorUpdateCount, error, refetch } = useQuery({
        queryKey: ["connected_brands"],
        queryFn: () => httpClient.setRoute("connected_brands").get(),
        staleTime: Infinity,
        select: (data): z.infer<typeof brandSchema>[] => data.data,
    });

    const {
        handleSubmit,
        formState: { dirtyFields },
        control,
    } = useForm<z.infer<typeof brandSchema>>({
        resolver: zodResolver(brandSchema),
        defaultValues: {
            id: "",
        },
    });

    const { mutate: onSubmit, error: submitError } = useMutation({
        mutationFn: async (formValues: z.infer<typeof brandSchema>) => {
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

    const { mutate: doLogout } = useMutation({
        mutationFn: async () => {
            return httpClient.setRoute("logout").post();
        },
        onSuccess: () => {
            window.location.href = metricool.dashboard_url;
        },
        onError: (error) => {
            console.error(error);
            showToast.error(error.message);
        }
    });

    return (
        <FlexContainer direction={"column"} className={"justify-center !gap-6 items-center"}>
            <FlexContainer direction={"column"} className={"w-full !gap-2"}>
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
                    <div
                        onClick={() => doLogout()}
                        dangerouslySetInnerHTML={{
                            __html: DOMPurify.sanitize(
                                sprintf(
                                    /*translators: the two variables are opening and closing anchor tags */
                                    __(
                                        "Don’t see the brand you’re looking for? %1$sGo to Metricool.com%2$s and make sure you’re logged into the correct account.",
                                        "metricool",
                                    ),
                                    `<a onclick="${() => doLogout}" class="underline" href=${metricool.trusted_urls.base_url} target="_blank" rel="noopener noreferrer">`,
                                    `</a>`
                                ), { ADD_ATTR: ["target"] }
                            )
                        }}
                    />
                </Alert>
            </FlexContainer>
            {submitError && (<Alert variant={"error"}>{submitError.message}</Alert>)}
            <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col items-center justify-center gap-6 w-full"}>
                {!connectedBrands ? (
                    <LoadingAndErrorState
                        error={error}
                        isLoading={isLoading}
                        errorUpdateCount={errorUpdateCount}
                        refetch={refetch}
                        supportTicketLink={metricool.trusted_urls.new_support_ticket}
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
                                className={"border-neutral-200 font-semibold !text-black"}
                                placeholder={__("Select a brand", "metricool")}
                            >
                                {connectedBrands.map((brand) => (
                                    <SelectOption
                                        value={brand.id}
                                        className={clsx("font-semibold hover:bg-primary-light/50 focus:bg-primary-light/50")}
                                    >
                                        {brand.label}
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
                        onClick={() => doLogout()}
                    >
                        {__("Cancel", "metricool")}
                    </Button>
                    <Button
                        variant={"black"}
                        type={"submit"}
                        disabled={!dirtyFields.id}
                    >
                        <FlexContainer direction={"row"} className={"!gap-2 items-center"}>
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