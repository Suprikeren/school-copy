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
import { Pelanggaran } from "@/types/pelanggaran";
import { Siswa } from "@/types/siswa";
import { JenisPelanggaran } from "@/types/jenis-pelanggaran";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Head, Link, router, usePage } from "@inertiajs/react";
import { PlusCircle } from "lucide-react";
import Pagination from "@/components/ui/pagination";
import { useDebounce } from "use-debounce";

export default function Index() {
    const { pelanggaran, kelas, filters } = usePage<PageProps<{ pelanggaran: { data: (Pelanggaran & { siswa?: Pick<Siswa, "id" | "nis" | "nama">; jenis_pelanggaran?: Pick<JenisPelanggaran, "id" | "nama" | "poin">; kasus?: { id: number; waktu: string; pencatat?: Pick<User, "id" | "name"> } })[]; links: { url: string | null; label: string; active: boolean }[]; current_page: number; per_page: number }; kelas: string[]; filters: { search?: string; kelas?: string; tanggal?: string } }>>().props;

    const [searchTerm, setSearchTerm] = useState(filters?.search ?? "");
    const [kelasFilter, setKelasFilter] = useState(filters?.kelas ?? "");
    const [tanggal, setTanggal] = useState(filters?.tanggal ?? "");
    const [debouncedSearch] = useDebounce(searchTerm, 500);

    useEffect(() => {
        if (
            debouncedSearch === (filters?.search ?? "") &&
            kelasFilter === (filters?.kelas ?? "") &&
            tanggal === (filters?.tanggal ?? "")
        ) {
            return;
        }

        router.get(
            route("dashboard.pelanggaran.index"),
            { search: debouncedSearch, kelas: kelasFilter, tanggal },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch, kelasFilter, tanggal]);

    return (
        <>
            <Head title="Pelanggaran" />

            <Header
                title="Data Pelanggaran"
                subtitle="Halaman ini digunakan untuk melihat seluruh input pelanggaran"
            >
                <Button asChild variant="outline">
                    <Link href={route("dashboard.pelanggaran.create")}>
                        <PlusCircle className="size-4" />
                        <span className="hidden sm:inline-flex">
                            Input Pelanggaran
                        </span>
                    </Link>
                </Button>
            </Header>

            <div className="p-6">
                <div className="mb-5">
                    <TableFilter
                        placeholder="Cari siswa, pelanggaran, pasal, atau pencatat"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    >
                        <select
                            className="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-sm"
                            value={kelasFilter}
                            onChange={(e) => setKelasFilter(e.target.value)}
                        >
                            <option value="">Semua Kelas</option>
                            {kelas.map((item) => (
                                <option key={item} value={item}>
                                    {item}
                                </option>
                            ))}
                        </select>
                        <input
                            type="date"
                            className="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-sm"
                            value={tanggal}
                            onChange={(e) => setTanggal(e.target.value)}
                        />
                    </TableFilter>
                </div>

                <TableCard>
                    <Table title="pelanggaran">
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[10px] text-center">
                                    No
                                </TableHead>
                                <TableHead>Waktu</TableHead>
                                <TableHead>Siswa</TableHead>
                                <TableHead>Kelas</TableHead>
                                <TableHead>Pelanggaran</TableHead>
                                <TableHead>Pasal</TableHead>
                                <TableHead>Poin</TableHead>
                                <TableHead>Pencatat</TableHead>
                                <TableHead className="w-[10px] text-center">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {pelanggaran.data.length === 0 ? (
                                <TableEmpty
                                    colSpan={9}
                                    message="Data pelanggaran"
                                />
                            ) : (
                                pelanggaran.data.map((item, index) => (
                                    <TableRow key={item.id}>
                                        <TableCell className="text-center">
                                            {++index +
                                                (pelanggaran.current_page - 1) *
                                                    pelanggaran.per_page}
                                        </TableCell>
                                        <TableCell>
                                            {new Date(
                                                item.kasus?.waktu ?? item.created_at
                                            ).toLocaleString("id-ID")}
                                        </TableCell>
                                        <TableCell>
                                            {item.siswa?.nama ?? "-"}
                                            <span className="ml-1 text-xs text-muted-foreground">
                                                ({item.siswa?.nis ?? "-"})
                                            </span>
                                        </TableCell>
                                        <TableCell>{item.kelas}</TableCell>
                                        <TableCell>{item.nama_pelanggaran}</TableCell>
                                        <TableCell className="max-w-[220px] whitespace-normal">
                                            {item.pasal || (
                                                <Badge variant="outline">
                                                    Belum dipetakan
                                                </Badge>
                                            )}
                                        </TableCell>
                                        <TableCell>{item.poin}</TableCell>
                                        <TableCell>
                                            {item.kasus?.pencatat?.name ?? "-"}
                                        </TableCell>
                                        <TableCell>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                asChild
                                            >
                                                <Link
                                                    href={route(
                                                        "dashboard.pelanggaran.show",
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
                    <Pagination links={pelanggaran.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <AppLayout children={page} />;
