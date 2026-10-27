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
import { JenisPelanggaran } from "@/types/jenis-pelanggaran";
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
    const { jenis, filters } = usePage<PageProps<{ jenis: { data: (JenisPelanggaran & { pasal_tatib: Pick<PasalTatib, "id" | "nomor">[] })[]; links: { url: string | null; label: string; active: boolean }[]; current_page: number; per_page: number }; currentPage: number; perPage: number; filters: { search?: string } }>>().props;

    const [searchTerm, setSearchTerm] = useState(filters?.search ?? "");
    const [debouncedSearch] = useDebounce(searchTerm, 500);

    useEffect(() => {
        if (debouncedSearch === (filters?.search ?? "")) return;

        router.get(
            route("dashboard.tatib.jenis-pelanggaran.index"),
            { search: debouncedSearch },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch]);

    const handleModalDelete = (item: JenisPelanggaran) => {
        confirmDelete(`Yakin ingin menghapus "${item.nama}"?`, () => {
            router.delete(
                route("dashboard.tatib.jenis-pelanggaran.destroy", item.id),
                {
                    preserveScroll: true,
                }
            );
        });
    };

    return (
        <>
            <Head title="Jenis Pelanggaran" />

            <Header
                title="Jenis Pelanggaran"
                subtitle="Halaman ini digunakan untuk mengelola jenis pelanggaran, poin, dan pasal"
            >
                <Button asChild variant="outline">
                    <Link
                        href={route(
                            "dashboard.tatib.jenis-pelanggaran.create"
                        )}
                    >
                        <PlusCircle className="size-4" />
                        <span className="hidden sm:inline-flex">
                            Tambah Jenis Pelanggaran
                        </span>
                    </Link>
                </Button>
            </Header>

            <div className="p-6">
                <div className="mb-5">
                    <TableFilter
                        placeholder="Cari jenis pelanggaran berdasarkan nama atau kategori"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    />
                </div>

                <TableCard>
                    <Table title="jenis-pelanggaran">
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[10px] text-center">
                                    No
                                </TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Kategori</TableHead>
                                <TableHead>Poin</TableHead>
                                <TableHead>Pasal</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="w-[10px] text-center">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {jenis.data.length === 0 ? (
                                <TableEmpty
                                    colSpan={7}
                                    message="Data jenis pelanggaran"
                                />
                            ) : (
                                jenis.data.map((item, index) => (
                                    <TableRow key={item.id}>
                                        <TableCell className="text-center">
                                            {++index +
                                                (jenis.current_page - 1) *
                                                    jenis.per_page}
                                        </TableCell>
                                        <TableCell>{item.nama}</TableCell>
                                        <TableCell className="capitalize">
                                            {item.kategori}
                                        </TableCell>
                                        <TableCell>{item.poin}</TableCell>
                                        <TableCell>
                                            <div className="flex max-w-[300px] flex-wrap gap-1">
                                                {item.pasal_tatib.map((pasal) => (
                                                    <Badge
                                                        key={pasal.id}
                                                        variant="outline"
                                                    >
                                                        {pasal.nomor}
                                                    </Badge>
                                                ))}
                                            </div>
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
                                            <div className="flex items-center justify-center">
                                                <ActionButton
                                                    withDetail
                                                    actionEditHref={route(
                                                        "dashboard.tatib.jenis-pelanggaran.edit",
                                                        item.id
                                                    )}
                                                    actionDetailHref={route(
                                                        "dashboard.tatib.jenis-pelanggaran.show",
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
                    <Pagination links={jenis.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <AppLayout children={page} />;
