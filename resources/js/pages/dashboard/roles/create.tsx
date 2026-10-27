import React from 'react';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Header } from '@/components/header';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableHeader,
    TableRow,
    TableHead,
    TableBody,
    TableCell,
    TableCard,
} from '@/components/ui/table';
import { PageProps } from '@/types';
import { Button } from '@/components/ui/button';
import { ArrowLeft, LoaderCircle, Save } from 'lucide-react';
import { Permission } from '@/types/permission';
import { Checkbox } from '@/components/ui/checkbox';

export default function Create() {
    const { permissions } = usePage<PageProps<{ permissions: Permission[] }>>().props;

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        selectedPermissions: [] as string[],
    });

    const togglePermission = (permissionName: string, checked: boolean) => {
        if (checked) {
            setData('selectedPermissions', [
                ...data.selectedPermissions,
                permissionName,
            ]);
        } else {
            setData(
                'selectedPermissions',
                data.selectedPermissions.filter(
                    (name) => name !== permissionName,
                ),
            );
        }
    };

    const toggleSelectAll = (checked: boolean) => {
        const allPermissions = permissions.map((p) => p.name);
        setData('selectedPermissions', checked ? allPermissions : []);
    };

    const storeData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post(route('dashboard.roles.store'), {
            onSuccess: () => {
                // setData('open', false);
                reset();
            },
        });
    };

    return (
        <>
            <Head title="Tambah Akses Group" />
            <div className="w-full">
                <Header
                    title="Tambah Data Akses Group"
                    subtitle="Halaman ini digunakan untuk menambahkan data akses group pengguna"
                />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Tambah Akses Group</CardTitle>
                            <CardDescription>
                                Form ini digunakan untuk menambahkan data akses
                                group
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="w-full">
                                <form onSubmit={storeData}>
                                    {/* Nama Group */}
                                    <div className="mb-4 flex flex-col gap-2">
                                        <Label htmlFor="name">
                                            Nama Akses Group
                                        </Label>
                                        <Input
                                            type="text"
                                            id="name"
                                            autoComplete="off"
                                            name="name"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData('name', e.target.value)
                                            }
                                            placeholder="Masukan nama akses group"
                                        />
                                        {errors.name && (
                                            <p className="text-xs text-red-500">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>

                                    {/* Hak Akses */}
                                    <div className="mb-4 flex flex-col gap-2">
                                        <Label>Hak Akses</Label>
                                        <TableCard>
                                            <Table>
                                                <TableHeader>
                                                    <TableRow>
                                                        <TableHead className="w-[50px] text-center">
                                                            <Checkbox
                                                                checked={
                                                                    data
                                                                        .selectedPermissions
                                                                        .length ===
                                                                    permissions.length
                                                                }
                                                                onCheckedChange={(
                                                                    checked,
                                                                ) =>
                                                                    toggleSelectAll(
                                                                        checked ===
                                                                            true,
                                                                    )
                                                                }
                                                                id="select-all"
                                                            />
                                                        </TableHead>
                                                        <TableHead>
                                                            Nama Hak Akses
                                                        </TableHead>
                                                    </TableRow>
                                                </TableHeader>
                                                <TableBody>
                                                    {permissions.map(
                                                        (permission, i) => (
                                                            <TableRow key={i}>
                                                                <TableCell className="w-[50px] text-center">
                                                                    <Checkbox
                                                                        id={`permission-${i}`}
                                                                        checked={data.selectedPermissions.includes(
                                                                            permission.name,
                                                                        )}
                                                                        onCheckedChange={(
                                                                            checked,
                                                                        ) =>
                                                                            togglePermission(
                                                                                permission.name,
                                                                                checked ===
                                                                                    true,
                                                                            )
                                                                        }
                                                                    />
                                                                </TableCell>
                                                                <TableCell>
                                                                    {
                                                                        permission.name
                                                                    }
                                                                </TableCell>
                                                            </TableRow>
                                                        ),
                                                    )}
                                                </TableBody>
                                            </Table>
                                        </TableCard>
                                        {errors.selectedPermissions && (
                                            <p className="text-xs text-red-500">
                                                {errors.selectedPermissions}
                                            </p>
                                        )}
                                    </div>

                                    {/* Tombol Aksi */}
                                    <div className="flex items-center gap-2">
                                        <Button variant="danger" asChild>
                                            <Link href="/dashboard/roles">
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
                                            )}{' '}
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
