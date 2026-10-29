import { Button, FlexContainer } from "@/components/shared";
import { __ } from "@wordpress/i18n";

type FormFooterStateProps = {
    formHasUnsavedChanges: boolean,
    formIsSubmitting: boolean,
    formHasErrors: boolean,
};

const FormFooterState = ({ formHasUnsavedChanges, formIsSubmitting, formHasErrors = false }: FormFooterStateProps) => {

    // Form states for Design page
    const settingsStates = [
        { condition: formIsSubmitting, message: __("Saving...", "metricool") },
        { condition: formHasErrors, message: __("Form contains errors", "metricool") },
        { condition: formHasUnsavedChanges, message: __("You have unsaved changes", "metricool") },
    ];

    return (
        <FlexContainer direction={"row"} className={"justify-end items-center p-2"}>
            {settingsStates.find(state => state.condition)?.message}
            <Button disabled={(!formHasUnsavedChanges || formIsSubmitting)} type={"submit"} variant={"black"}>
                {__("Save changes", "metricool")}
            </Button>
        </FlexContainer>
    );
};

export { FormFooterState };