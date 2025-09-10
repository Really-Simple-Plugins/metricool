import { clsx } from "clsx";

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
        <div className={"flex items-center justify-between"}>
            <div className={"flex items-center"}>
                {icon === "circle" && (
                    <div className={clsx("h-3 w-3 rounded-full mr-2", iconColor && iconColorMap[iconColor])}></div>)}
                <div className={clsx(className, "text-sm")}>
                    {children}
                </div>
            </div>
            {link && (<span className={"text-sm underline cursor-pointer"}>{link}</span>)}
        </div>
    );
};

export default ListItem;