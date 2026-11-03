import { Link, type RouteOrHref } from "@/components/shared/general/Link";
import { Icon } from "@/components/shared/user-feedback/Icon";
import { FlexContainer } from "@/components/shared/general/FlexContainer";
import { cn } from "@/support/functions/utils";
import { type VariantProps } from "class-variance-authority";
import { headerVariants } from "@/support/helpers/Variants";

type HeaderTabProps =
    React.ComponentProps<"div">
    & RouteOrHref
    & {
        badge?: {
            label: string | number,
            classes?: string,
        }
    };

const HeaderTab = ({ route, href, children, className, badge }: HeaderTabProps) => {
    return (
        <Link
            {...(route ? {
                route: route
            } : {
                href: href,
                target: "_blank"
            })}
            variant={"unstyled"}
            className={cn(
                "text-md text-black items-center flex gap-1 max-[700px]:py-5.75 relative ease-in-out duration-200 border-b-3 border-b-transparent [&.active]:border-b-primary hover:border-b-3 hover:border-b-primary-light hover:text-black",
                className
            )}
        >
            {children}
            {badge && (
                <div className={cn("rounded-full flex items-center justify-center text-[8px] font-bold bg-rsp-error text-white max-h-3 min-w-3 px-0.5 absolute top-2 -right-3", badge.classes)}>
                    {badge.label}
                </div>
            )}
            {href && (<Icon icon={"inline-external-link"}/>)}
        </Link>
    );
};

type HeaderProps = {
    tabs?: React.ReactNode | React.ReactNode[],
    actions: React.ReactNode | React.ReactNode[],
    logo: React.ReactNode,
} & VariantProps<typeof headerVariants>

const Header = ({ tabs, actions, logo, variant, className }: React.ComponentProps<"div"> & HeaderProps) => {
    return (
        <div className={headerVariants({ variant })}>
            <FlexContainer direction={"row"} className={cn("max-w-8xl mx-auto justify-between items-stretch flex-wrap max-[700px]:gap-3! gap-8!", className)}>
                <FlexContainer direction={"row"} className={"min-w-17.5 min-h-17.5 items-center justify-center"}>
                    {logo}
                </FlexContainer>
                <FlexContainer direction={"row"} className={"order-3 sm:order-2 w-full sm:w-fit grow justify-center sm:justify-start"}>
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