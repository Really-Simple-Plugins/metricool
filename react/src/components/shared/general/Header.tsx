import { Link } from "@tanstack/react-router";
import { Icon } from "@/components/shared/user-feedback/Icon.tsx";
import { FlexContainer } from "@/components/shared/general/FlexContainer.tsx";
import { cn } from "@/support/functions/utils.ts";
import { cva, type VariantProps } from "class-variance-authority";

const HeaderTab = ({ link, children, className, external = false, badge }: React.ComponentProps<"div"> & {
    link: string,
    external?: boolean,
    badge?: {
        label: string | number,
        classes?: string,
    }
}) => {
    return (
        <Link
            to={link}
            className={cn(
                "text-md text-black items-center flex gap-1 max-[700px]:py-[23px] focus:outline-hidden relative ease-in-out duration-200 border-b-3 border-b-transparent [&.active]:border-b-primary hover:border-b-3 hover:border-b-primary-light hover:text-black",
                className
            )}
            {...(external && { target: "_blank" })}
        >
            {children}
            {badge && (
                <div className={cn("rounded-full flex items-center justify-center text-[8px] font-bold bg-rsp-error text-white max-h-3 min-w-3 px-0.5 absolute top-2 -right-3", badge.classes)}>
                    {badge.label}
                </div>
            )}
            {external && (<Icon icon={"inline-external-link"}/>)}
        </Link>
    );
};

const HeaderVariantStyling = {
    "default": "bg-white",
    "transparent": "bg-transparent",
};

const HeaderVariants = cva(
    "min-w-full",
    {
        variants: {
            variant: HeaderVariantStyling
        },
        defaultVariants: {
            variant: "default",
        },
    }
);

type HeaderProps = {
    tabs?: React.ReactNode | React.ReactNode[],
    actions: React.ReactNode | React.ReactNode[],
    logo: React.ReactNode,
} & VariantProps<typeof HeaderVariants>

const Header = ({ tabs, actions, logo, variant, className }: React.ComponentProps<"div"> & HeaderProps) => {
    return (
        <div className={HeaderVariants({ variant })}>
            <FlexContainer direction={"row"} className={cn("max-w-[125rem] mx-auto justify-between items-stretch flex-wrap max-[700px]:!gap-3 !gap-8", className)}>
                <FlexContainer direction={"row"} className={"min-w-[4.375rem] min-h-[4.375rem] items-center justify-center"}>
                    {logo}
                </FlexContainer>
                <FlexContainer direction={"row"} className={"order-3 sm:order-2 w-full sm:w-fit flex-grow justify-center sm:justify-start"}>
                    {tabs}
                </FlexContainer>
                <FlexContainer direction={"row"} className={"order-2 sm:order-3 items-center"}>
                    {actions}
                </FlexContainer>
            </FlexContainer>
        </div>
    );
};

export { Header, HeaderTab };