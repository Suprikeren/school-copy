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
import { TahunAjaran } from "@/types/tahun-ajaran";

export default function Create() {
    const { tahunAjaran } = usePage<PageProps<{ tahunAjaran: Pick<TahunAjaran, "id" | "nama"> | null }>>().props;
    const { data, setData, post, processing, errors, reset } = useForm({
        nis: "",
        nama: "",
        status: "aktif",
        kelas: "",
    });

    const storeData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("dashboard.siswa.store"), {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <>
            <Head title="Tambah Siswa" />
            <div className="w-full">
                <Header
                    title="Tambah Data Siswa"
                    subtitle="Halaman ini digunakan untuk menambahkan data siswa"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Siswa</CardTitle>
                            <CardDescription>
                                Data siswa dan kelas pada tahun ajaran aktif
                                {tahunAjaran ? ` (${tahunAjaran.nama})` : ""}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={storeData}>
                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>NIS</Label>
                                    <Input
                                        type="text"
                                        value={data.nis}
                                        onChange={(e) =>
                                            setData("nis", e.target.value)
                                        }
                                        placeholder="Masukan NIS siswa"
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
                                        placeholder="Masukan nama siswa"
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
                                        placeholder="Contoh: X IPA 1"
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

Create.layout = (page: React.ReactNode) => <AppLayout children={page} />;
