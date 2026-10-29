import {
    Select as PrimitiveSelect,
    SelectContent as PrimitiveSelectContent,
    SelectItem as PrimitiveSelectItem,
    SelectTrigger as PrimitiveSelectTrigger,
    SelectValue as PrimitiveSelectValue,
} from "@/components/shared/primitives/select";
import { cn } from "@/support/functions/utils";
import * as React from "react";
import { Icon, type IconProps } from "@/components/shared/user-feedback/Icon";
import { FlexContainer } from "@/components/shared/general/FlexContainer";

type SelectProps = {
    placeholder?: string,
    inputSize?: "sm" | "default",
    icon?: IconProps,
}

const defaultSelectOptionClasses = "gap-2! items-center rounded-base py-1.5 px-2 text-md outline-hidden select-none font-semibold cursor-pointer";

/**
 *
 * @version 1.0.0
 */
const SelectOption = ({ children, value, className, disabled }: React.ComponentProps<"option">) => {
    return (
        <PrimitiveSelectItem className={cn(className, defaultSelectOptionClasses)} value={String(value)} disabled={disabled}>
            {children}
        </PrimitiveSelectItem>
    );
};

/**
 *
 * @version 1.0.0
 */
const DisabledSelectOption = ({ children, className }: React.ComponentProps<"div">) => {
    return (
        <FlexContainer
            direction={"row"}
            className={cn(className, defaultSelectOptionClasses)}
        >
            {children}
        </FlexContainer>
    );
};

/**
 *
 * @version 1.0.0
 */
const Select = ({
    id,
    children,
    className,
    placeholder,
    onValueChange,
    inputSize,
    icon,
    'aria-invalid': ariaInvalid,
    'aria-label': ariaLabel,
    ...props
}: React.ComponentProps<typeof PrimitiveSelect> & React.ComponentProps<"select"> & SelectProps) => {
    return (
        <PrimitiveSelect onValueChange={onValueChange} {...props}>
            <PrimitiveSelectTrigger
                size={inputSize}
                id={id}
                aria-invalid={ariaInvalid}
                aria-label={ariaLabel}
                className={cn(
                    className,
                    "w-full shadow-none data-[size=default]:text-md data-[size=sm]:text-sm rounded-base! px-2 cursor-pointer *:data-[slot=select-value]:grow",
                    "focus-visible:aria-invalid:ring-rsp-error-dark/20 focus-visible:aria-invalid:ring-3 aria-invalid:border-rsp-error-dark",
                )}
            >
                {icon && <Icon icon={icon.icon} className={icon.className}/>}
                <PrimitiveSelectValue placeholder={placeholder}/>
            </PrimitiveSelectTrigger>
            <PrimitiveSelectContent className={"rounded-base! z-999999"}>
                {children}
            </PrimitiveSelectContent>
        </PrimitiveSelect>
    );
};

export { Select, SelectOption, DisabledSelectOption };