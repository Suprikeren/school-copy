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
import { Siswa } from "@/types/siswa";
import { TahunAjaran } from "@/types/tahun-ajaran";

export default function Edit() {
    const { siswa, tahunAjaran, kelas } = usePage<PageProps<{ siswa: Siswa; tahunAjaran: Pick<TahunAjaran, "id" | "nama"> | null; kelas: string | null }>>().props;
    const { data, setData, post, processing, errors } = useForm({
        nis: siswa.nis,
        nama: siswa.nama,
        status: siswa.status,
        kelas: kelas ?? "",
        _method: "put",
    });

    const updateData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("dashboard.siswa.update", siswa.id));
    };

    return (
        <>
            <Head title={`Edit ${siswa.nama}`} />
            <div className="w-full">
                <Header
                    title="Edit Data Siswa"
                    subtitle="Halaman ini digunakan untuk mengubah data siswa"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Siswa</CardTitle>
                            <CardDescription>
                                Kelas mengikuti tahun ajaran aktif
                                {tahunAjaran ? ` (${tahunAjaran.nama})` : ""}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={updateData}>
                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>NIS</Label>
                                    <Input
                                        type="text"
                                        value={data.nis}
                                        onChange={(e) =>
                                            setData("nis", e.target.value)
                                        }
                                    />
                                    <p className="text-xs text-red-500">
                                        {errors.nis}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Nama</Label>
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
                                    <Label>Kelas</Label>
                                    <Input
                                        type="text"
                                        value={data.kelas}
                                        onChange={(e) =>
                                            setData("kelas", e.target.value)
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
                                        <option value="aktif">Aktif</option>
                                        <option value="alumni">Alumni</option>
                                        <option value="pindah">Pindah</option>
                                    </select>
                                    <p className="text-xs text-red-500">
                                        {errors.status}
                                    </p>
                                </div>

                                <div className="flex items-center gap-2">
                                    <Button variant="danger" asChild>
                                        <Link href="/dashboard/siswa">
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
