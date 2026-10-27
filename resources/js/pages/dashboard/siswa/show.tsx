import React from "react";
import AppLayout from "@/layouts/app-layout";
import { Head, Link, usePage } from "@inertiajs/react";
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
import { ArrowLeft } from "lucide-react";
import { PageProps } from "@/types";
import { Siswa } from "@/types/siswa";
import { RiwayatKelas } from "@/types/riwayat-kelas";
import { TahunAjaran } from "@/types/tahun-ajaran";
import { SpSiswa } from "@/types/sp-siswa";

export default function Show() {
    const { siswa } = usePage<PageProps<{ siswa: Siswa & { riwayat_kelas: (RiwayatKelas & { tahun_ajaran?: Pick<TahunAjaran, "id" | "nama"> })[]; sp_siswa: SpSiswa[] } }>>().props;

    return (
        <>
            <Head title={`Detail ${siswa.nama}`} />
            <div className="w-full">
                <Header
                    title="Detail Siswa"
                    subtitle={`Detail data ${siswa.nama}`}
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{siswa.nama}</CardTitle>
                            <CardDescription>
                                NIS {siswa.nis} - status {siswa.status}
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6 text-sm">
                            <div>
                                <div className="mb-2 flex items-center justify-between">
                                    <h3 className="font-semibold">
                                        Riwayat Kelas
                                    </h3>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        asChild
                                    >
                                        <Link
                                            href={route(
                                                "dashboard.riwayat-kelas.index",
                                                { siswa_id: siswa.id }
                                            )}
                                        >
                                            Kelola Riwayat Kelas
                                        </Link>
                                    </Button>
                                </div>
                                {siswa.riwayat_kelas.length === 0 ? (
                                    <p className="text-muted-foreground">
                                        Belum ada riwayat kelas.
                                    </p>
                                ) : (
                                    <ul className="space-y-1">
                                        {siswa.riwayat_kelas.map((riwayat) => (
                                            <li key={riwayat.id}>
                                                {riwayat.tahun_ajaran?.nama ?? "-"}{" "}
                                                : {riwayat.kelas}
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>

                            <div>
                                <h3 className="mb-2 font-semibold">
                                    Surat Peringatan
                                </h3>
                                {siswa.sp_siswa.length === 0 ? (
                                    <p className="text-muted-foreground">
                                        Belum ada SP.
                                    </p>
                                ) : (
                                    <div className="flex flex-wrap gap-2">
                                        {siswa.sp_siswa.map((sp) => (
                                            <Badge key={sp.id}>
                                                SP {sp.level} -{" "}
                                                {sp.poin_saat_tercapai} poin
                                            </Badge>
                                        ))}
                                    </div>
                                )}
                            </div>

                            <div className="pt-2">
                                <Button variant="danger" asChild>
                                    <Link href="/dashboard/siswa">
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
