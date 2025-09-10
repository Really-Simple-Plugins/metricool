import { clsx } from "clsx";
import FlexContainer from "./FlexContainer.tsx";

const iconColorMap = {
    "warning": "bg-(--rsp-warning)",
    "success": "bg-(--rsp-success)",
    "error": "bg-(--rsp-error)",
    "rss": "bg-(--color-rss)",
    "simplybook": "bg-(--color-simplybook)",
    "complianz": "bg-(--color-complianz)",
};

type ListItemProps = {
    link?: string
} & ({
    icon?: never,
    iconColor?: never,
} | {
    icon: string,
    iconColor: keyof typeof iconColorMap
});

const ListItem = ({ icon, iconColor, link, children, className }: React.ComponentProps<'div'> & ListItemProps) => {

    return (
        <FlexContainer direction={"row"} className={"items-center justify-between"}>
            <FlexContainer direction={"row"} className={"items-center !gap-2"}>
                {icon === "circle" && (
                    <div className={clsx("h-3 w-3 rounded-full", iconColor && iconColorMap[iconColor])}></div>)}
                <div className={clsx(className, "text-sm")}>
                    {children}
                </div>
            </FlexContainer>
            {link && (<span className={"text-sm underline cursor-pointer"}>{link}</span>)}
        </FlexContainer>
    );
};

export default ListItem;