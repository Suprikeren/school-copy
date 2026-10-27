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
import { Siswa } from "@/types/siswa";
import { RiwayatKelas } from "@/types/riwayat-kelas";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Head, Link, router, usePage } from "@inertiajs/react";
import { PlusCircle } from "lucide-react";
import { ActionButton } from "@/components/action-button";
import { confirmDelete } from "@/utils/sweetAlert";
import Pagination from "@/components/ui/pagination";
import { useDebounce } from "use-debounce";

export default function Index() {
    const { siswa, filters } = usePage<PageProps<{ siswa: { data: (Siswa & { riwayat_kelas: Pick<RiwayatKelas, "id" | "kelas">[] })[]; links: { url: string | null; label: string; active: boolean }[]; current_page: number; per_page: number }; filters: { search?: string; status?: string } }>>().props;

    const [searchTerm, setSearchTerm] = useState(filters?.search ?? "");
    const [status, setStatus] = useState(filters?.status ?? "");
    const [debouncedSearch] = useDebounce(searchTerm, 500);

    useEffect(() => {
        if (
            debouncedSearch === (filters?.search ?? "") &&
            status === (filters?.status ?? "")
        ) {
            return;
        }

        router.get(
            route("dashboard.siswa.index"),
            { search: debouncedSearch, status },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch, status]);

    const handleModalDelete = (item: Siswa) => {
        confirmDelete(`Yakin ingin menghapus siswa "${item.nama}"?`, () => {
            router.delete(route("dashboard.siswa.destroy", item.id), {
                preserveScroll: true,
            });
        });
    };

    return (
        <>
            <Head title="Siswa" />

            <Header
                title="Data Siswa"
                subtitle="Halaman ini digunakan untuk mengelola data siswa"
            >
                <Button asChild variant="outline">
                    <Link href={route("dashboard.siswa.create")}>
                        <PlusCircle className="size-4" />
                        <span className="hidden sm:inline-flex">
                            Tambah Siswa
                        </span>
                    </Link>
                </Button>
            </Header>

            <div className="p-6">
                <div className="mb-5">
                    <TableFilter
                        placeholder="Cari siswa berdasarkan nama atau NIS"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    >
                        <select
                            className="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="alumni">Alumni</option>
                            <option value="pindah">Pindah</option>
                        </select>
                    </TableFilter>
                </div>

                <TableCard>
                    <Table title="siswa">
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[10px] text-center">
                                    No
                                </TableHead>
                                <TableHead>NIS</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Kelas</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="w-[10px] text-center">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {siswa.data.length === 0 ? (
                                <TableEmpty colSpan={6} message="Data siswa" />
                            ) : (
                                siswa.data.map((item, index) => (
                                    <TableRow key={item.id}>
                                        <TableCell className="text-center">
                                            {++index +
                                                (siswa.current_page - 1) *
                                                    siswa.per_page}
                                        </TableCell>
                                        <TableCell>{item.nis}</TableCell>
                                        <TableCell>{item.nama}</TableCell>
                                        <TableCell>
                                            {item.riwayat_kelas[0]?.kelas ?? "-"}
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                variant={
                                                    item.status === "aktif"
                                                        ? "default"
                                                        : "outline"
                                                }
                                                className="capitalize"
                                            >
                                                {item.status}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center justify-center">
                                                <ActionButton
                                                    withDetail
                                                    actionEditHref={route(
                                                        "dashboard.siswa.edit",
                                                        item.id
                                                    )}
                                                    actionDetailHref={route(
                                                        "dashboard.siswa.show",
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
                    <Pagination links={siswa.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <AppLayout children={page} />;
