import { FetchingErrorAlert } from "@/components/shared/user-feedback/FetchingErrorAlert.tsx";
import { FlexContainer } from "@/components/shared/general/FlexContainer.tsx";
import { Icon } from "@/components/shared/user-feedback/Icon.tsx";
import type { UseQueryResult } from "@tanstack/react-query";

type LoadingAndErrorState = Pick<UseQueryResult, "isLoading" | "error" | "errorUpdateCount" | "refetch"> & {
    supportTicketLink: string,
};

const LoadingAndErrorState = ({ isLoading, error, errorUpdateCount, refetch, supportTicketLink }: LoadingAndErrorState) => {
    return (
        <>
            {isLoading ? (
                <FlexContainer direction={"row"} className={"justify-center items-center w-full grow"}>
                    <Icon icon={"loading"} className={"size-5"}/>
                </FlexContainer>
            ) : error && (
                <FetchingErrorAlert errorUpdateCount={errorUpdateCount} refetch={refetch} supportTicketLink={supportTicketLink}/>
            )}
        </>
    );
};

export { LoadingAndErrorState };