import React, { useMemo, useState } from "react";
import AppLayout from "@/layouts/app-layout";
import { Head, useForm, Link, usePage } from "@inertiajs/react";
import { Header } from "@/components/header";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ArrowLeft, LoaderCircle, Save, Search } from "lucide-react";
import { PageProps } from "@/types";
import { Siswa } from "@/types/siswa";
import { RiwayatKelas } from "@/types/riwayat-kelas";
import { JenisPelanggaran } from "@/types/jenis-pelanggaran";
import { PasalTatib } from "@/types/pasal-tatib";
import { TahunAjaran } from "@/types/tahun-ajaran";

export default function Create() {
    const { siswa, jenis, tahunAjaran } = usePage<PageProps<{ siswa: (Pick<Siswa, "id" | "nis" | "nama"> & { riwayat_kelas: Pick<RiwayatKelas, "id" | "kelas">[] })[]; jenis: (Pick<JenisPelanggaran, "id" | "nama" | "kategori" | "poin"> & { pasal_tatib: Pick<PasalTatib, "id" | "nomor">[] })[]; tahunAjaran: Pick<TahunAjaran, "id" | "nama"> | null }>>().props;

    const [siswaSearch, setSiswaSearch] = useState("");

    const { data, setData, post, processing, errors, reset } = useForm({
        siswa_ids: [] as number[],
        jenis_pelanggaran_id: "",
        keterangan: "",
        foto: null as File | null,
    });

    const siswaTerfilter = useMemo(() => {
        const keyword = siswaSearch.toLowerCase();

        return siswa.filter(
            (item) =>
                item.nama.toLowerCase().includes(keyword) ||
                item.nis.toLowerCase().includes(keyword)
        );
    }, [siswa, siswaSearch]);

    const jenisTerpilih = jenis.find(
        (item) => String(item.id) === String(data.jenis_pelanggaran_id)
    );

    const toggleSiswa = (id: number, checked: boolean) => {
        if (checked) {
            setData("siswa_ids", [...data.siswa_ids, id]);
        } else {
            setData(
                "siswa_ids",
                data.siswa_ids.filter((siswaId) => siswaId !== id)
            );
        }
    };

    const storeData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("dashboard.pelanggaran.store"), {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <>
            <Head title="Input Pelanggaran" />
            <div className="w-full">
                <Header
                    title="Input Pelanggaran"
                    subtitle="Halaman ini digunakan untuk mencatat pelanggaran siswa"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Form Pelanggaran</CardTitle>
                            <CardDescription>
                                Tahun ajaran aktif:{" "}
                                {tahunAjaran?.nama ?? "belum ditentukan"}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={storeData}>
                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Jenis Pelanggaran</Label>
                                    <select
                                        className="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                        value={data.jenis_pelanggaran_id}
                                        onChange={(e) =>
                                            setData(
                                                "jenis_pelanggaran_id",
                                                e.target.value
                                            )
                                        }
                                    >
                                        <option value="">
                                            Pilih jenis pelanggaran
                                        </option>
                                        {jenis.map((item) => (
                                            <option
                                                key={item.id}
                                                value={item.id}
                                            >
                                                {item.nama} ({item.poin} poin)
                                            </option>
                                        ))}
                                    </select>
                                    <p className="text-xs text-red-500">
                                        {errors.jenis_pelanggaran_id}
                                    </p>
                                    {jenisTerpilih && (
                                        <div className="flex flex-wrap items-center gap-2 rounded-md border p-3 text-sm">
                                            <span>
                                                Poin:{" "}
                                                <strong>
                                                    {jenisTerpilih.poin}
                                                </strong>
                                            </span>
                                            <span className="text-muted-foreground">
                                                Pasal:
                                            </span>
                                            {jenisTerpilih.pasal_tatib.length ===
                                            0 ? (
                                                <Badge variant="outline">
                                                    Belum dipetakan
                                                </Badge>
                                            ) : (
                                                jenisTerpilih.pasal_tatib.map(
                                                    (pasal) => (
                                                        <Badge
                                                            key={pasal.id}
                                                            variant="outline"
                                                        >
                                                            {pasal.nomor}
                                                        </Badge>
                                                    )
                                                )
                                            )}
                                        </div>
                                    )}
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Siswa</Label>
                                    <div className="relative">
                                        <Search className="absolute left-3 top-2.5 size-4 text-muted-foreground" />
                                        <Input
                                            className="pl-9"
                                            placeholder="Cari siswa berdasarkan nama atau NIS"
                                            value={siswaSearch}
                                            onChange={(e) =>
                                                setSiswaSearch(e.target.value)
                                            }
                                        />
                                    </div>
                                    <div className="grid max-h-64 gap-2 overflow-y-auto rounded-md border p-4 md:grid-cols-2">
                                        {siswaTerfilter.map((item) => (
                                            <label
                                                key={item.id}
                                                className="flex items-center gap-2 text-sm"
                                            >
                                                <input
                                                    type="checkbox"
                                                    className="size-4 rounded border"
                                                    checked={data.siswa_ids.includes(
                                                        item.id
                                                    )}
                                                    onChange={(e) =>
                                                        toggleSiswa(
                                                            item.id,
                                                            e.target.checked
                                                        )
                                                    }
                                                />
                                                <span>
                                                    {item.nama} ({item.nis}) -{" "}
                                                    {item.riwayat_kelas[0]
                                                        ?.kelas ?? "-"}
                                                </span>
                                            </label>
                                        ))}
                                    </div>
                                    <p className="text-xs text-red-500">
                                        {errors.siswa_ids}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Keterangan</Label>
                                    <textarea
                                        className="min-h-24 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm"
                                        value={data.keterangan}
                                        onChange={(e) =>
                                            setData(
                                                "keterangan",
                                                e.target.value
                                            )
                                        }
                                        placeholder="Keterangan tambahan (opsional)"
                                    />
                                    <p className="text-xs text-red-500">
                                        {errors.keterangan}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Foto Bukti (opsional)</Label>
                                    <Input
                                        type="file"
                                        accept="image/*"
                                        onChange={(e) =>
                                            setData(
                                                "foto",
                                                e.target.files?.[0] ?? null
                                            )
                                        }
                                    />
                                    <p className="text-xs text-red-500">
                                        {errors.foto}
                                    </p>
                                </div>

                                <div className="flex items-center gap-2">
                                    <Button variant="danger" asChild>
                                        <Link href="/dashboard/pelanggaran">
                                            <ArrowLeft /> Kembali
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="default"
                                        type="submit"
                                        disabled={processing}
                                    >
                                        {processing ? (
                                            <LoaderCircle className="animate-spin" />
                                        ) : (
                                            <Save />
                                        )}{" "}
                                        Simpan Pelanggaran
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

Create.layout = (page: React.ReactNode) => <AppLayout children={page} />;
