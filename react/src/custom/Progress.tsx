import { Block, BlockHeader, FetchingErrorAlert, FlexContainer, Icon, showToast } from "@/components";
import { __, _n, sprintf } from "@wordpress/i18n";
import TabNavigation from "./TabNavigation.tsx";
import { useState } from "react";
import Task, { type TaskProps } from "./Task.tsx";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation, useQuery } from "@tanstack/react-query";
import { queryClient } from "@/main.tsx";

/**
 * The Progress block used in {@link DashboardLayout}.
 *
 * Contains a {@link useQuery} which fetches tasks, sorts and filters them based
 * on status and calculates the completionPercentage.
 *
 * Contains a {@link useMutation} which dismisses a task based on ID.
 *
 * Contains the logic (state, array and callback) for the tabs
 * (All Tasks/Remaining Tasks), rendering the {@link TabNavigation} through
 * the `action` prop of the {@link BlockHeader}.
 *
 * Displays everything in a {@link Block} with a fixed height (500px)
 */
const Progress = () => {
    const { httpClient, metricool } = useGlobalContext();
    // The 'enabled' option ensures the connected accounts call, which updates the
    // first_connection task in the database if a connection is detected, will
    // always finish first before tasks are actually fetched so users receive
    // accurate information on first page load
    const { data: taskData, isLoading, error, refetch, errorUpdateCount } = useQuery({
        enabled: queryClient.getQueryData(["connected", "accounts"]) !== undefined,
        queryKey: ["tasks"],
        queryFn: () => httpClient.setRoute("get_tasks").get(),
        staleTime: 1000 * 60, // 1 minute
        select: (data): {
            tasks: TaskProps[],
            remainingTasks: TaskProps[],
            completedTasks: TaskProps[],
            completionPercentage: number
        } => {
            const allTasks = data.data.sort((a: TaskProps, b: TaskProps) => a.priority - b.priority);
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
            return httpClient.setRoute("dismiss_task").setPayload({
                "taskId": taskId,
            }).post();
        },
        onSuccess: async () => {
            await refetch();
        },
        onError: (error) => {
            showToast.error(__("There was an error dismissing your task", "metricool"));
            console.error(error.message);
        }
    });

    // This state saves the activeTab's index in the tabs array.
    // Initiated as 1 for the Remaining Tasks.
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
        <Block className={"min-h-[500px] max-h-[500px]"}>
            <BlockHeader
                title={__("Progress", "metricool")}
                action={taskData && (
                    <TabNavigation activeTab={activeTab} onTabClick={onTabChange} separator={true} tabs={tabs}/>
                )}
            />
            {isLoading ? (
                <FlexContainer direction={"row"} className={"justify-center items-center w-full grow"}>
                    <Icon icon={"loading"} className={"size-5"}/>
                </FlexContainer>
            ) : error ? (
                <FetchingErrorAlert errorUpdateCount={errorUpdateCount} refetch={refetch} supportTicketLink={metricool.trusted_urls.new_support_ticket}/>
            ) : taskData && (
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
                            {taskData.remainingTasks.length === 0 && __("You're all set! Great job!", "metricool")}
                            {taskData.remainingTasks.length > 0 && sprintf(_n("You're on your way. You still have %s task open.", "You're on your way. You still have %s tasks open.", taskData.remainingTasks.length, "metricool"), String(taskData.remainingTasks.length))}
                        </span>
                    </div>
                    {/* Task List */}
                    <div className="max-h-[300px] flex flex-col overflow-y-auto gap-4 pr-3">
                        {/* using spread operators, the array of tasks to display is built dynamically
                            if All Tasks is selected (activeTab === 0), the completed tasks are added on.
                            This way, no extra filtering needs to be done.
                         */}
                        {[...taskData.remainingTasks, ...(activeTab === 0 ? [...taskData.completedTasks] : [])]
                            .sort((a: TaskProps, b: TaskProps) => a.priority - b.priority)
                            .map((task) => (
                                <Task
                                    key={task.id}
                                    task={task}
                                    onDismiss={() => dismissTask({ taskId: task.id })}
                                    {...((task.premium && !(task.status === "completed" || task.status === "dismissed")) && {badgeClass: "bg-[image:var(--gradient-brand)] border-transparent [background-origin:border-box]"})}
                                />
                            ))
                        }
                    </div>
                </FlexContainer>
            )}
        </Block>
    );
};

export default Progress;