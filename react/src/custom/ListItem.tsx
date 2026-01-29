import { clsx } from "clsx";
import { FlexContainer, Icon, type IconProps } from "@/components";

type ListItemProps = {
    link?: string,
    action?: () => void,
    actionText?: string,
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
    actionText,
    children,
    className,
}: React.ComponentProps<"div"> & ListItemProps) => {
    return (
        <FlexContainer direction={"row"} className={"items-center justify-between"}>
            <FlexContainer direction={"row"} className={clsx("items-center !gap-2", iconPosition === "right" && "flex-row-reverse")}>
                {icon && (
                    <Icon icon={icon} className={clsx(iconClass,
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
                    <a href={link} target={"_blank"} className={clsx(className, "text-md")}>
                        {children}
                    </a>
                ) : (
                    <div className={clsx(className, "text-md")}>
                        {children}
                    </div>
                )}
            </FlexContainer>
            {actionText && (
                <span onClick={action} className={clsx("text-sm", action && "underline cursor-pointer")}>
                    {actionText}
                </span>
            )}
        </FlexContainer>
    );
};

export default ListItem;