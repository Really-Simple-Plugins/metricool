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
} from "@/components/shared/primitives/table.tsx";
import { Icon } from "@/components/shared/user-feedback/Icon.tsx";
import { Button } from "@/components/shared/forms/Button.tsx";
import { useState } from "react";
import { __, sprintf } from "@wordpress/i18n";

interface DataTableColumnHeaderProps<TData, TValue> {
    column: Column<TData, TValue>,
    title: string,
}

/**
 *
 * @version 1.0.0
 */
const DataTableColumnHeader = <TData, TValue>({
    column,
    title,
    className,
}: DataTableColumnHeaderProps<TData, TValue> & React.ComponentProps<"div">) => {
    if (!column.getCanSort()) {
        return (<div className={className}>{title}</div>);
    }

    return (
        <div
            className={"flex items-center justify-between font-bold h-9 "}
            onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
        >
            {title}
            <Icon icon={"sort"}/>
        </div>
    );
};


interface DataTableProps<TData, TValue> {
    columns: ColumnDef<TData, TValue>[],
    data: TData[],
    tableSettings?: Record<string, string | number>,
}

/**
 *
 * @version 1.0.0
 */
const DataTable = <TData, TValue>({ data, columns, tableSettings }: DataTableProps<TData, TValue>) => {
    const [sorting, setSorting] = useState<SortingState>([]);
    const table = useReactTable({
        data,
        columns,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        onSortingChange: setSorting,
        getSortedRowModel: getSortedRowModel(),
        state: {
            sorting,
        },
        initialState: {
            pagination: {
                pageSize: tableSettings && tableSettings.pageSize ? Number(tableSettings.pageSize) : 10,
            },
        },
    });

    return (
        <div className={"flex flex-col gap-2"}>
            <div className={"overflow-hidden rounded-sm border border-neutral-100"}>
                <PrimitiveTable>
                    <PrimitiveTableHeader className={"bg-neutral-200"}>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <PrimitiveTableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header, index) => {
                                    return (
                                        <PrimitiveTableHead
                                            key={header.id}
                                            style={{
                                                minWidth: index === 0 ? "60%" : "20%",
                                                maxWidth: index === 0 ? "60%" : "20%",
                                            }}
                                        >
                                            {header.isPlaceholder
                                                ? null
                                                : flexRender(
                                                    header.column.columnDef.header,
                                                    header.getContext()
                                                )}
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
                                    className={"border-neutral-100"}
                                    key={row.id}
                                    data-state={row.getIsSelected() && "selected"}
                                >
                                    {row.getVisibleCells().map((cell, index) => (
                                        <PrimitiveTableCell
                                            key={cell.id}
                                            style={{
                                                minWidth: index === 0 ? "60%" : "20%",
                                                maxWidth: index === 0 ? "60%" : "20%",
                                            }}
                                        >
                                            {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                        </PrimitiveTableCell>
                                    ))}
                                </PrimitiveTableRow>
                            ))
                        ) : (
                            <PrimitiveTableRow>
                                <PrimitiveTableCell colSpan={columns.length} className={"h-24 text-center"}>
                                    No results.
                                </PrimitiveTableCell>
                            </PrimitiveTableRow>
                        )}
                    </PrimitiveTableBody>
                </PrimitiveTable>
            </div>
            <div className={"flex items-center justify-end gap-2"}>
                <div className={"flex items-center justify-center text-sm font-semibold"}>
                    {sprintf(__("Page %s of %s", "metricool"), [String(table.getState().pagination.pageIndex === 0 ? 0 : table.getState().pagination.pageIndex + 1), String(table.getPageCount())])}
                </div>
                <div className={"flex gap-1"}>
                    <Button
                        variant={"icon"}
                        size={"icon"}
                        icon={"left"}
                        iconPosition={"left"}
                        className={"bg-primary-light text-primary p-1.5"}
                        iconClass={"size-3"}
                        onClick={() => table.previousPage()}
                        disabled={!table.getCanPreviousPage()}
                    />
                    <Button
                        variant={"icon"}
                        size={"icon"}
                        icon={"right"}
                        iconPosition={"right"}
                        className={"bg-primary-light text-primary p-1.5"}
                        iconClass={"size-3"}
                        onClick={() => table.nextPage()}
                        disabled={!table.getCanNextPage()}
                    />
                </div>
            </div>
        </div>
    );
};

export { DataTable, DataTableColumnHeader, type Column };