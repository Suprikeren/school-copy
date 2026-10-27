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
import { PageProps, User } from "@/types";
import { TindakLanjut } from "@/types/tindak-lanjut";
import { Siswa } from "@/types/siswa";
import { SpSiswa } from "@/types/sp-siswa";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Head, Link, router, usePage } from "@inertiajs/react";
import { PlusCircle } from "lucide-react";
import { ActionButton } from "@/components/action-button";
import { confirmDelete } from "@/utils/sweetAlert";
import Pagination from "@/components/ui/pagination";
import { useDebounce } from "use-debounce";

export default function Index() {
    const { tindak, filters } = usePage<PageProps<{ tindak: { data: (TindakLanjut & { siswa?: Pick<Siswa, "id" | "nis" | "nama">; user?: Pick<User, "id" | "name">; sp_siswa?: Pick<SpSiswa, "id" | "level"> })[]; links: { url: string | null; label: string; active: boolean }[]; current_page: number; per_page: number }; filters: { search?: string; status?: string } }>>().props;

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
            route("dashboard.tindak-lanjut.index"),
            { search: debouncedSearch, status },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch, status]);

    const handleModalDelete = (item: TindakLanjut) => {
        confirmDelete(`Yakin ingin menghapus tindak lanjut ini?`, () => {
            router.delete(
                route("dashboard.tindak-lanjut.destroy", item.id),
                {
                    preserveScroll: true,
                }
            );
        });
    };

    return (
        <>
            <Head title="Tindak Lanjut" />

            <Header
                title="Tindak Lanjut"
                subtitle="Halaman ini digunakan untuk mencatat pembinaan siswa"
            >
                <Button asChild variant="outline">
                    <Link href={route("dashboard.tindak-lanjut.create")}>
                        <PlusCircle className="size-4" />
                        <span className="hidden sm:inline-flex">
                            Tambah Tindak Lanjut
                        </span>
                    </Link>
                </Button>
            </Header>

            <div className="p-6">
                <div className="mb-5">
                    <TableFilter
                        placeholder="Cari siswa atau tindakan"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    >
                        <select
                            className="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">Semua Status</option>
                            <option value="belum">Belum</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </TableFilter>
                </div>

                <TableCard>
                    <Table title="tindak-lanjut">
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[10px] text-center">
                                    No
                                </TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Siswa</TableHead>
                                <TableHead>Tindakan</TableHead>
                                <TableHead>SP</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Ditangani Oleh</TableHead>
                                <TableHead className="w-[10px] text-center">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {tindak.data.length === 0 ? (
                                <TableEmpty
                                    colSpan={8}
                                    message="Data tindak lanjut"
                                />
                            ) : (
                                tindak.data.map((item, index) => (
                                    <TableRow key={item.id}>
                                        <TableCell className="text-center">
                                            {++index +
                                                (tindak.current_page - 1) *
                                                    tindak.per_page}
                                        </TableCell>
                                        <TableCell>
                                            {new Date(
                                                item.tanggal
                                            ).toLocaleDateString("id-ID")}
                                        </TableCell>
                                        <TableCell>
                                            {item.siswa?.nama ?? "-"}
                                        </TableCell>
                                        <TableCell>{item.tindakan}</TableCell>
                                        <TableCell>
                                            {item.sp_siswa
                                                ? `SP ${item.sp_siswa.level}`
                                                : "-"}
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                variant={
                                                    item.status === "selesai"
                                                        ? "default"
                                                        : "outline"
                                                }
                                                className="capitalize"
                                            >
                                                {item.status}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {item.user?.name ??
                                                "-"}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center justify-center">
                                                <ActionButton
                                                    withDetail={false}
                                                    actionEditHref={route(
                                                        "dashboard.tindak-lanjut.edit",
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
                    <Pagination links={tindak.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <AppLayout children={page} />;
