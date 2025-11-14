import type { Dispatch, SetStateAction } from "react";
import { FlexContainer } from "../components";
import { clsx } from "clsx";

type TabNavigationProps = {
    tabs: { title: string, component?: React.ReactElement }[],
    separator?: boolean,
    activeTab: number,
    onTabClick: Dispatch<SetStateAction<number>> | ((index: number) => void),
}

const TabNavigation = ({ tabs, activeTab, onTabClick, separator = false }: TabNavigationProps) => {
    return (
        <FlexContainer direction={"row"} className={"w-auto leading-none text-md !gap-2"}>
            {tabs.map((tab, index) => (<>
                <span onClick={() => onTabClick(index)} className={clsx("cursor-pointer", activeTab === index ? "font-semibold" : "opacity-45")}>{tab.title}</span>
                {separator && index != tabs.length - 1 && <span>|</span>}
            </>))}
        </FlexContainer>
    );
};

export default TabNavigation;