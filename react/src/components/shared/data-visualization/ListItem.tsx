import { cn } from "@/support/functions/utils";
import { FlexContainer } from "@/components/shared/general/FlexContainer";
import { Icon, type IconProps } from "@/components/shared/user-feedback/Icon";

type ListItemProps = {
    link?: string,
    action?: React.ReactNode,
    iconProps?: {
        icon: IconProps["icon"],
        iconColor?: string,
        iconClass?: string,
        iconPosition: "left" | "right",
    }
};

const ListItem = ({
    iconProps,
    link,
    action,
    children,
    className,
}: React.ComponentProps<"div"> & ListItemProps) => {
    return (
        <FlexContainer direction={"row"} className={cn(className, "items-center justify-between group text-md")}>
            <FlexContainer direction={iconProps?.iconPosition === "right" ? "row-reverse" : "row"} className={cn("items-center !gap-2")}>
                {iconProps && (
                    <Icon
                        icon={iconProps.icon}
                        className={cn(
                            iconProps.iconClass,
                            iconProps.icon === "circle" && "h-3 w-3",
                            iconProps.iconColor === "warning" && "text-rsp-warning",
                            iconProps.iconColor === "success" && "text-rsp-success",
                            iconProps.iconColor === "error" && "text-rsp-error",
                            iconProps.iconColor === "simplybook" && "text-simplybook",
                            iconProps.iconColor === "rsssl" && "text-rss",
                            iconProps.iconColor === "cmplz" && "text-complianz",
                        )}
                    />
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