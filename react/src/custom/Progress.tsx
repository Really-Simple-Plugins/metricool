import { Block, BlockHeader, BlockHeaderTitle, FlexContainer } from "../components";
import { __, _n, sprintf } from "@wordpress/i18n";
import TabNavigation from "./TabNavigation.tsx";
import { useEffect, useState } from "react";
import Task, { type TaskProps } from "./Task.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useQuery } from "@tanstack/react-query";

const Progress = () => {
    const { httpClient } = useGlobalContext();

    const { data: taskData, isLoading, error } = useQuery({
        queryKey: ["tasks"],
        queryFn: () => httpClient?.setRoute("get_tasks").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data) => data.data,
    });

    useEffect(() => {
        console.log(taskData, isLoading, error);
    }, [taskData, isLoading, error]);


    const completionPercentage = 45;
    const tasks: TaskProps[] = [
        {
            text: "Schedule your first Social Media post",
            completed: false,
            status: "open",
            dismissed: false,
            action: { text: "Schedule post", link: "" },
            id: 1
        },
        {
            text: "Connect al your social Media accounts",
            completed: false,
            status: "open",
            dismissed: false,
            action: { text: "Connect Accounts", link: "" },
            id: 2
        },
        {
            text: "Connect your LinkedIn account",
            completed: false,
            status: "open",
            dismissed: false,
            action: { text: "Connect Account", link: "" },
            id: 3
        },
        {
            text: "Connect your Twitter account",
            completed: true,
            status: "premium",
            dismissed: false,
            action: { text: "Connect Account", link: "" },
            id: 4
        },
        {
            text: "Store historical analytics to 90 days with Premium",
            completed: true,
            status: "premium",
            dismissed: false,
            action: { text: "Add Service", link: "" },
            id: 5
        }
    ];
    const remainingTasks = tasks.filter((task) => !task.completed);

    const [activeTab, setActiveTab] = useState(1);
    const [displayedTasks, setDisplayedTasks] = useState(remainingTasks);
    const tabs = [{
        title: `${__("All Tasks", "metricool")} (${tasks.length})`
    }, {
        title: `${__("Remaining Tasks", "metricool")} (${remainingTasks.length})`
    }];
    const onTabChange = (tabIndex: number) => {
        setActiveTab(tabIndex);
        setDisplayedTasks(tabIndex === 0 ? tasks : remainingTasks);
    };

    return (
        <Block>
            <BlockHeader className={"flex justify-between"}>
                <BlockHeaderTitle>{__("Progress", "metricool")}</BlockHeaderTitle>
                <TabNavigation activeTab={activeTab} onTabClick={onTabChange} separator={true} tabs={tabs}/>
            </BlockHeader>
            <FlexContainer direction={"column"}>
                <div className={"w-full bg-neutral-200 rounded-md h-5"}>
                    <div
                        className={"bg-[image:var(--gradient-brand)] h-5 rounded-md transition-all duration-300"}
                        style={{ width: `${completionPercentage}%` }}
                    />
                </div>

                <div className={"flex items-center justify-start gap-4"}>
                    <span className={"font-bold text-2xl w-min"}>
                        {completionPercentage}%
                    </span>
                    <span className="text-xl font-medium">
                        {remainingTasks.length === 0 && __("You're all set! Great job!", "simplybook")}
                        {remainingTasks.length > 0 && sprintf(_n("You're on your way. You still have %s task open.", "You're on your way. You still have %s tasks open.", remainingTasks.length, "simplybook"), String(remainingTasks.length))}
                    </span>
                </div>

                {/* Task List */}
                <div className="h-[290px] mt-1 grid overflow-y-auto content-start gap-4">
                    {displayedTasks.map((task) => (
                        <Task key={task.id} task={task}/>
                    ))}
                </div>
            </FlexContainer>
        </Block>
    );
};

export default Progress;