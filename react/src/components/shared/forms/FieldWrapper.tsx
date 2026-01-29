import {
    Field as PrimitiveField,
    FieldError as PrimitiveFieldError,
    FieldLabel as PrimitiveFieldLabel,
} from "@/components/shared/primitives/field.tsx";
import { type ReactNode, useState } from "react";
import { cn } from "@/functions/utils.ts";
import { FlexContainer } from "@/components/shared/general/FlexContainer.tsx";

type FieldWrapperProps = {
    fieldState: {
        invalid: boolean,
        error?: { message?: string },
    },
    label: string | ReactNode,
    flexDirection?: "row" | "column" | "row-reverse" | "column-reverse"
};

/**
 *
 * @version 1.0.0
 */
const FieldWrapper = ({
    children,
    required,
    flexDirection,
    fieldState,
    htmlFor,
    className,
    label,
    ...props
}: React.ComponentProps<"input"> & React.ComponentProps<"label"> & FieldWrapperProps) => {
    const [errorMessage, setErrorMessage] = useState(fieldState.error);

    if ((fieldState.error?.message && errorMessage !== fieldState.error)) {
        setErrorMessage(fieldState.error);
    }

    return (
        <PrimitiveField data-invalid={fieldState.invalid} className={cn("gap-0 transition-all ease-in-out duration-300", fieldState.invalid && "gap-2")} {...props}>
            <FlexContainer direction={flexDirection ?? "column"} className={cn("!gap-2", className)}>
                <PrimitiveFieldLabel htmlFor={htmlFor} className={cn("!gap-1 font-semibold text-md text-black", required && "required-asterisk")}>
                    {label}
                </PrimitiveFieldLabel>
                {children}
            </FlexContainer>
            <PrimitiveFieldError className={cn("text-rsp-error-dark text-sm h-0 opacity-0 transition-all ease-in-out duration-300", fieldState.invalid && "h-3 opacity-100")} errors={[errorMessage]}/>
        </PrimitiveField>
    );
};

export { FieldWrapper };