import { useGlobalContext } from "@/context/GlobalContext.tsx";
import { useMutation, useQuery } from "@tanstack/react-query";
import { showToast, type TaskProps, } from "@/components/shared";
import { __ } from "@wordpress/i18n";

type UseTaskDataProps = {
    hasConnectedAccountsDataLoaded: boolean | undefined,
};
/**
 * Hook to retrieve Task Data.
 *
 * Contains a {@link useQuery} which fetches tasks, sorts and filters them based
 * on status and calculates the completionPercentage.
 *
 * Contains a {@link useMutation} which dismisses a task based on ID.
 */
const useTaskData = ({hasConnectedAccountsDataLoaded}: UseTaskDataProps) => {
    const { httpClient } = useGlobalContext();

    // The 'enabled' option ensures the connected accounts call, which updates the
    // first_connection task in the database if a connection is detected, will
    // always finish first before tasks are actually fetched so users receive
    // accurate information on first page load
    const taskDataQuery = useQuery({
        enabled: hasConnectedAccountsDataLoaded,
        queryKey: ["tasks"],
        queryFn: () => httpClient.setRoute("get_tasks").get(),
        staleTime: 1000 * 60 * 5, // 5 minutes
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

    const dismissTaskMutation = useMutation({
        mutationFn: async ({ taskId }: {
            taskId: string,
        }) => {
            return httpClient.setRoute("dismiss_task").setPayload({
                "taskId": taskId,
            }).post();
        },
        onSuccess: async () => {
            await taskDataQuery.refetch();
        },
        onError: (error) => {
            showToast.error(__("There was an error dismissing your task", "metricool"));
            console.error(error.message);
        }
    });

    return {
        taskDataQuery: taskDataQuery,
        dismissTaskMutation: dismissTaskMutation,
    }
};

export { useTaskData };