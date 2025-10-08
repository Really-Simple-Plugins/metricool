import { Card, CardHeader, CardHeaderTitle } from "../components";
import { __, _n, sprintf } from "@wordpress/i18n";
import TabNavigation from "./TabNavigation.tsx";
import { useState } from "react";
import FlexContainer from "./FlexContainer.tsx";
import Task, { type TaskProps } from "./Task.tsx";

const Progress = () => {

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
        <Card>
            <CardHeader className={"flex justify-between"}>
                <CardHeaderTitle>{__("Progress", "metricool")}</CardHeaderTitle>
                <TabNavigation activeTab={activeTab} onTabClick={onTabChange} separator={true} tabs={tabs}/>
            </CardHeader>
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
                <div className="h-[290px] mt-1 grid overflow-y-auto content-start gap-2">
                    {displayedTasks.map((task) => (
                        <Task key={task.id} task={task}/>
                    ))}
                </div>
            </FlexContainer>
        </Card>
    );
};

export default Progress;