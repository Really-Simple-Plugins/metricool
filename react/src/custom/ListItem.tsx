import { clsx } from "clsx";
import { FlexContainer } from "../components";
import { Icon } from "../components";
import { type IconProps } from "../components/src/components/Icon.tsx";

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
    icon: IconProps['icon'],
    iconColor?: never,
    iconClass?: string,
    iconPosition: "left" | "right",
} | {
    icon: "circle",
    iconColor: string,
    iconClass?: string,
    iconPosition: "left" | "right",
});

const ListItem = ({ icon, iconColor, iconPosition, iconClass, link, action, actionText, children, className }: React.ComponentProps<'div'> & ListItemProps) => {
    return (
        <FlexContainer direction={"row"} className={"items-center justify-between"}>
            <FlexContainer direction={"row"} className={clsx("items-center !gap-2", iconPosition === "right" && "flex-row-reverse")}>
                {icon && ( icon === "circle" ? (
                    <div className={clsx("h-3 w-3 rounded-full",
                        iconColor === "warning" && "bg-rsp-warning",
                        iconColor === "success" && "bg-rsp-success",
                        iconColor === "error" && "bg-rsp-error",
                        iconColor === "simplybook" && "bg-simplybook",
                        iconColor === "rsssl" && "bg-rss",
                        iconColor === "cmplz" && "bg-complianz",
                    )}></div>
                ) : (
                    <Icon icon={icon} iconClass={iconClass} />
                ))}
                {link ? (
                    <a href={link} target={"_blank"} className={clsx(className, "text-sm")}>
                        {children}
                    </a>
                ) : (
                    <div className={clsx(className, "text-sm")}>
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