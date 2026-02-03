import { Button, FlexContainer, Icon, type IconProps } from "@/components/shared";
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
    isConnected: boolean,
}

const AccountTile = ({
    label,
    icon,
    connectedClasses,
    unconnectedClasses,
    upsell,
    userName,
    link,
    isConnected,
}: AccountTileProps) => {
    return (
        <FlexContainer
            direction={"row"}
            {...(!isConnected && {
                onClick: () => {
                    window.open(link, "_blank");
                    window.focus();
                }
            })}
            className={clsx(
                "flex rounded-sm border-1 w-full min-h-[48px] px-2 items-center gap-2",
                isConnected ? "border-neutral-200" : `${unconnectedClasses} cursor-pointer transition-all duration-300 ease-in-out`,
            )}
        >
            <FlexContainer direction={"row"} className={"min-w-[25px] items-center justify-center"}>
                <Icon
                    data-content
                    icon={icon}
                    className={clsx(
                        "size-6 transition-all duration-300 ease-in-out",
                        isConnected ? connectedClasses : "text-white"
                    )}
                />
            </FlexContainer>
            <FlexContainer  direction={"row"} className={"justify-between items-center grow"}>
                <FlexContainer direction={"column"} className={clsx("text-sm justify-center !gap-0")}>
                    {isConnected ? (
                        <>
                            <span className={"text-gray-500"}>{label}</span>
                            <div className={"font-semibold"}>{userName}</div>
                        </>
                    ) : (
                        <span data-content className={"text-white transition-all duration-300 ease-in-out"}>{sprintf(__("Connect a %s Account"), label)}</span>
                    )}
                </FlexContainer>
                <FlexContainer direction={"row"}>
                    {isConnected && (
                        <Button
                            size={"icon"}
                            variant={"icon"}
                            icon={"settings"}
                            iconPosition={"left"}
                            onClick={() => {
                                window.open(link, "_blank");
                                window.focus();
                            }}
                        />
                    )}
                    {upsell && (
                        <Icon icon={"upsell"} className={"size-2.5 p-0.5 bg-upsell rounded-full text-black"}/>
                    )}
                </FlexContainer>
            </FlexContainer>
        </FlexContainer>
    );
};

export default AccountTile;