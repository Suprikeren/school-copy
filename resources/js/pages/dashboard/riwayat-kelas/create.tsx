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

export default function Create() {
    const { siswaOpsi, tahunAjaranOpsi, siswaTerpilih } =
        usePage<PageProps<{ siswaOpsi: Pick<Siswa, "id" | "nis" | "nama">[]; tahunAjaranOpsi: Pick<TahunAjaran, "id" | "nama">[]; siswaTerpilih?: string | number | null }>>().props;

    const { data, setData, post, processing, errors } = useForm({
        siswa_id: siswaTerpilih ?? "",
        tahun_ajaran_id: "",
        kelas: "",
    });

    const simpan = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route("dashboard.riwayat-kelas.store"));
    };

    return (
        <>
            <Head title="Tambah Riwayat Kelas" />
            <div className="w-full">
                <Header
                    title="Tambah Riwayat Kelas"
                    subtitle="Halaman ini digunakan untuk menambahkan riwayat kelas siswa"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Riwayat Kelas</CardTitle>
                            <CardDescription>
                                Satu siswa hanya memiliki satu riwayat per tahun
                                ajaran
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={simpan}>
                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Siswa</Label>
                                    <select
                                        className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-sm"
                                        value={data.siswa_id}
                                        onChange={(e) =>
                                            setData("siswa_id", e.target.value)
                                        }
                                    >
                                        <option value="">Pilih siswa</option>
                                        {siswaOpsi.map((siswa) => (
                                            <option
                                                key={siswa.id}
                                                value={siswa.id}
                                            >
                                                {siswa.nama} ({siswa.nis})
                                            </option>
                                        ))}
                                    </select>
                                    <p className="text-xs text-red-500">
                                        {errors.siswa_id}
                                    </p>
                                </div>

                                <div className="mb-4 flex flex-col gap-2">
                                    <Label>Tahun Ajaran</Label>
                                    <select
                                        className="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-sm"
                                        value={data.tahun_ajaran_id}
                                        onChange={(e) =>
                                            setData(
                                                "tahun_ajaran_id",
                                                e.target.value
                                            )
                                        }
                                    >
                                        <option value="">
                                            Pilih tahun ajaran
                                        </option>
                                        {tahunAjaranOpsi.map((tahun) => (
                                            <option
                                                key={tahun.id}
                                                value={tahun.id}
                                            >
                                                {tahun.nama}
                                            </option>
                                        ))}
                                    </select>
                                    <p className="text-xs text-red-500">
                                        {errors.tahun_ajaran_id}
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
                                    <p className="text-xs text-red-500">
                                        {errors.kelas}
                                    </p>
                                </div>

                                <div className="mt-6 flex items-center gap-2">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? (
                                            <LoaderCircle className="animate-spin" />
                                        ) : (
                                            <Save />
                                        )}
                                        Simpan
                                    </Button>
                                    <Button variant="outline" asChild>
                                        <Link href={route("dashboard.riwayat-kelas.index")}>
                                            <ArrowLeft /> Kembali
                                        </Link>
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
