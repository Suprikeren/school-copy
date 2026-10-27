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
import { Checkbox } from "@/components/ui/checkbox";
import { PageProps } from "@/types";
import { JenisPelanggaran } from "@/types/jenis-pelanggaran";
import { PasalTatib } from "@/types/pasal-tatib";

export default function Edit() {
    const { jenis, pasal } = usePage<PageProps<{ jenis: JenisPelanggaran & { pasal_tatib: Pick<PasalTatib, "id" | "nomor" | "isi_ringkas">[] }; pasal: Pick<PasalTatib, "id" | "nomor" | "isi_ringkas">[] }>>().props;
    const { data, setData, post, processing, errors } = useForm({
        nama: jenis.nama,
        kategori: jenis.kategori,
        poin: jenis.poin,
        aktif: jenis.aktif,
        pasal_ids: jenis.pasal_tatib.map((item) => item.id),
        _method: "put",
    });

    const togglePasal = (id: number, checked: boolean) => {
        if (checked) {
            setData("pasal_ids", [...data.pasal_ids, id]);
        } else {
            setData(
                "pasal_ids",
                data.pasal_ids.filter((pasalId) => pasalId !== id)
            );
        }
    };

    const updateData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("dashboard.tatib.jenis-pelanggaran.update", jenis.id));
    };

    return (
        <>
            <Head title={`Edit ${jenis.nama}`} />
            <div className="w-full">
                <Header
                    title="Edit Jenis Pelanggaran"
                    subtitle="Halaman ini digunakan untuk mengubah jenis pelanggaran"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Jenis Pelanggaran</CardTitle>
                            <CardDescription>
                                Nama, kategori, poin, dan pasal yang terkait
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={updateData}>
                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Nama Pelanggaran</Label>
                                    <Input
                                        type="text"
                                        value={data.nama}
                                        onChange={(e) =>
                                            setData("nama", e.target.value)
                                        }
                                    />
                                    <p className="text-xs text-red-500">
                                        {errors.nama}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Kategori</Label>
                                    <select
                                        className="h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                        value={data.kategori}
                                        onChange={(e) =>
                                            setData("kategori", e.target.value)
                                        }
                                    >
                                        <option value="umum">Umum</option>
                                        <option value="terlambat">
                                            Terlambat
                                        </option>
                                    </select>
                                    <p className="text-xs text-red-500">
                                        {errors.kategori}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Poin</Label>
                                    <Input
                                        type="number"
                                        min={0}
                                        value={data.poin}
                                        onChange={(e) =>
                                            setData(
                                                "poin",
                                                Number(e.target.value)
                                            )
                                        }
                                    />
                                    <p className="text-xs text-red-500">
                                        {errors.poin}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Pasal Terkait</Label>
                                    <div className="grid gap-2 rounded-md border p-4 md:grid-cols-2">
                                        {pasal.map((item) => (
                                            <label
                                                key={item.id}
                                                className="flex items-start gap-2 text-sm"
                                            >
                                                <Checkbox
                                                    checked={data.pasal_ids.includes(
                                                        item.id
                                                    )}
                                                    onCheckedChange={(checked) =>
                                                        togglePasal(
                                                            item.id,
                                                            checked === true
                                                        )
                                                    }
                                                />
                                                <span>
                                                    <strong>{item.nomor}</strong>{" "}
                                                    - {item.isi_ringkas}
                                                </span>
                                            </label>
                                        ))}
                                    </div>
                                    <p className="text-xs text-red-500">
                                        {errors.pasal_ids}
                                    </p>
                                </div>

                                <div className="mb-4 flex items-center gap-2">
                                    <Checkbox
                                        id="aktif"
                                        checked={data.aktif}
                                        onCheckedChange={(checked) =>
                                            setData("aktif", checked === true)
                                        }
                                    />
                                    <Label htmlFor="aktif">Aktif</Label>
                                </div>

                                <div className="flex items-center gap-2">
                                    <Button variant="danger" asChild>
                                        <Link href="/dashboard/tatib/jenis-pelanggaran">
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
