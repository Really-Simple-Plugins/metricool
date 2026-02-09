import { Button, FlexContainer, Icon, type IconProps } from "@/components/shared";
import { clsx } from "clsx";
import { __, sprintf } from "@wordpress/i18n";
import { isFirstCharacterAVowel } from "@/functions/utils.ts";

type AccountTileProps = {
    label: string,
    icon: IconProps["icon"],
    connectedClasses: string,
    unconnectedClasses: string,
    upsell: boolean,
    userName?: string,
    link: string,
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
    const wrapperClasses = clsx(
        "flex flex-row rounded-sm border-1 w-full min-h-[48px] px-2 items-center gap-2",
        isConnected ? "border-neutral-200" : `${unconnectedClasses} cursor-pointer transition-all duration-300 ease-in-out`,
    );

    // depending on the isConnected status, the top element will either be a div or an anchor
    const WrapperComponent = !isConnected ? "a" : "div";
    return (
        <WrapperComponent
            className={wrapperClasses}
            href={link}
            target={"_blank"}
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
            <FlexContainer direction={"row"} className={"justify-between items-center grow"}>
                <FlexContainer direction={"column"} className={clsx("text-sm justify-center !gap-0")}>
                    {isConnected ? (
                        <>
                            <span className={"text-gray-500"}>{label}</span>
                            <div className={"font-semibold"}>{userName}</div>
                        </>
                    ) : (
                        <span data-content className={"text-white transition-all duration-300 ease-in-out"}>
                            {sprintf(
                                /*translators: first variable is either 'a' or 'an', second is the name of a social media*/
                                __("Connect %1$s %2$s Account", "metricool"), [(isFirstCharacterAVowel(label) ? "an" : "a"), label]
                            )}
                        </span>
                    )}
                </FlexContainer>
                <FlexContainer direction={"row"}>
                    {isConnected && (
                        <Button
                            size={"icon"}
                            variant={"icon"}
                            link={link}
                        >
                            <Icon icon={"settings"}/>
                        </Button>
                    )}
                    {upsell && (
                        <Icon icon={"upsell"} className={"size-2.5 p-0.5 bg-upsell rounded-full text-black"}/>
                    )}
                </FlexContainer>
            </FlexContainer>
        </WrapperComponent>
    );
};

export { AccountTile };