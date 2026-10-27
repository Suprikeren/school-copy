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
import { PengajuanPerubahan } from "@/types/pengajuan-perubahan";
import { Pelanggaran } from "@/types/pelanggaran";
import { Siswa } from "@/types/siswa";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Head, Link, router, usePage } from "@inertiajs/react";
import Pagination from "@/components/ui/pagination";
import { useDebounce } from "use-debounce";

export default function Index() {
    const { pengajuan, filters } = usePage<PageProps<{ pengajuan: { data: (PengajuanPerubahan & { pelanggaran?: Pick<Pelanggaran, "id" | "nama_pelanggaran" | "kelas" | "poin" | "pasal"> & { siswa?: Pick<Siswa, "id" | "nis" | "nama"> }; user?: Pick<User, "id" | "name"> })[]; links: { url: string | null; label: string; active: boolean }[]; current_page: number; per_page: number }; filters: { search?: string; status?: string } }>>().props;

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
            route("dashboard.pengajuan.index"),
            { search: debouncedSearch, status },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch, status]);

    const badgeVariant = (value: string) => {
        if (value === "disetujui") return "default";
        if (value === "ditolak") return "destructive";

        return "outline";
    };

    return (
        <>
            <Head title="Pengajuan Perubahan" />

            <Header
                title="Pengajuan Perubahan"
                subtitle="Antrean koreksi data pelanggaran"
            />

            <div className="p-6">
                <div className="mb-5">
                    <TableFilter
                        placeholder="Cari siswa, pengaju, atau alasan"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    >
                        <select
                            className="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">Semua Status</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </TableFilter>
                </div>

                <TableCard>
                    <Table title="pengajuan">
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[10px] text-center">
                                    No
                                </TableHead>
                                <TableHead>Waktu</TableHead>
                                <TableHead>Siswa</TableHead>
                                <TableHead>Pelanggaran</TableHead>
                                <TableHead>Pengaju</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="w-[10px] text-center">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {pengajuan.data.length === 0 ? (
                                <TableEmpty
                                    colSpan={7}
                                    message="Data pengajuan"
                                />
                            ) : (
                                pengajuan.data.map((item, index) => (
                                    <TableRow key={item.id}>
                                        <TableCell className="text-center">
                                            {++index +
                                                (pengajuan.current_page - 1) *
                                                    pengajuan.per_page}
                                        </TableCell>
                                        <TableCell>
                                            {new Date(
                                                item.created_at
                                            ).toLocaleString("id-ID")}
                                        </TableCell>
                                        <TableCell>
                                            {item.pelanggaran?.siswa?.nama ??
                                                "-"}
                                        </TableCell>
                                        <TableCell>
                                            {item.pelanggaran
                                                ?.nama_pelanggaran ?? "-"}
                                        </TableCell>
                                        <TableCell>
                                            {item.user?.name ??
                                                "-"}
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                variant={badgeVariant(
                                                    item.status
                                                )}
                                                className="capitalize"
                                            >
                                                {item.status}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                asChild
                                            >
                                                <Link
                                                    href={route(
                                                        "dashboard.pengajuan.show",
                                                        item.id
                                                    )}
                                                >
                                                    Detail
                                                </Link>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </TableCard>

                <div className="mt-4 flex w-full justify-end">
                    <Pagination links={pengajuan.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <AppLayout children={page} />;
