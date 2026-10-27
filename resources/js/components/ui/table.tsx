import * as React from "react";
import { cn } from "@/lib/utils";
import { Input } from "./input";

// Wrapper agar tabel bisa di-scroll ke samping kalau kolomnya kebanyakan
const TableCard = ({ children, className } : { children: React.ReactNode, className?: string }) => {
    return(
        <div className={cn("w-full dark:border-gray-800 border rounded-md overflow-x-auto scrollbar-hide dark:bg-gray-950 bg-white", className)}>
            {children}
        </div>
    )
}

// Komponen Tabel Utama
const Table = React.forwardRef<HTMLTableElement, React.HTMLAttributes<HTMLTableElement>>(
    ({ className, ...props }, ref) => (
        <table
            ref={ref}
            className={cn("w-full caption-bottom text-sm", className)}
            {...props}
        />
    )
);

const TableHeader = React.forwardRef<HTMLTableSectionElement, React.HTMLAttributes<HTMLTableSectionElement>>(
    ({ className, ...props }, ref) => (
        <thead ref={ref} className={cn("bg-gray-50 dark:bg-gray-900/50 border-b dark:border-gray-800", className)} {...props} />
    )
);

const TableBody = React.forwardRef<HTMLTableSectionElement, React.HTMLAttributes<HTMLTableSectionElement>>(
    ({ className, ...props }, ref) => (
        <tbody ref={ref} className={cn("[&_tr:last-child]:border-0", className)} {...props} />
    )
);

const TableRow = React.forwardRef<HTMLTableRowElement, React.HTMLAttributes<HTMLTableRowElement>>(
    ({ className, ...props }, ref) => (
        <tr
            ref={ref}
            className={cn(
                "border-b dark:border-gray-800 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-900/50 data-[state=selected]:bg-muted",
                className
            )}
            {...props}
        />
    )
);

const TableHead = React.forwardRef<HTMLTableCellElement, React.ThHTMLAttributes<HTMLTableCellElement>>(
    ({ className, ...props }, ref) => (
        <th
            ref={ref}
            className={cn(
                "p-4 text-left align-middle font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap",
                className
            )}
            {...props}
        />
    )
);

const TableCell = React.forwardRef<HTMLTableCellElement, React.TdHTMLAttributes<HTMLTableCellElement>>(
    ({ className, ...props }, ref) => (
        <td
            ref={ref}
            className={cn("p-4 align-middle text-gray-700 dark:text-gray-400 whitespace-nowrap", className)}
            {...props}
        />
    )
);

// State kalau data kosong
const TableEmpty = ({ colSpan, message = "Data" } : { colSpan: number, message?: string }) => (
    <TableRow>
        <TableCell colSpan={colSpan} className="text-center py-10 text-muted-foreground">
            {message} tidak ditemukan.
        </TableCell>
    </TableRow>
);

// --- TABLE FILTER (UI Saja) ---
interface TableFilterProps {
    placeholder?: string;
    value?: string;
    onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
    children?: React.ReactNode; // Untuk naruh tombol "Tambah" atau Select
}

const TableFilter = ({ placeholder = "Cari...", value, onChange, children }: TableFilterProps) => {
    return (
        <div className="flex flex-col md:flex-row items-center justify-between gap-4 mb-4">
            <div className="relative w-full md:w-80">
                <Input
                    placeholder={placeholder}
                    value={value}
                    onChange={onChange}
                    className="w-full bg-white dark:bg-gray-900 shadow-sm"
                />
            </div>
            <div className="flex items-center gap-2 w-full md:w-auto">
                {children} {/* Nanti bisa buat naruh tombol Tambah Data di sini */}
            </div>
        </div>
    );
};

TableFilter.displayName = "TableFilter";

export {
    TableCard,
    Table,
    TableFilter,
    TableHeader,
    TableBody,
    TableHead,
    TableRow,
    TableCell,
    TableEmpty,
};
