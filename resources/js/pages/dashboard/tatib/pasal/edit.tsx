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
import { PasalTatib } from "@/types/pasal-tatib";

export default function Edit() {
    const { pasal } = usePage<PageProps<{ pasal: PasalTatib }>>().props;
    const { data, setData, post, processing, errors, reset } = useForm({
        nomor: pasal.nomor,
        isi_ringkas: pasal.isi_ringkas,
        aktif: pasal.aktif ?? true,
        _method: "put",
    });

    const updateData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route("dashboard.tatib.pasal.update", pasal.id), {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <>
            <Head title={`Edit Pasal Tatib - ${pasal.nomor}`} />
            <div className="w-full">
                <Header
                    title="Edit Data Pasal Tatib"
                    subtitle="Halaman ini digunakan untuk mengubah data pasal tata tertib"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Edit Pasal Tata Tertib</CardTitle>
                            <CardDescription>
                                Form ini digunakan untuk mengubah data pasal
                                tata tertib
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="w-full">
                                <form onSubmit={updateData}>
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

Edit.layout = (page: React.ReactNode) => <AppLayout children={page} />;
