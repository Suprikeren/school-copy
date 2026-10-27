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
import { RiwayatKelas } from "@/types/riwayat-kelas";
import { Siswa } from "@/types/siswa";
import { TahunAjaran } from "@/types/tahun-ajaran";
import { Button } from "@/components/ui/button";
import { Head, Link, router, usePage } from "@inertiajs/react";
import { PlusCircle } from "lucide-react";
import { ActionButton } from "@/components/action-button";
import { confirmDelete } from "@/utils/sweetAlert";
import Pagination from "@/components/ui/pagination";
import { useDebounce } from "use-debounce";

export default function Index() {
    const { riwayat, tahunAjaran, siswa, filters } =
        usePage<PageProps<{ riwayat: { data: (RiwayatKelas & { siswa?: Pick<Siswa, "id" | "nis" | "nama">; tahun_ajaran?: Pick<TahunAjaran, "id" | "nama"> })[]; links: { url: string | null; label: string; active: boolean }[]; current_page: number; per_page: number }; tahunAjaran: Pick<TahunAjaran, "id" | "nama">[]; siswa: Pick<Siswa, "id" | "nis" | "nama"> | null; filters: { search?: string; siswa_id?: string | number; tahun_ajaran_id?: string | number } }>>().props;

    const [searchTerm, setSearchTerm] = useState(filters?.search ?? "");
    const [tahunAjaranId, setTahunAjaranId] = useState(
        filters?.tahun_ajaran_id ?? ""
    );
    const [debouncedSearch] = useDebounce(searchTerm, 500);

    useEffect(() => {
        if (
            debouncedSearch === (filters?.search ?? "") &&
            tahunAjaranId === (filters?.tahun_ajaran_id ?? "")
        ) {
            return;
        }

        router.get(
            route("dashboard.riwayat-kelas.index"),
            {
                search: debouncedSearch,
                siswa_id: filters?.siswa_id,
                tahun_ajaran_id: tahunAjaranId,
            },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch, tahunAjaranId]);

    const handleModalDelete = (item: RiwayatKelas & { tahun_ajaran?: Pick<TahunAjaran, "id" | "nama"> }) => {
        confirmDelete(
            `Yakin ingin menghapus riwayat kelas ${item.kelas} (${item.tahun_ajaran?.nama ?? "-"})?`,
            () => {
                router.delete(
                    route("dashboard.riwayat-kelas.destroy", item.id),
                    { preserveScroll: true }
                );
            }
        );
    };

    const createHref = filters?.siswa_id
        ? route("dashboard.riwayat-kelas.create", {
              siswa_id: filters.siswa_id,
          })
        : route("dashboard.riwayat-kelas.create");

    return (
        <>
            <Head title="Riwayat Kelas" />

            <Header
                title="Riwayat Kelas"
                subtitle={
                    siswa
                        ? `Riwayat kelas ${siswa.nama} (${siswa.nis})`
                        : "Halaman ini digunakan untuk mengelola riwayat kelas siswa"
                }
            >
                <Button asChild variant="outline">
                    <Link href={createHref}>
                        <PlusCircle className="size-4" />
                        <span className="hidden sm:inline-flex">
                            Tambah Riwayat
                        </span>
                    </Link>
                </Button>
            </Header>

            <div className="p-6">
                <div className="mb-5">
                    <TableFilter
                        placeholder="Cari siswa, NIS, kelas, atau tahun ajaran"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    >
                        <select
                            className="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-sm"
                            value={tahunAjaranId}
                            onChange={(e) =>
                                setTahunAjaranId(e.target.value)
                            }
                        >
                            <option value="">Semua Tahun Ajaran</option>
                            {tahunAjaran.map((item) => (
                                <option key={item.id} value={item.id}>
                                    {item.nama}
                                </option>
                            ))}
                        </select>
                    </TableFilter>
                </div>

                <TableCard>
                    <Table title="riwayat-kelas">
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[10px] text-center">
                                    No
                                </TableHead>
                                <TableHead>NIS</TableHead>
                                <TableHead>Nama Siswa</TableHead>
                                <TableHead>Tahun Ajaran</TableHead>
                                <TableHead>Kelas</TableHead>
                                <TableHead className="w-[10px] text-center">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {riwayat.data.length === 0 ? (
                                <TableEmpty
                                    colSpan={6}
                                    message="Data riwayat kelas"
                                />
                            ) : (
                                riwayat.data.map((item, index) => (
                                    <TableRow key={item.id}>
                                        <TableCell className="text-center">
                                            {++index +
                                                (riwayat.current_page - 1) *
                                                    riwayat.per_page}
                                        </TableCell>
                                        <TableCell>
                                            {item.siswa?.nis ?? "-"}
                                        </TableCell>
                                        <TableCell>
                                            {item.siswa?.nama ?? "-"}
                                        </TableCell>
                                        <TableCell>
                                            {item.tahun_ajaran?.nama ?? "-"}
                                        </TableCell>
                                        <TableCell>{item.kelas}</TableCell>
                                        <TableCell>
                                            <div className="flex items-center justify-center">
                                                <ActionButton
                                                    withDetail={false}
                                                    actionEditHref={route(
                                                        "dashboard.riwayat-kelas.edit",
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
                    <Pagination links={riwayat.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <AppLayout children={page} />;
