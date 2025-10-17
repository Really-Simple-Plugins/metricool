import { Button, Icon } from "../components";
import { clsx } from "clsx";
import { __, sprintf } from "@wordpress/i18n";

type AccountTileProps = {
    label: string,
    icon: string,
    connectedClasses: string,
    unconnectedClasses: string,
    upsell: boolean,
    userName?: string,
}

const AccountTile = ({ label, icon, connectedClasses, unconnectedClasses, upsell, userName }: AccountTileProps) => {
    return (
        <div className={clsx("flex rounded-sm border-1 w-full min-h-[40px] px-1 items-center gap-2",
            userName ? "border-neutral-200" : unconnectedClasses,
        )}>
            <div className={"min-w-[24px] flex items-center justify-center"}>
                <Icon icon={icon} iconClass={clsx("text-xl", userName ? connectedClasses : "text-white")}/>
            </div>
            <div className={"flex justify-between grow"}>
                <div className={clsx("text-xs flex flex-col justify-center", !userName && "cursor-pointer")}>
                    {userName ? (
                        <>
                            <span className={"text-gray-500"}>{label}</span>
                            <div className={"font-semibold"}>{userName}</div>
                        </>
                    ) : (
                        <span className={"text-white text-xs"}>{sprintf(__("Connect a %s Account"), label)}</span>
                    )}
                </div>
                <div className={"flex items-center gap-2 mr-1"}>
                    {userName ? (
                        <>
                            <Button variant={"icon"} size={"icon"} icon={"edit"} iconPosition={"right"} iconClass={"size-3"}></Button>
                            <Button variant={"icon"} size={"icon"} icon={"settings"} iconPosition={"right"} iconClass={"size-3"}></Button>
                        </>
                    ) : (
                        <>
                            <Button variant={"icon"} size={"icon"} icon={"external-link"} iconPosition={"right"} iconClass={"size-3 text-white"}></Button>
                            {
                                upsell && <Button variant={"icon"} size={"icon"} icon={"upsell"} iconPosition={"right"} iconClass={"size-2.5 p-0.5 bg-upsell rounded-full text-black"}></Button>
                            }
                        </>
                    )}

                </div>
            </div>
        </div>
    );
};

export default AccountTile;