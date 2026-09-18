import type { Dispatch, SetStateAction } from "react";
import { FlexContainer } from "@/components/shared/general/FlexContainer";
import { cn } from "@/support/functions/utils";
import { Button } from "@/components/shared/forms/Button";

type TabNavigationProps = {
    tabs: { title: string, component?: React.ReactElement }[],
    separator?: boolean,
    activeTab: number,
    onTabClick: Dispatch<SetStateAction<number>> | ((index: number) => void),
}

const TabNavigation = ({ tabs, activeTab, onTabClick, separator = false }: TabNavigationProps) => {
    return (
        <FlexContainer direction={"row"} className={"w-auto leading-none text-md gap-2!"}>
            {tabs.map((tab, index) => (
                <>
                    <Button
                        key={index}
                        variant={"link"}
                        onClick={() => onTabClick(index)}
                        className={cn(
                            "no-underline hover:underline hover:text-black",
                            activeTab === index ? "font-semibold" : "opacity-45"
                        )}
                    >
                        {tab.title}
                    </Button>
                    {separator && (index !== tabs.length - 1) && (<span>|</span>)}
                </>
            ))}
        </FlexContainer>
    );
};

export { TabNavigation };