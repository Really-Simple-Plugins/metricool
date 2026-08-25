import { Button, FlexContainer, Icon } from "@/components/shared";
import { cn } from "@/support/functions/utils.ts";
import { type ConnectedAccount } from "@/hooks/useConnectedAccountsData.tsx";

const AccountTile = ({
    label,
    unconnectedLabel,
    icon,
    connectedClasses,
    unconnectedClasses,
    upsell,
    userName,
    link,
    isConnected,
}: ConnectedAccount) => {
    const wrapperClasses = cn(
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
            <FlexContainer direction={"row"} className={"min-w-6.25 items-center justify-center"}>
                <Icon
                    data-content
                    icon={icon}
                    className={cn(
                        "size-6 transition-all duration-300 ease-in-out",
                        isConnected ? connectedClasses : "text-white"
                    )}
                />
            </FlexContainer>
            <FlexContainer direction={"row"} className={"justify-between items-center grow"}>
                <FlexContainer direction={"column"} className={cn("text-sm justify-center gap-0!")}>
                    {isConnected ? (
                        <>
                            <span className={"text-gray-500"}>{label}</span>
                            <div className={"font-semibold"}>{userName}</div>
                        </>
                    ) : (
                        <span data-content className={"text-white transition-all duration-300 ease-in-out"}>
                            {unconnectedLabel}
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