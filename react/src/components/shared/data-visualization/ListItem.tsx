import { cn } from "@/support/functions/utils.ts";
import { FlexContainer } from "@/components/shared/general/FlexContainer.tsx";
import { Icon, type IconProps } from "@/components/shared/user-feedback/Icon.tsx"

type ListItemProps = {
    link?: string,
    action?: React.ReactNode,
} & ({
    icon?: never,
    iconColor?: never,
    iconClass?: never,
    iconPosition?: never,
} | {
    icon: IconProps["icon"],
    iconColor?: never,
    iconClass?: string,
    iconPosition: "left" | "right",
} | {
    icon: "circle",
    iconColor: string,
    iconClass?: string,
    iconPosition: "left" | "right",
});

const ListItem = ({
    icon,
    iconColor,
    iconPosition,
    iconClass,
    link,
    action,
    children,
    className,
}: React.ComponentProps<"div"> & ListItemProps) => {
    return (
        <FlexContainer direction={"row"} className={cn(className, "items-center justify-between group text-md")}>
            <FlexContainer direction={iconPosition === "right" ? "row-reverse" : "row"} className={cn("items-center !gap-2")}>
                {icon && (
                    <Icon icon={icon} className={cn(iconClass,
                        icon === "circle" && "h-3 w-3",
                        iconColor === "warning" && "text-rsp-warning",
                        iconColor === "success" && "text-rsp-success",
                        iconColor === "error" && "text-rsp-error",
                        iconColor === "simplybook" && "text-simplybook",
                        iconColor === "rsssl" && "text-rss",
                        iconColor === "cmplz" && "text-complianz",
                    )}/>
                )}
                {link ? (
                    <a href={link} target={"_blank"} className={cn(className, "text-md")}>
                        {children}
                    </a>
                ) : (
                    <div className={cn(className, "text-md")}>
                        {children}
                    </div>
                )}
            </FlexContainer>
            {action && action}
        </FlexContainer>
    );
};

export { ListItem };