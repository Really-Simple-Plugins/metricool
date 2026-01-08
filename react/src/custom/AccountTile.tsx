import { Icon, type IconProps } from "../components";
import { clsx } from "clsx";
import { __, sprintf } from "@wordpress/i18n";

type AccountTileProps = {
    label: string,
    icon: IconProps["icon"],
    connectedClasses: string,
    unconnectedClasses: string,
    upsell: boolean,
    userName?: string,
    link?: string,
}

const AccountTile = ({ label, icon, connectedClasses, unconnectedClasses, upsell, userName, link }: AccountTileProps) => {
    return (
        <div onClick={() => {window.open(link, "_blank"); window.focus();}}
             className={clsx("flex rounded-sm border-1 w-full min-h-[48px] px-2 items-center gap-2 cursor-pointer",
                 userName ? "border-neutral-200" : unconnectedClasses,
        )}>
            <div className={"min-w-[25px] flex items-center justify-center"}>
                <Icon icon={icon} className={clsx("size-6", userName ? connectedClasses : "text-white")}/>
            </div>
            <div className={"flex justify-between items-center grow"}>
                <div className={clsx("text-sm flex flex-col justify-center")}>
                    {userName ? (
                        <>
                            <span className={"text-gray-500"}>{label}</span>
                            <div className={"font-semibold"}>{userName}</div>
                        </>
                    ) : (
                        <span className={"text-white"}>{sprintf(__("Connect a %s Account"), label)}</span>
                    )}
                </div>
                {upsell && (
                    <Icon icon={"upsell"} className={"size-2.5 p-0.5 bg-upsell rounded-full text-black"}/>
                )}
            </div>
        </div>
    );
};

export default AccountTile;