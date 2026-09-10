import { FetchingErrorAlert } from "@/components/shared/user-feedback/FetchingErrorAlert";
import { FlexContainer } from "@/components/shared/general/FlexContainer";
import { Icon } from "@/components/shared/user-feedback/Icon";
import type { UseQueryResult } from "@tanstack/react-query";
import { Alert } from "@/components/shared/user-feedback/Alert";
import { __ } from "@wordpress/i18n";

type EmptyDataStateProps = Pick<UseQueryResult, "isLoading" | "error" | "errorUpdateCount" | "refetch"> & {
    supportTicketLink: string,
} & ({
    customEmptyState?: React.ReactNode | React.ReactNode[],
    emptyStateTitle?: never,
    emptyStateDescription?: never,
} | {
    customEmptyState?: never,
    emptyStateTitle?: string,
    emptyStateDescription?: string | React.ReactNode | React.ReactNode[],
});

const EmptyDataState = ({
    isLoading,
    error,
    errorUpdateCount,
    refetch,
    supportTicketLink,
    emptyStateTitle,
    emptyStateDescription,
    customEmptyState,
}: EmptyDataStateProps) => {
    return (
        <>
            {isLoading ? (
                <FlexContainer direction={"row"} className={"justify-center items-center w-full grow"}>
                    <Icon icon={"loading"} className={"size-5"}/>
                </FlexContainer>
            ) : error ? (
                <FetchingErrorAlert errorUpdateCount={errorUpdateCount} refetch={refetch} supportTicketLink={supportTicketLink}/>
            ) : customEmptyState ? (
                <>
                    {customEmptyState}
                </>
            ) : (
                <FlexContainer direction={"row"} className={"justify-center items-end w-full grow"}>
                    <Alert variant={"warning"} title={emptyStateTitle ?? __("No data found", "metricool")}>
                        {emptyStateDescription ?? __("There appears to be no data available", "metricool")}
                    </Alert>
                </FlexContainer>
            )}
        </>
    );
};

export { EmptyDataState };