import React from "react";
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
import { ArrowLeft, LoaderCircle, Save } from "lucide-react";
import { PageProps } from "@/types";
import { TindakLanjut } from "@/types/tindak-lanjut";
import { Siswa } from "@/types/siswa";
import { SpSiswa } from "@/types/sp-siswa";

export default function Edit() {
    const { tindak, siswa, sp } = usePage<PageProps<{ tindak: TindakLanjut; siswa: Pick<Siswa, "id" | "nis" | "nama">[]; sp: (Pick<SpSiswa, "id" | "level" | "poin_saat_tercapai"> & { siswa?: Pick<Siswa, "id" | "nama"> })[] }>>().props;
    const { data, setData, post, processing, errors } = useForm({
        siswa_id: String(tindak.siswa_id),
        sp_siswa_id: tindak.sp_siswa_id ? String(tindak.sp_siswa_id) : "",
        pelanggaran_id: tindak.pelanggaran_id
            ? String(tindak.pelanggaran_id)
            : "",
        tindakan: tindak.tindakan,
        tanggal: tindak.tanggal.slice(0, 10),
        catatan: tindak.catatan ?? "",
        status: tindak.status,
        _method: "put",
    });

    const updateData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("dashboard.tindak-lanjut.update", tindak.id));
    };

    return (
        <>
            <Head title="Edit Tindak Lanjut" />
            <div className="w-full">
                <Header
                    title="Edit Tindak Lanjut"
                    subtitle="Halaman ini digunakan untuk mengubah tindak lanjut"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Form Tindak Lanjut</CardTitle>
                            <CardDescription>
                                Tindakan pembinaan dan hasilnya
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={updateData}>
                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Siswa</Label>
                                    <select
                                        className="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                        value={data.siswa_id}
                                        onChange={(e) =>
                                            setData("siswa_id", e.target.value)
                                        }
                                    >
                                        <option value="">Pilih siswa</option>
                                        {siswa.map((item) => (
                                            <option
                                                key={item.id}
                                                value={item.id}
                                            >
                                                {item.nama} ({item.nis})
                                            </option>
                                        ))}
                                    </select>
                                    <p className="text-xs text-red-500">
                                        {errors.siswa_id}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>SP Terkait (opsional)</Label>
                                    <select
                                        className="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                        value={data.sp_siswa_id}
                                        onChange={(e) =>
                                            setData(
                                                "sp_siswa_id",
                                                e.target.value
                                            )
                                        }
                                    >
                                        <option value="">Tidak terkait SP</option>
                                        {sp.map((item) => (
                                            <option
                                                key={item.id}
                                                value={item.id}
                                            >
                                                SP {item.level} -{" "}
                                                {item.siswa?.nama ?? "-"}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Tindakan</Label>
                                    <Input
                                        value={data.tindakan}
                                        onChange={(e) =>
                                            setData(
                                                "tindakan",
                                                e.target.value
                                            )
                                        }
                                    />
                                    <p className="text-xs text-red-500">
                                        {errors.tindakan}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Tanggal</Label>
                                    <Input
                                        type="date"
                                        value={data.tanggal}
                                        onChange={(e) =>
                                            setData("tanggal", e.target.value)
                                        }
                                    />
                                    <p className="text-xs text-red-500">
                                        {errors.tanggal}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Catatan</Label>
                                    <textarea
                                        className="min-h-24 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm"
                                        value={data.catatan}
                                        onChange={(e) =>
                                            setData("catatan", e.target.value)
                                        }
                                    />
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Status</Label>
                                    <select
                                        className="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                        value={data.status}
                                        onChange={(e) =>
                                            setData("status", e.target.value)
                                        }
                                    >
                                        <option value="belum">Belum</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>

                                <div className="flex items-center gap-2">
                                    <Button variant="danger" asChild>
                                        <Link href="/dashboard/tindak-lanjut">
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
                                        Simpan Data
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

Edit.layout = (page: React.ReactNode) => <AppLayout children={page} />;
