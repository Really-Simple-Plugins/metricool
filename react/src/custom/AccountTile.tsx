import { Button, Icon } from "../components";
import { clsx } from "clsx";
import { capitalizeFirstCharacter } from "../functions/utils.tsx";

type AccountTileProps = {
    connected: boolean,
    accountType: "twitter" | "youtube" | "linkedIn" | "instagram" | "domain" | "facebook",
    accountName?: string,
}

const AccountTile = ({ connected, accountType, accountName }: AccountTileProps) => {
    return (
        <div className={clsx("flex rounded-sm border-1 w-full min-h-[40px] px-1 items-center gap-2",
            connected && "border-neutral-200",
            accountType === "twitter" && !connected && "border-twitter bg-twitter",
            accountType === "youtube" && !connected && "border-youtube bg-youtube",
            accountType === "linkedIn" && !connected && "border-linkedin bg-linkedin",
            accountType === "facebook" && !connected && "border-facebook bg-facebook",
        )}>
            <Icon icon={accountType} iconClass={clsx("text-xl",
                accountType === "twitter" && (connected ? "text-twitter" : "text-white"),
                accountType === "youtube" && (connected ? "text-youtube" : "text-white"),
                accountType === "linkedIn" && (connected ? "text-linkedin" : "text-white"),
                accountType === "facebook" && (connected ? "text-facebook" : "text-white"),
            )}/>
            {connected ? (
                <div className={"flex justify-between grow"}>
                    <div className={"text-xs flex flex-col justify-center"}>
                        <span className={"text-gray-500"}>{capitalizeFirstCharacter(accountType)}</span>
                        <div className={"font-semibold"}>{accountName}</div>
                    </div>
                    <div className={"flex items-center gap-1"}>
                        <Button variant={"icon"} size={"icon"} icon={"edit"} iconPosition={"right"} iconClass={"size-3"}></Button>
                        <Button variant={"icon"} size={"icon"} icon={"settings"} iconPosition={"right"} iconClass={"size-3"}></Button>
                    </div>
                </div>
                ) : (
                <div className={"flex justify-between grow"}>
                    <div className={"flex items-center justify-center cursor-pointer"}>
                        <span className={"text-white text-xs"}>{`Connect a ${capitalizeFirstCharacter(accountType)} Account`}</span>
                    </div>
                    <div className={"flex items-center gap-1"}>
                        <Button variant={"icon"} size={"icon"} icon={"external-link"} iconPosition={"right"} iconClass={"size-3 text-white"}></Button>
                        <Button variant={"icon"} size={"icon"} icon={"upsell"} iconPosition={"right"} iconClass={"size-2.5 p-0.5 bg-upsell rounded-full text-black"}></Button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default AccountTile;