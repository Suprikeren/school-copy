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
import { JenisPelanggaran } from "@/types/jenis-pelanggaran";
import { PasalTatib } from "@/types/pasal-tatib";

export default function Show() {
    const { jenis } = usePage<PageProps<{ jenis: JenisPelanggaran & { pasal_tatib: Pick<PasalTatib, "id" | "nomor" | "isi_ringkas">[] } }>>().props;

    return (
        <>
            <Head title={`Detail ${jenis.nama}`} />
            <div className="w-full">
                <Header
                    title="Detail Jenis Pelanggaran"
                    subtitle={`Detail data ${jenis.nama}`}
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{jenis.nama}</CardTitle>
                            <CardDescription>
                                Informasi detail jenis pelanggaran
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4 text-sm">
                            <div className="flex gap-2">
                                <span className="w-32 text-muted-foreground">
                                    Kategori
                                </span>
                                <span className="capitalize">
                                    {jenis.kategori}
                                </span>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-32 text-muted-foreground">
                                    Poin
                                </span>
                                <span>{jenis.poin}</span>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-32 text-muted-foreground">
                                    Status
                                </span>
                                <Badge
                                    variant={jenis.aktif ? "default" : "outline"}
                                >
                                    {jenis.aktif ? "Aktif" : "Nonaktif"}
                                </Badge>
                            </div>
                            <div className="flex gap-2">
                                <span className="w-32 text-muted-foreground">
                                    Pasal
                                </span>
                                <div className="flex flex-wrap gap-1">
                                    {jenis.pasal_tatib.length === 0
                                        ? "-"
                                        : jenis.pasal_tatib.map((pasal) => (
                                              <Badge
                                                  key={pasal.id}
                                                  variant="outline"
                                              >
                                                  {pasal.nomor}
                                              </Badge>
                                          ))}
                                </div>
                            </div>
                            <div className="pt-2">
                                <Button variant="danger" asChild>
                                    <Link href="/dashboard/tatib/jenis-pelanggaran">
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
