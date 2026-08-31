import {
    Field as PrimitiveField,
    FieldError as PrimitiveFieldError,
    FieldLabel as PrimitiveFieldLabel,
} from "@/components/shared/primitives/field";
import { type ComponentProps, type ReactNode, useState } from "react";
import { camelCaseToHyphenated, cn } from "@/support/functions/utils";
import { FlexContainer } from "@/components/shared/general/FlexContainer";
import { type Control, Controller, type ControllerRenderProps, type FieldValues, type Path } from "react-hook-form";

type FieldErrorProps = {
    fieldState: {
        invalid: boolean,
        error?: { message?: string },
    },
}

/**
 *
 * @version 1.0.0
 */
const FieldError = ({ fieldState }: FieldErrorProps) => {
    const [errorMessage, setErrorMessage] = useState(fieldState.error);

    if ((fieldState.error?.message && errorMessage !== fieldState.error)) {
        setErrorMessage(fieldState.error);
    }

    return (
        <PrimitiveFieldError
            className={cn(
                "text-rsp-error-dark text-sm h-0 opacity-0 transition-all ease-in-out duration-300",
                {
                    "h-3 opacity-100": fieldState.invalid,
                }
            )}
            errors={[errorMessage]}
        />
    );
};

type FieldComponentProps = {
    "aria-invalid": boolean,
    id: string,
    disabled?: boolean,
} & Pick<ControllerRenderProps, "onChange" | "onBlur" | "value">;


type FieldWrapperProps<T extends FieldValues> = {
    label: string | ReactNode,
    hideLabel?: boolean,
    flexDirection?: "row" | "column" | "row-reverse" | "column-reverse",
    control: Control<T>,
    name: Path<T>,
    uniqueIdSuffix?: string,
    render: (args: FieldComponentProps) => ReactNode,
};

/**
 *
 * @version 1.0.0
 */
const FieldWrapper = <T extends FieldValues>({
    required,
    flexDirection,
    name,
    className,
    label,
    hideLabel,
    control,
    uniqueIdSuffix = "",
    disabled,
    render,
}: ComponentProps<"input"> & ComponentProps<"label"> & FieldWrapperProps<T>) => {
    const fieldId = camelCaseToHyphenated(name + uniqueIdSuffix);

    return (
        <Controller
            name={name}
            control={control}
            render={({ field, fieldState }) => {
                return (
                    <PrimitiveField
                        data-invalid={fieldState.invalid}
                        className={cn(
                            "gap-0 transition-all ease-in-out duration-300 justify-center",
                            {
                                "gap-2": fieldState.invalid
                            }
                        )}
                    >
                        <FlexContainer
                            direction={flexDirection ?? "column"}
                            className={cn(
                                "gap-2!",
                                className
                            )}
                        >
                            <PrimitiveFieldLabel
                                htmlFor={fieldId}
                                className={cn(
                                    "gap-1! font-semibold text-md text-black",
                                    {
                                        "required-asterisk": required,
                                        "hidden": hideLabel
                                    }
                                )}
                            >
                                {label}
                            </PrimitiveFieldLabel>
                            {render({
                                "aria-invalid": fieldState.invalid,
                                id: fieldId,
                                disabled: disabled,
                                onChange: field.onChange,
                                onBlur: field.onBlur,
                                value: field.value
                            })}
                        </FlexContainer>
                        <FieldError fieldState={fieldState}/>
                    </PrimitiveField>
                );
            }}
        />
    );
};

export { FieldWrapper, FieldError };