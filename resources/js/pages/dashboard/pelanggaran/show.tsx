import React from "react";
import AppLayout from "@/layouts/app-layout";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import { Header } from "@/components/header";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { ArrowLeft, LoaderCircle, Send } from "lucide-react";
import { PageProps, User } from "@/types";
import { Pelanggaran } from "@/types/pelanggaran";
import { PengajuanPerubahan } from "@/types/pengajuan-perubahan";
import { Siswa } from "@/types/siswa";
import { JenisPelanggaran } from "@/types/jenis-pelanggaran";
import { Kasus } from "@/types/kasus";
import { TahunAjaran } from "@/types/tahun-ajaran";

export default function Show() {
    const { pelanggaran, pengajuan, bisaAjukan } = usePage<PageProps<{ pelanggaran: Pelanggaran & { siswa: Pick<Siswa, "id" | "nis" | "nama">; jenis_pelanggaran: Pick<JenisPelanggaran, "id" | "nama" | "kategori" | "poin">; kasus: Pick<Kasus, "id" | "waktu" | "keterangan" | "foto_path"> & { pencatat?: Pick<User, "id" | "name">; tahun_ajaran?: Pick<TahunAjaran, "id" | "nama"> } }; pengajuan: PengajuanPerubahan[]; bisaAjukan: boolean }>>().props;

    const { data, setData, post, processing, errors, reset } = useForm({
        pelanggaran_id: pelanggaran.id,
        alasan: "",
        data_baru: {
            kelas: pelanggaran.kelas,
            poin: pelanggaran.poin,
            pasal: pelanggaran.pasal,
        },
    });

    const ajukanPerubahan = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("dashboard.pengajuan.store"), {
            onSuccess: () => {
                reset("alasan");
            },
        });
    };

    return (
        <>
            <Head title="Detail Pelanggaran" />
            <div className="w-full">
                <Header
                    title="Detail Pelanggaran"
                    subtitle={`${pelanggaran.siswa.nama} - ${pelanggaran.nama_pelanggaran}`}
                />
                <div className="p-6 space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Pelanggaran</CardTitle>
                            <CardDescription>
                                Dicatat oleh{" "}
                                {pelanggaran.kasus.pencatat?.name ?? "-"} pada{" "}
                                {new Date(
                                    pelanggaran.kasus.waktu
                                ).toLocaleString("id-ID")}
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3 text-sm">
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Siswa
                                </span>
                                <span>
                                    {pelanggaran.siswa.nama} (
                                    {pelanggaran.siswa.nis})
                                </span>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Kelas
                                </span>
                                <span>{pelanggaran.kelas}</span>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Tahun Ajaran
                                </span>
                                <span>
                                    {pelanggaran.kasus.tahun_ajaran?.nama ?? "-"}
                                </span>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Pelanggaran
                                </span>
                                <span>{pelanggaran.nama_pelanggaran}</span>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Pasal
                                </span>
                                <span>{pelanggaran.pasal || "-"}</span>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Poin
                                </span>
                                <span>{pelanggaran.poin}</span>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Keterangan
                                </span>
                                <span>{pelanggaran.kasus.keterangan ?? "-"}</span>
                            </div>
                            {pelanggaran.kasus.foto_path && (
                                <div className="flex gap-2">
                                    <span className="w-40 text-muted-foreground">
                                        Foto Bukti
                                    </span>
                                    <a
                                        className="text-primary underline"
                                        href={`/storage/${pelanggaran.kasus.foto_path}`}
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        Lihat foto
                                    </a>
                                </div>
                            )}
                            <div className="pt-2">
                                <Button variant="danger" asChild>
                                    <Link href="/dashboard/pelanggaran">
                                        <ArrowLeft /> Kembali
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Riwayat Pengajuan Perubahan</CardTitle>
                            <CardDescription>
                                Koreksi data pelanggaran lewat persetujuan super
                                admin
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3 text-sm">
                            {pengajuan.length === 0 ? (
                                <p className="text-muted-foreground">
                                    Belum ada pengajuan perubahan.
                                </p>
                            ) : (
                                pengajuan.map((item) => (
                                    <div
                                        key={item.id}
                                        className="rounded-md border p-3"
                                    >
                                        <div className="mb-1 flex items-center gap-2">
                                            <Badge
                                                variant={
                                                    item.status === "disetujui"
                                                        ? "default"
                                                        : item.status === "ditolak"
                                                          ? "destructive"
                                                          : "outline"
                                                }
                                                className="capitalize"
                                            >
                                                {item.status}
                                            </Badge>
                                            <span className="text-muted-foreground">
                                                {new Date(
                                                    item.created_at
                                                ).toLocaleString("id-ID")}
                                            </span>
                                        </div>
                                        <p>Alasan: {item.alasan}</p>
                                        {item.catatan_keputusan && (
                                            <p>
                                                Keputusan:{" "}
                                                {item.catatan_keputusan}
                                            </p>
                                        )}
                                    </div>
                                ))
                            )}

                            {bisaAjukan && (
                                <form
                                    onSubmit={ajukanPerubahan}
                                    className="space-y-3 border-t pt-4"
                                >
                                    <p className="font-semibold">
                                        Ajukan Perubahan
                                    </p>
                                    <div className="grid gap-3 md:grid-cols-3">
                                        <div className="flex flex-col gap-1">
                                            <Label>Kelas</Label>
                                            <Input
                                                value={data.data_baru.kelas}
                                                onChange={(e) =>
                                                    setData("data_baru", {
                                                        ...data.data_baru,
                                                        kelas: e.target.value,
                                                    })
                                                }
                                            />
                                        </div>
                                        <div className="flex flex-col gap-1">
                                            <Label>Poin</Label>
                                            <Input
                                                type="number"
                                                value={data.data_baru.poin}
                                                onChange={(e) =>
                                                    setData("data_baru", {
                                                        ...data.data_baru,
                                                        poin: Number(
                                                            e.target.value
                                                        ),
                                                    })
                                                }
                                            />
                                        </div>
                                        <div className="flex flex-col gap-1">
                                            <Label>Pasal</Label>
                                            <Input
                                                value={data.data_baru.pasal}
                                                onChange={(e) =>
                                                    setData("data_baru", {
                                                        ...data.data_baru,
                                                        pasal: e.target.value,
                                                    })
                                                }
                                            />
                                        </div>
                                    </div>
                                    <div className="flex flex-col gap-1">
                                        <Label>Alasan Koreksi</Label>
                                        <textarea
                                            className="min-h-20 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm"
                                            value={data.alasan}
                                            onChange={(e) =>
                                                setData(
                                                    "alasan",
                                                    e.target.value
                                                )
                                            }
                                        />
                                        <p className="text-xs text-red-500">
                                            {errors.alasan}
                                        </p>
                                    </div>
                                    <Button
                                        type="submit"
                                        disabled={processing}
                                    >
                                        {processing ? (
                                            <LoaderCircle className="animate-spin" />
                                        ) : (
                                            <Send />
                                        )}{" "}
                                        Kirim Pengajuan
                                    </Button>
                                </form>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

Show.layout = (page: React.ReactNode) => <AppLayout children={page} />;
