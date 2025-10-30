import { Block, BlockHeader, FlexContainer } from "../components";
import { __, _n, sprintf } from "@wordpress/i18n";
import TabNavigation from "./TabNavigation.tsx";
import { useEffect, useState } from "react";
import Task, { type TaskProps } from "./Task.tsx";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { useMutation, useQuery } from "@tanstack/react-query";

const Progress = () => {
    const { httpClient } = useGlobalContext();
    const { data: taskData, isLoading, error, refetch } = useQuery({
        queryKey: ["tasks"],
        queryFn: () => httpClient?.setRoute("get_tasks").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data): {
            tasks: TaskProps[],
            remainingTasks: TaskProps[],
            completedTasks: TaskProps[],
            completionPercentage: number
        } => {
            const statusPriority = {
                urgent: 0,
                open: 10,
                completed: 20,
                dismissed: 30,
                hidden: 40,
            };
            const allTasks = data.data.map((task: TaskProps) => ({
                ...task,
                priority: (task.premium || task.special_feature) ? 15 : statusPriority[task.status] ? statusPriority[task.status] : 50,
            })).sort((a: TaskProps, b: TaskProps) => a.priority - b.priority);

            const remainingTasks = allTasks.filter((task: TaskProps) => !(task.status === "completed" || task.status === "dismissed"));
            const completedTasks = allTasks.filter(
                (task: TaskProps) => task.status === "dismissed" || task.status === "completed",
            );
            const completionPercentage = Math.round((completedTasks.length / allTasks.length) * 100);
            return {
                tasks: allTasks,
                remainingTasks: remainingTasks,
                completedTasks: completedTasks,
                completionPercentage: completionPercentage,
            };
        },
    });

    const { mutate: dismissTask } = useMutation({
        mutationFn: async ({ taskId }: {
            taskId: string,
        }) => {
            const response = await httpClient?.setRoute("dismiss_task").setPayload({
                "taskId": taskId,
            }).post();
            console.log(response);

            const taskDismissed = response?.data;

            if (!taskDismissed) {
                console.error("Error dismissing task: ", response?.message);
                return;
            }

            return taskDismissed;
        },
        onSuccess: async () => {
            await refetch();
        }
    });

    useEffect(() => {
        console.log(taskData, isLoading, error);
    }, [taskData, isLoading, error]);

    const [activeTab, setActiveTab] = useState(1);
    const tabs = [{
        title: `${__("All Tasks", "metricool")} (${taskData?.tasks.length})`,
    }, {
        title: `${__("Remaining Tasks", "metricool")} (${taskData?.remainingTasks?.length})`,
    }];
    const onTabChange = (tabIndex: number) => {
        setActiveTab(tabIndex);
    };

    return (
        <Block>
            <BlockHeader
                title={__("Progress", "metricool")}
                action={taskData && (
                    <TabNavigation activeTab={activeTab} onTabClick={onTabChange} separator={true} tabs={tabs}/>
                )}
            />
            {isLoading && (
                <div>LOADING</div>
            )}
            {taskData && (
                <FlexContainer direction={"column"}>
                    <div className={"w-full bg-neutral-200 rounded-md h-5"}>
                        <div
                            className={"bg-[image:var(--gradient-brand)] h-5 rounded-md transition-all duration-300"}
                            style={{ width: `${taskData.completionPercentage}%` }}
                        />
                    </div>

                    <div className={"flex items-center justify-start gap-4"}>
                        <span className={"font-bold text-2xl w-min"}>
                            {taskData.completionPercentage}%
                        </span>
                        <span className="text-xl font-medium">
                            {taskData.remainingTasks.length === 0 && __("You're all set! Great job!", "simplybook")}
                            {taskData.remainingTasks.length > 0 && sprintf(_n("You're on your way. You still have %s task open.", "You're on your way. You still have %s tasks open.", taskData.remainingTasks.length, "simplybook"), String(taskData.remainingTasks.length))}
                        </span>
                    </div>

                    {/* Task List */}
                    <div className="max-h-[300px] flex flex-col overflow-y-auto gap-4 pr-3">
                        {[...taskData.remainingTasks, ...(activeTab === 0 ? [...taskData.completedTasks] : [])]
                            .sort((a: TaskProps, b: TaskProps) => a.priority - b.priority)
                            .map((task) => (
                                <Task key={task.id} task={task} onDismiss={() => dismissTask({ taskId: task.id })}/>
                            ))
                        }
                    </div>
                </FlexContainer>
            )}
            {error && (
                <div>"error"</div>
            )}
        </Block>
    );
};

export default Progress;