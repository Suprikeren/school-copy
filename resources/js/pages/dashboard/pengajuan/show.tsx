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
import { Label } from "@/components/ui/label";
import { ArrowLeft, Check, LoaderCircle, X } from "lucide-react";
import { PageProps, User } from "@/types";
import { PengajuanPerubahan } from "@/types/pengajuan-perubahan";
import { Pelanggaran } from "@/types/pelanggaran";
import { Siswa } from "@/types/siswa";

export default function Show() {
    const { pengajuan, bisaPutuskan } = usePage<PageProps<{ pengajuan: PengajuanPerubahan & { pelanggaran: Pick<Pelanggaran, "id" | "nama_pelanggaran" | "kelas" | "poin" | "pasal"> & { siswa?: Pick<Siswa, "id" | "nis" | "nama"> }; user?: Pick<User, "id" | "name">; pemutus?: Pick<User, "id" | "name"> }; bisaPutuskan: boolean }>>().props;

    const { data, setData, put, processing, errors } = useForm({
        keputusan: "disetujui",
        catatan_keputusan: "",
    });

    const putuskan = (keputusan: "disetujui" | "ditolak") => {
        setData("keputusan", keputusan);

        put(route("dashboard.pengajuan.putuskan", pengajuan.id), {
            onSuccess: () => setData("catatan_keputusan", ""),
        });
    };

    return (
        <>
            <Head title="Detail Pengajuan Perubahan" />
            <div className="w-full">
                <Header
                    title="Detail Pengajuan Perubahan"
                    subtitle={`${pengajuan.pelanggaran?.siswa?.nama ?? "-"} - ${
                        pengajuan.pelanggaran?.nama_pelanggaran ?? "-"
                    }`}
                />
                <div className="p-6 space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Pengajuan</CardTitle>
                            <CardDescription>
                                Diajukan oleh{" "}
                                {pengajuan.user?.name ?? "-"} pada{" "}
                                {new Date(
                                    pengajuan.created_at
                                ).toLocaleString("id-ID")}
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3 text-sm">
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Status
                                </span>
                                <Badge
                                    variant={
                                        pengajuan.status === "disetujui"
                                            ? "default"
                                            : pengajuan.status === "ditolak"
                                              ? "destructive"
                                              : "outline"
                                    }
                                    className="capitalize"
                                >
                                    {pengajuan.status}
                                </Badge>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-40 text-muted-foreground">
                                    Alasan
                                </span>
                                <span>{pengajuan.alasan}</span>
                            </div>
                            {pengajuan.catatan_keputusan && (
                                <div className="flex gap-2">
                                    <span className="w-40 text-muted-foreground">
                                        Catatan Keputusan
                                    </span>
                                    <span>{pengajuan.catatan_keputusan}</span>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Data Lama vs Data Baru</CardTitle>
                            <CardDescription>
                                Perbandingan koreksi yang diajukan
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="rounded-md border p-4 text-sm">
                                    <p className="mb-2 font-semibold">
                                        Data Lama
                                    </p>
                                    <p>Kelas: {String(pengajuan.data_lama.kelas ?? "-")}</p>
                                    <p>Poin: {String(pengajuan.data_lama.poin ?? "-")}</p>
                                    <p>Pasal: {String(pengajuan.data_lama.pasal ?? "-")}</p>
                                </div>
                                <div className="rounded-md border p-4 text-sm">
                                    <p className="mb-2 font-semibold">
                                        Data Baru
                                    </p>
                                    <p>Kelas: {String(pengajuan.data_baru.kelas ?? "-")}</p>
                                    <p>Poin: {String(pengajuan.data_baru.poin ?? "-")}</p>
                                    <p>Pasal: {String(pengajuan.data_baru.pasal ?? "-")}</p>
                                </div>
                            </div>

                            {bisaPutuskan && pengajuan.status === "menunggu" && (
                                <div className="mt-4 space-y-3 border-t pt-4">
                                    <div className="flex flex-col gap-1">
                                        <Label>Catatan Keputusan</Label>
                                        <textarea
                                            className="min-h-20 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm"
                                            value={data.catatan_keputusan}
                                            onChange={(e) =>
                                                setData(
                                                    "catatan_keputusan",
                                                    e.target.value
                                                )
                                            }
                                        />
                                        <p className="text-xs text-red-500">
                                            {errors.catatan_keputusan}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Button
                                            type="button"
                                            disabled={processing}
                                            onClick={() =>
                                                putuskan("disetujui")
                                            }
                                        >
                                            {processing ? (
                                                <LoaderCircle className="animate-spin" />
                                            ) : (
                                                <Check />
                                            )}{" "}
                                            Setujui
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="danger"
                                            disabled={processing}
                                            onClick={() => putuskan("ditolak")}
                                        >
                                            <X /> Tolak
                                        </Button>
                                    </div>
                                </div>
                            )}

                            <div className="pt-4">
                                <Button variant="outline" asChild>
                                    <Link href="/dashboard/pengajuan">
                                        <ArrowLeft /> Kembali
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

Show.layout = (page: React.ReactNode) => <AppLayout children={page} />;
