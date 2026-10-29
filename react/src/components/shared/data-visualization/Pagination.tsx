import { __, sprintf } from "@wordpress/i18n";
import { Button } from "@/components/shared/forms/Button";
import { Icon } from "@/components/shared/user-feedback/Icon";

type PaginationProps = {
    currentPage: number,
    totalPages?: number | null,
    onPrevious: () => void,
    onNext: () => void,
    isNextDisabled: boolean,
    isPreviousDisabled: boolean,
};

const Pagination = ({ currentPage, totalPages, onNext, onPrevious, isNextDisabled, isPreviousDisabled }: PaginationProps) => {
    return (
        <div className={"flex items-center justify-end gap-2"}>
            <div className={"flex items-center justify-center text-sm font-semibold"}>
                {typeof totalPages === "number"
                    ? sprintf(
                        /*translators: variables are page numbers*/
                        __("Page %1$d of %2$d", "metricool"), currentPage, totalPages
                    )
                    : sprintf(
                        /*translators: variable is the current page number*/
                        __("Page %d", "metricool"), currentPage
                    )
                }
            </div>
            <div className={"flex gap-1"}>
                <Button
                    variant={"icon"}
                    size={"icon"}
                    className={"bg-primary-light text-primary p-1.5!"}
                    onClick={onPrevious}
                    disabled={isPreviousDisabled}
                >
                    <Icon icon={"left"} className={"size-3"}/>
                </Button>
                <Button
                    variant={"icon"}
                    size={"icon"}
                    className={"bg-primary-light text-primary p-1.5!"}
                    onClick={onNext}
                    disabled={isNextDisabled}
                >
                    <Icon icon={"right"} className={"size-3"}/>
                </Button>
            </div>
        </div>
    );
};

export { Pagination };