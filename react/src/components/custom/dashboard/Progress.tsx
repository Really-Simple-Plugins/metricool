import {
    Block,
    BlockHeader,
    FlexContainer,
    LoadingAndErrorState,
    TabNavigation,
    Task,
    type TaskProps,
} from "@/components/shared";
import { __, _n, sprintf } from "@wordpress/i18n";
import { useState } from "react";
import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useTaskData } from "@/hooks/useTaskData";
import { useConnectedAccountsData } from "@/hooks/useConnectedAccountsData.tsx";
// import { queryClient } from "@/main.tsx";

/**
 * The Progress block used in {@link DashboardLayout}.
 *
 * Retrieves all data from {@link useTaskData}, which gets passed the `isSuccess`
 * flag from {@link useConnectedAccountsData}'s `connectedAccountsQuery` to use
 * as the `enabled` option on `taskDataQuery`.
 *
 * Contains the logic (state, array and callback) for the tabs
 * (All Tasks/Remaining Tasks), rendering the {@link TabNavigation} through
 * the `action` prop of the {@link BlockHeader}.
 *
 * Displays everything in a {@link Block} with a fixed height (500px)
 */
const Progress = () => {
    const { metricool, dispatch, dashboardSettings } = useGlobalContext();

    const {
        connectedAccountsQuery: { isSuccess: connectedAccountsLoaded }
    } = useConnectedAccountsData();

    const {
        taskDataQuery: { data: taskData, isLoading, error, refetch, errorUpdateCount },
        dismissTaskMutation: { mutate: dismissTask },
    } = useTaskData({ hasConnectedAccountsDataLoaded: connectedAccountsLoaded });


    // This state saves the activeTab's index in the tabs array.
    // Initiated as 1 for the Remaining Tasks.
    const [activeTab, setActiveTab] = useState(dashboardSettings.activeProgressTab ?? 1);
    const tabs = [{
        title: `${__("All Tasks", "metricool")} (${taskData?.tasks.length})`,
    }, {
        title: `${__("Remaining Tasks", "metricool")} (${taskData?.remainingTasks?.length})`,
    }];
    const onTabChange = (tabIndex: number) => {
        setActiveTab(tabIndex);
        dispatch({
            dispatchType: "setDashboardSetting",
            change: { dashboardSettings: { activeProgressTab: tabIndex } }
        });
    };

    return (
        <Block className={"min-h-[500px] max-h-[500px]"}>
            <BlockHeader
                title={__("Progress", "metricool")}
                action={taskData && (
                    <TabNavigation activeTab={activeTab} onTabClick={onTabChange} separator={true} tabs={tabs}/>
                )}
            />
            {!taskData ? (
                <LoadingAndErrorState
                    error={error}
                    isLoading={isLoading}
                    errorUpdateCount={errorUpdateCount}
                    refetch={refetch}
                    supportTicketLink={metricool.trusted_urls.new_support_ticket}
                />
            ) : (
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
                        <span className={"text-xl font-medium"}>
                            {taskData.remainingTasks.length === 0 && __("You're all set! Great job!", "metricool")}
                            {taskData.remainingTasks.length > 0 && sprintf(
                                /*translators: variable is number of tasks*/
                                _n("You're on your way. You still have %1$s task open.", "You're on your way. You still have %1$s tasks open.", taskData.remainingTasks.length, "metricool"), String(taskData.remainingTasks.length)
                            )}
                        </span>
                    </div>
                    {/* Task List */}
                    <div className={"max-h-[300px] flex flex-col overflow-y-auto gap-4 pr-3"}>
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
                                    {...((task.premium && !(task.status === "completed" || task.status === "dismissed")) && { badgeClass: "bg-[image:var(--gradient-brand)] border-transparent [background-origin:border-box]" })}
                                />
                            ))
                        }
                    </div>
                </FlexContainer>
            )}
        </Block>
    );
};

export { Progress };