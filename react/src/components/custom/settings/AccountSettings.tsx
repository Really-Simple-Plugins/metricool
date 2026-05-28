import {
    Block,
    BlockHeader,
    Button,
    FieldWrapper,
    FlexContainer,
    FormFooter,
    Icon,
    Input,
    LoadingAndErrorState,
    SignOut,
    Switch
} from "@/components/shared";
import { __, sprintf } from "@wordpress/i18n";
import { useBlocker } from "@tanstack/react-router";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useAuthenticationData } from "@/hooks/useAuthenticationData.tsx";
import { useUserData } from "@/hooks/useUserData.tsx";

/**
 * The Account Settings section in Settings.
 *
 * Is a `<form>` component which contains {@link Block}(s). This way the form's
 * onSubmit attribute can be used and a submit callback function doesn't have
 * to be passed down to the button in the {@link FormFooter}. No other button
 * with type "submit" should be added anywhere in the subtree of this component.
 *
 * Retrieves all Query, Mutation and Form data from {@link useUserData}.
 *
 * Contains a {@link useBlocker} which requests user confirmation to leave or
 * refresh the page if there are unsaved changes.
 *
 */
const AccountSettings = () => {
    const { metricool, metricoolDynamicUrl } = useGlobalContext();

    const {
        userSettingsDataQuery: { data, isLoading, error: queryError, errorUpdateCount, refetch },
        userSettingsFormData: {
            handleSubmit,
            formState: { errors: formValidationErrors, isDirty },
            getValues,
            resetField,
            control,
        },
        updateUserSettingsDataMutation: { mutate: onSubmit, isPending }
    } = useUserData();

    const {
        logoutMutation: { mutate: logoutUser }
    } = useAuthenticationData({ reloadOnLogout: true });

    useBlocker({
        shouldBlockFn: () => {
            if (!isDirty) {
                return false; // Dont block
            }

            const shouldLeave = window.confirm(
                /*translators: this text is taken directly from FireFox's native pop-up for unsaved changes. Please use that text for your language.*/
                __("This page is asking you to confirm that you want to leave — information you’ve entered may not be saved.", "metricool"),
            );

            return !shouldLeave;
        },
        enableBeforeUnload: isDirty,
    });

    return (
        <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col min-w-full md:min-w-[50%]"}>
            <FlexContainer direction={"column"}>
                <Block className={"rounded-md"}>
                    <BlockHeader title={__("Account settings", "metricool")}/>
                    <SignOut onSignOut={() => logoutUser()} currentUserEmail={metricool.account?.user.mail ?? ""}/>
                    <Button variant={"primary-ghost"} className={"font-bold"} link={metricoolDynamicUrl.withPath("user-settings/access")}>
                        {__("Change Password", "metricool")}
                        <Icon icon={"external-link"}/>
                    </Button>
                </Block>
                <Block className={"rounded-t-md rounded-b-none"}>
                    <BlockHeader title={__("Monthly summary", "metricool")}/>
                    {!data ? (
                        <LoadingAndErrorState
                            error={queryError}
                            isLoading={isLoading}
                            errorUpdateCount={errorUpdateCount}
                            refetch={refetch}
                            supportTicketLink={metricool.trusted_urls.new_support_ticket}
                        />
                    ) : (
                        <FlexContainer direction={"column"}>
                            <FieldWrapper
                                flexDirection={"row"}
                                className={"justify-between"}
                                label={__("Receive monthly summary", "metricool")}
                                control={control}
                                name={"sendToAlternativeEmail"}
                                render={(props) => (
                                    <Switch
                                        {...props}
                                        checked={props.value}
                                        onCheckedChange={
                                            (checked) => {
                                                props.onChange(checked);
                                                // If the switch is set to false, the alternativeEmail field is
                                                // reset so the form doesn't think there are unsaved changes on
                                                // this field or send these changes to be saved
                                                if (!checked) {
                                                    resetField("alternativeEmail");
                                                }
                                            }
                                        }
                                    />
                                )}
                            />
                            {/* Render the email input field only if the value of sendToAlternativeEmail is true */}
                            {getValues().sendToAlternativeEmail && (
                                <FlexContainer direction={"column"} className={"gap-1!"}>
                                    <FieldWrapper
                                        label={__("Custom e-mail for the monthly summary", "metricool")}
                                        control={control}
                                        name={"alternativeEmail"}
                                        render={(props) => (
                                            <Input
                                                {...props}
                                            />
                                        )}
                                    />
                                    {metricool.account?.user.mail && !data.alternativeEmail && (
                                        <span className={"text-gray-400 pl-2"}>
                                            {sprintf(
                                                __("When this field is empty the monthly summary is sent to %1$s", "metricool"),
                                                [metricool.account.user.mail]
                                            )}
                                        </span>
                                    )}
                                </FlexContainer>
                            )}
                        </FlexContainer>
                    )}
                </Block>
            </FlexContainer>
            <FormFooter formHasUnsavedChanges={isDirty} formIsSubmitting={isPending} formHasErrors={Object.keys(formValidationErrors).length > 0}/>
        </form>
    );
};

export { AccountSettings };