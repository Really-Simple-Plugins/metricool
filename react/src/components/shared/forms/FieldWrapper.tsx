import {
    Field as PrimitiveField,
    FieldError as PrimitiveFieldError,
    FieldLabel as PrimitiveFieldLabel,
} from "@/components/shared/primitives/field.tsx";
import { type ComponentProps, type ComponentType, type ReactNode, useCallback, useState } from "react";
import { camelCaseToHyphenated, cn } from "@/functions/utils.ts";
import { FlexContainer } from "@/components/shared/general/FlexContainer.tsx";
import {
    type Control,
    Controller,
    type ControllerFieldState,
    type ControllerRenderProps,
    type FieldValues,
    type Path
} from "react-hook-form";

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
            className={cn("text-rsp-error-dark text-sm h-0 opacity-0 transition-all ease-in-out duration-300",
                fieldState.invalid && "h-3 opacity-100"
            )}
            errors={[errorMessage]}
        />
    );
};

type FieldComponentProps = {
    "aria-invalid": boolean,
    id: string,
} & Pick<ControllerRenderProps, "onChange" | "onBlur" | "value">;


type FieldWrapperProps<T extends FieldValues> = {
    label: string | ReactNode,
    flexDirection?: "row" | "column" | "row-reverse" | "column-reverse",
    control: Control<T>,
    name: Path<T>,
    uniqueIdSuffix?: string,
    FieldComponent: ComponentType<FieldComponentProps>,
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
    control,
    uniqueIdSuffix = "",
    FieldComponent,
}: ComponentProps<"input"> & ComponentProps<"label"> & FieldWrapperProps<T>) => {
    const fieldId = camelCaseToHyphenated(name + uniqueIdSuffix);

    const ComponentToRender = useCallback(
        ({ field, fieldState }: {
                field: ControllerRenderProps<T, (string & Path<T>) | (undefined & Path<T>)>,
                fieldState: ControllerFieldState
            }
        ) => (
            <FieldComponent
                aria-invalid={fieldState.invalid}
                id={fieldId}
                onChange={field.onChange}
                onBlur={field.onBlur}
                value={field.value}
            />
        ), []);

    return (
        <Controller
            name={name}
            control={control}
            render={({ field, fieldState }) => {
                return (
                    <PrimitiveField data-invalid={fieldState.invalid} className={cn("gap-0 transition-all ease-in-out duration-300", fieldState.invalid && "gap-2")}>
                        <FlexContainer direction={flexDirection ?? "column"} className={cn("!gap-2", className)}>
                            <PrimitiveFieldLabel htmlFor={fieldId} className={cn("!gap-1 font-semibold text-md text-black", required && "required-asterisk")}>
                                {label}
                            </PrimitiveFieldLabel>
                            <ComponentToRender
                                field={field}
                                fieldState={fieldState}
                            />
                        </FlexContainer>
                        <FieldError fieldState={fieldState}/>
                    </PrimitiveField>
                );
            }}
        />
    );
};

export { FieldWrapper, FieldError };