import React from "react";
import AppLayout from "@/layouts/app-layout";
import { Head, useForm, Link } from "@inertiajs/react";
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

export default function Create() {
    const { data, setData, post, processing, errors, reset } = useForm({
        nomor: "",
        isi_ringkas: "",
        aktif: true,
    });

    const storeData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("dashboard.tatib.pasal.store"), {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <>
            <Head title="Tambah Pasal Tatib" />
            <div className="w-full">
                <Header
                    title="Tambah Data Pasal Tatib"
                    subtitle="Halaman ini digunakan untuk menambahkan data pasal tata tertib"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Tambah Pasal Tata Tertib</CardTitle>
                            <CardDescription>
                                Form ini digunakan untuk menambahkan data pasal
                                tata tertib
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="w-full">
                                <form onSubmit={storeData}>
                                    <div className="mb-4 flex flex-col gap-2">
                                        <Label>Nomor Pasal</Label>
                                        <Input
                                            type="text"
                                            value={data.nomor}
                                            onChange={(e) =>
                                                setData("nomor", e.target.value)
                                            }
                                            placeholder="Masukan nomor pasal, contoh: Pasal 7 ayat 2"
                                        />
                                        <p className="text-xs text-red-500">
                                            {errors.nomor}
                                        </p>
                                    </div>

                                    <div className="mb-4 flex flex-col gap-2">
                                        <Label>Isi Ringkas</Label>
                                        <Input
                                            type="text"
                                            value={data.isi_ringkas}
                                            onChange={(e) =>
                                                setData(
                                                    "isi_ringkas",
                                                    e.target.value
                                                )
                                            }
                                            placeholder="Masukan isi ringkas pasal"
                                        />
                                        <p className="text-xs text-red-500">
                                            {errors.isi_ringkas}
                                        </p>
                                    </div>

                                    <div className="flex items-center gap-2">
                                        <Checkbox
                                            id="aktif"
                                            checked={data.aktif}
                                            onCheckedChange={(checked) =>
                                                setData(
                                                    "aktif",
                                                    checked === true
                                                )
                                            }
                                        />
                                        <Label htmlFor="aktif">
                                            Pasal Aktif
                                        </Label>
                                        <p className="text-xs text-red-500">
                                            {errors.aktif}
                                        </p>
                                    </div>

                                    <div className="mt-4 flex items-center gap-2">
                                        <Button variant="danger" asChild>
                                            <Link href="/dashboard/tatib/pasal">
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
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

Create.layout = (page: React.ReactNode) => <AppLayout children={page} />;
