import React from "react";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuItem
} from "@/components/ui/dropdown-menu";
import { Ellipsis, Edit, Trash, CircleArrowRight } from "lucide-react";
import { Link } from "@inertiajs/react";

interface ActionButtonProps {
    isModal?: boolean;
    withEdit?: boolean;
    withDetail?: boolean;
    withDelete?: boolean;
    actionEdit?: () => void;
    actionDelete?: () => void;
    actionEditHref?: string;
    actionDetailHref?: string;
    // permissionPrefix dihapus karena tidak dipakai lagi
    children?: React.ReactNode
}

export function ActionButton({
    withDetail = true,
    withEdit = true,
    withDelete = true,
    isModal = false,
    actionEdit,
    actionDetailHref = '',
    actionEditHref = '',
    actionDelete,
    children
}: ActionButtonProps) {

    return (
        <DropdownMenu>
            <DropdownMenuTrigger className="focus-within:outline-none cursor-pointer">
                <Ellipsis className="h-5 w-5 text-gray-500 hover:text-gray-900" />
            </DropdownMenuTrigger>
            <DropdownMenuContent side="left" align="start" className="w-40">
                <DropdownMenuLabel>Aksi</DropdownMenuLabel>
                <DropdownMenuSeparator />

                {children}

                {/* Tombol Detail */}
                {withDetail && (
                    <DropdownMenuItem asChild>
                        <Link href={actionDetailHref} className="flex items-center gap-2 cursor-pointer">
                            <CircleArrowRight className="h-4 w-4" /> Detail
                        </Link>
                    </DropdownMenuItem>
                )}

                {/* Tombol Edit (Kondisional: Modal atau Link Href) */}
                {withEdit && (
                    isModal ? (
                        <DropdownMenuItem onClick={actionEdit} className="flex items-center gap-2 cursor-pointer">
                            <Edit className="h-4 w-4" /> Ubah
                        </DropdownMenuItem>
                    ) : (
                        <DropdownMenuItem asChild>
                            <Link href={actionEditHref} className="flex items-center gap-2 cursor-pointer">
                                <Edit className="h-4 w-4" /> Ubah
                            </Link>
                        </DropdownMenuItem>
                    )
                )}

                {/* Tombol Delete */}
                {withDelete && (
                    <DropdownMenuItem
                        onClick={actionDelete}
                        className="flex items-center gap-2 cursor-pointer text-red-600 focus:text-red-600"
                    >
                        <Trash className="h-4 w-4" /> Hapus
                    </DropdownMenuItem>
                )}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
