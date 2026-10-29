import { cn } from "@/support/functions/utils";
import { FlexContainer } from "@/components/shared/general/FlexContainer";
import { Icon, type IconProps } from "@/components/shared/user-feedback/Icon";

type LinkOrOnClick = ({
    link?: never,
    onClick?: React.ComponentProps<"button">["onClick"],
    disabled?: boolean,
} | {
    link: string,
    onClick?: never,
    disabled?: never,
});

type ListItemProps = {
    action?: React.ReactNode,
    iconProps?: {
        icon: IconProps["icon"],
        iconColor?: string,
        iconClass?: string,
        iconPosition: "left" | "right",
    },
} & LinkOrOnClick;

const ListItem = ({
    iconProps,
    link,
    action,
    children,
    className,
    onClick,
    disabled,
}: React.ComponentProps<"div"> & ListItemProps) => {
    return (
        <FlexContainer direction={"row"} className={cn(className, "items-center justify-between group text-md")}>
            <FlexContainer direction={iconProps?.iconPosition === "right" ? "row-reverse" : "row"} className={cn("items-center gap-2!")}>
                {iconProps && (
                    <Icon
                        icon={iconProps.icon}
                        className={cn(
                            iconProps.iconClass,
                            {
                                "h-3 w-3": (iconProps.icon === "circle"),
                                "text-rsp-warning": (iconProps.iconColor === "warning"),
                                "text-rsp-success": (iconProps.iconColor === "success"),
                                "text-rsp-error": (iconProps.iconColor === "error"),
                                "text-simplybook": (iconProps.iconColor === "simplybook"),
                                "text-rss": (iconProps.iconColor === "rsssl"),
                                "text-complianz": (iconProps.iconColor === "cmplz"),
                                "text-metricool": (iconProps.iconColor === "metricool"),
                            },
                        )}
                    />
                )}
                {link ? (
                    <a href={link} target={"_blank"} className={cn(className, "text-md")}>
                        {children}
                    </a>
                ) : (
                    <div
                        className={cn(className, "text-md")}
                        {...((onClick && !disabled) && { onClick: onClick })}
                    >
                        {children}
                    </div>
                )}
            </FlexContainer>
            {action && action}
        </FlexContainer>
    );
};

export { ListItem };