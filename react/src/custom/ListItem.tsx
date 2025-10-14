import { clsx } from "clsx";
import { FlexContainer } from "../components";
import { Icon } from "../components";
import { type IconProps } from "../components/src/components/Icon.tsx";

const iconColorMap = {
    "warning": "bg-rsp-warning",
    "success": "bg-rsp-success",
    "error": "bg-sp-error",
    "rss": "bg-rss",
    "simplybook": "bg-simplybook",
    "complianz": "bg-complianz",
};

type ListItemProps = {
    link?: string
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
    iconColor: keyof typeof iconColorMap,
    iconClass?: string,
    iconPosition: "left" | "right",
});

const ListItem = ({ icon, iconColor, iconPosition, iconClass, link, children, className }: React.ComponentProps<'div'> & ListItemProps) => {

    return (
        <FlexContainer direction={"row"} className={"items-center justify-between"}>
            <FlexContainer direction={"row"} className={clsx("items-center !gap-2", iconPosition === "right" && "flex-row-reverse")}>
                {icon && ( icon === "circle" ? (
                    <div className={clsx("h-3 w-3 rounded-full", iconColor && iconColorMap[iconColor])}></div>
                ) : (
                    <Icon icon={icon} iconClass={iconClass} />
                ))}
                <div className={clsx(className, "text-sm")}>
                    {children}
                </div>
            </FlexContainer>
            {link && (<span className={"text-sm underline cursor-pointer"}>{link}</span>)}
        </FlexContainer>
    );
};

export default ListItem;