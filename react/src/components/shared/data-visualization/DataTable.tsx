import {
    type Column,
    type ColumnDef,
    flexRender,
    getCoreRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    type SortingState,
    useReactTable,
} from "@tanstack/react-table";
import {
    Table as PrimitiveTable,
    TableBody as PrimitiveTableBody,
    TableCell as PrimitiveTableCell,
    TableHead as PrimitiveTableHead,
    TableHeader as PrimitiveTableHeader,
    TableRow as PrimitiveTableRow,
} from "@/components/shared/primitives/table";
import { Icon } from "@/components/shared/user-feedback/Icon";
import { Button } from "@/components/shared/forms/Button";
import { type MouseEvent, useState } from "react";
import { cn } from "@/support/functions/utils";
import { Pagination } from "@/components/shared/data-visualization/Pagination";
import { __ } from "@wordpress/i18n";
import { FlexContainer } from "@/components/shared/general/FlexContainer";

interface DataTableColumnHeaderProps<TData, TValue> {
    column: Column<TData, TValue>,
    title: string,
    iconClasses?: string,
    onSort?: (values: Record<string, string | number | boolean>) => void
}

/**
 *
 * @version 1.0.0
 */
const DataTableColumnHeader = <TData, TValue>({
    column,
    title,
    className,
    iconClasses,
    onSort,
}: DataTableColumnHeaderProps<TData, TValue> & React.ComponentProps<"div">) => {
    const isColumnSortable = column.getCanSort();
    const columnSortDirection = column.getIsSorted();

    const handleSort = (event: MouseEvent<HTMLDivElement | HTMLButtonElement>) => {
        event.stopPropagation();

        const nextOrder = column.getNextSortingOrder();

        if (onSort) {
            onSort({
                columnName: column.id,
                columnSort: nextOrder,
            });
        } else {
            column.toggleSorting();
        }
    };

    return (
        <FlexContainer
            direction={"row"}
            className={cn(
                "items-center justify-between font-bold h-9 px-2",
                className,
                {
                    "cursor-pointer": isColumnSortable
                }
            )}
            {...(isColumnSortable && {
                onClick: handleSort
            })}
        >
            {title}
            {column.getCanSort() && (
                <Button
                    variant={"icon"}
                    size={"icon"}
                    onClick={handleSort}
                >
                    <Icon
                        icon={
                            columnSortDirection === false ?
                                "sort" :
                                `sort-${columnSortDirection}`
                        }
                        className={iconClasses}
                    />
                </Button>
            )}
        </FlexContainer>
    );
};


interface DataTableProps<TData> {
    columns: ColumnDef<TData, TData[keyof TData]>[],
    data: TData[],
    isLoading?: boolean,
    hideNoResultsRowIfDataEmpty?: boolean,
    initialSorting?: SortingState,
    tableSettings?: {
        disableSorting?: boolean,
        manualSorting?: boolean,
        pageSize?: number,
        showBorder?: boolean,
        tableHeaderClasses?: string,
        tableRowClasses?: string,
        hidePagination?: boolean,
        roundedTopCornersTableHeader?: boolean,
        tableLayoutFixed?: boolean,
    },
}

/**
 *
 * @version 1.0.0
 */
const DataTable = <TData, >({ data, columns, isLoading, hideNoResultsRowIfDataEmpty, initialSorting, tableSettings }: DataTableProps<TData>) => {
    const [sorting, setSorting] = useState<SortingState>(initialSorting ?? []);
    const table = useReactTable({
        data,
        columns,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        onSortingChange: setSorting,
        getSortedRowModel: getSortedRowModel(),
        manualSorting: tableSettings?.manualSorting,
        enableSorting: !tableSettings?.disableSorting,
        defaultColumn: {
            size: 0,
        },
        state: {
            sorting,
        },
        initialState: {
            pagination: {
                pageSize: tableSettings?.pageSize ?? 10,
            },
        },
    });

    const columnWidth = (size?: number) => size ? `${size}px` : "auto";

    return (
        <FlexContainer direction={"column"} className={cn(
            "gap-2! grow justify-between",
            {
                "opacity-45": isLoading,
            }
        )}>
            <div
                className={cn(
                    "overflow-hidden",
                    {
                        "rounded-sm border border-neutral-100": tableSettings?.showBorder,
                    }
                )}
            >
                <PrimitiveTable
                    roundedTopCornersTableHeader={tableSettings?.roundedTopCornersTableHeader}
                    className={cn(
                        {
                            "md:table-fixed md:w-full": tableSettings?.tableLayoutFixed
                        }
                    )}
                >
                    <PrimitiveTableHeader
                        className={cn(
                            "bg-neutral-200",
                            tableSettings?.tableHeaderClasses
                        )}
                    >
                        {table.getHeaderGroups().map((headerGroup) => (
                            <PrimitiveTableRow
                                key={headerGroup.id}
                                className={cn(
                                    {
                                        "pointer-events-none": tableSettings?.disableSorting || isLoading
                                    }
                                )}
                            >
                                {headerGroup.headers.map((header) => {
                                    return (
                                        <PrimitiveTableHead
                                            key={header.id}
                                            style={{
                                                width: columnWidth(header.column.columnDef.size)
                                            }}
                                        >
                                            {header.isPlaceholder
                                                ? null
                                                : flexRender(
                                                    header.column.columnDef.header,
                                                    header.getContext()
                                                )
                                            }
                                        </PrimitiveTableHead>
                                    );
                                })}
                            </PrimitiveTableRow>
                        ))}
                    </PrimitiveTableHeader>
                    <PrimitiveTableBody className={"bg-white"}>
                        {table.getRowModel().rows?.length ? (
                            table.getRowModel().rows.map((row) => (
                                <PrimitiveTableRow
                                    className={cn("border-neutral-100", tableSettings?.tableRowClasses)}
                                    key={row.id}
                                    data-state={row.getIsSelected() && "selected"}
                                >
                                    {row.getVisibleCells().map((cell) => (
                                            <PrimitiveTableCell
                                                key={cell.id}
                                                className={"text-ellipsis overflow-hidden"}
                                                style={{
                                                    width: columnWidth(cell.column.columnDef.size),
                                                }}
                                            >
                                                {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                            </PrimitiveTableCell>
                                        )
                                    )}
                                </PrimitiveTableRow>
                            ))
                        ) : !hideNoResultsRowIfDataEmpty && (
                            <PrimitiveTableRow>
                                <PrimitiveTableCell colSpan={columns.length} className={"h-24 text-center"}>
                                    {__("No results.", "metricool")}
                                </PrimitiveTableCell>
                            </PrimitiveTableRow>
                        )}
                    </PrimitiveTableBody>
                </PrimitiveTable>
            </div>
            {!tableSettings?.hidePagination && (
                <Pagination
                    currentPage={table.getPageCount() === 0 ? 0 : table.getState().pagination.pageIndex + 1}
                    totalPages={table.getPageCount()}
                    onNext={() => table.nextPage()}
                    onPrevious={() => table.previousPage()}
                    isNextDisabled={!table.getCanNextPage()}
                    isPreviousDisabled={!table.getCanPreviousPage()}
                />
            )}
        </FlexContainer>
    );
};

export { DataTable, DataTableColumnHeader, type Column };