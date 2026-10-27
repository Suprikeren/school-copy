import React, { useEffect, useState } from "react";
import AppLayout from "@/layouts/app-layout";
import { Header } from "@/components/header";
import {
    TableFilter,
    TableCard,
    Table,
    TableHead,
    TableHeader,
    TableRow,
    TableBody,
    TableCell,
    TableEmpty,
} from "@/components/ui/table";
import { PageProps } from "@/types";
import { PasalTatib } from "@/types/pasal-tatib";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Head, Link, router, usePage } from "@inertiajs/react";
import { PlusCircle } from "lucide-react";
import { ActionButton } from "@/components/action-button";
import { confirmDelete } from "@/utils/sweetAlert";
import Pagination from "@/components/ui/pagination";
import { useDebounce } from "use-debounce";

export default function Index() {
    const { pasal, filters } = usePage<PageProps<{ pasal: { data: PasalTatib[]; links: { url: string | null; label: string; active: boolean }[]; current_page: number; per_page: number }; currentPage: number; perPage: number; filters: { search?: string } }>>().props;

    const [searchTerm, setSearchTerm] = useState(filters?.search ?? "");
    const [debouncedSearch] = useDebounce(searchTerm, 500);

    useEffect(() => {
        if (debouncedSearch === (filters?.search ?? "")) return;

        router.get(
            route("dashboard.tatib.pasal.index"),
            { search: debouncedSearch },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch]);

    const handleModalDelete = (item: PasalTatib) => {
        confirmDelete(`Yakin ingin menghapus pasal "${item.nomor}"?`, () => {
            router.delete(route("dashboard.tatib.pasal.destroy", item.id), {
                preserveScroll: true,
            });
        });
    };

    return (
        <>
            <Head title="Pasal Tatib" />

            <Header
                title="Pasal Tata Tertib"
                subtitle="Halaman ini digunakan untuk mengelola data pasal tata tertib sekolah"
            >
                <Button asChild variant="outline">
                    <Link href={route("dashboard.tatib.pasal.create")}>
                        <PlusCircle className="size-4" />
                        <span className="hidden sm:inline-flex">
                            Tambah Data Pasal
                        </span>
                    </Link>
                </Button>
            </Header>

            <div className="p-6">
                <div className="mb-5">
                    <TableFilter
                        placeholder="Cari pasal berdasarkan nomor atau isi ringkas"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    />
                </div>

                <TableCard>
                    <Table title="pasal">
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[10px] text-center">
                                    No
                                </TableHead>
                                <TableHead>Nomor Pasal</TableHead>
                                <TableHead>Isi Ringkas</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Dibuat Oleh</TableHead>
                                <TableHead className="w-[10px] text-center">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {pasal.data.length === 0 ? (
                                <TableEmpty
                                    colSpan={6}
                                    message="Data pasal tata tertib"
                                />
                            ) : (
                                pasal.data.map((item, index) => (
                                    <TableRow key={item.id}>
                                        <TableCell className="text-center">
                                            {++index +
                                                (pasal.current_page - 1) *
                                                    pasal.per_page}
                                        </TableCell>
                                        <TableCell>{item.nomor}</TableCell>
                                        <TableCell className="max-w-[400px]">
                                            {item.isi_ringkas}
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                variant={
                                                    item.aktif
                                                        ? "default"
                                                        : "outline"
                                                }
                                            >
                                                {item.aktif
                                                    ? "Aktif"
                                                    : "Nonaktif"}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {item.created_by ?? "-"}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center justify-center">
                                                <ActionButton
                                                    withDetail
                                                    actionEditHref={route(
                                                        "dashboard.tatib.pasal.edit",
                                                        item.id
                                                    )}
                                                    actionDetailHref={route(
                                                        "dashboard.tatib.pasal.show",
                                                        item.id
                                                    )}
                                                    actionDelete={() =>
                                                        handleModalDelete(item)
                                                    }
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </TableCard>

                <div className="mt-4 flex w-full justify-end">
                    <Pagination links={pasal.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <AppLayout children={page} />;
