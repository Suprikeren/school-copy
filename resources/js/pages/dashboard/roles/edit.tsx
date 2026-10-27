import React from 'react'
import AppLayout from '@/layouts/app-layout'
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import { Header } from '@/components/header'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell, TableCard } from '@/components/ui/table'
import { PageProps } from '@/types'
import { Button } from '@/components/ui/button'
import { ArrowLeft, LoaderCircle, Save } from 'lucide-react'
import { Role } from '@/types/role'
import { Permission } from '@/types/permission'
import { Checkbox } from '@/components/ui/checkbox'

export default function Edit() {
    const { role, permissions } = usePage<PageProps<{ role: Role; permissions: Permission[] }>>().props
    const { data, setData, post, processing, errors } = useForm({
        name: role.name,
        selectedPermissions: role.permissions.map((permission: Permission) => permission.name),
        _method: 'put'
    })

    const selectedPermission = (checked: boolean, permissionName: string) => {
        let permissionsIds = [...data.selectedPermissions]

        if (checked) {
            if (!permissionsIds.includes(permissionName)) {
                permissionsIds.push(permissionName)
            }
        } else {
            permissionsIds = permissionsIds.filter((name) => name !== permissionName)
        }

        setData('selectedPermissions', permissionsIds)
    }

    const selectAllPermission = (checked: boolean) => {
        const permissionsIds = checked ? permissions.map(permission => permission.name) : []
        setData('selectedPermissions', permissionsIds)
    }

    const storeData = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()

        post(route('dashboard.roles.update', role.id), {
            onSuccess: () => {
                // setData('open', false);
                // reset();
            },
        })
    }

    return (
        <>
            <Head title="Ubah Akses Group" />
            <div className="w-full">
                <Header title="Ubah Data Akses Group" subtitle="Halaman ini digunakan untuk mengubah data akses group pengguna" />
                <div className="p-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Ubah Akses Group</CardTitle>
                            <CardDescription>Form ini digunakan untuk mengubah data akses group</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="w-full">
                                <form onSubmit={storeData}>
                                    <div className="mb-4 flex flex-col gap-2">
                                        <Label>Nama Akses Group</Label>
                                        <Input
                                            type="text"
                                            autoComplete="off"
                                            name="name"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            placeholder="Masukan nama akses group"
                                        />
                                        <p className="text-red-500 text-xs">{errors.name}</p>
                                    </div>
                                    <div className="mb-4 flex flex-col gap-2">
                                        <Label>Hak Akses</Label>
                                        <TableCard>
                                            <Table>
                                                <TableHeader>
                                                    <TableRow>
                                                        <TableHead className="w-[50px] text-center">
                                                            <Checkbox
                                                                onCheckedChange={(checked) => selectAllPermission(checked === true)}
                                                                checked={data.selectedPermissions.length === permissions.length}
                                                            />
                                                        </TableHead>
                                                        <TableHead>Nama Hak Akses</TableHead>
                                                    </TableRow>
                                                </TableHeader>
                                                <TableBody>
                                                    {permissions.map((permission, i) => (
                                                        <TableRow key={i}>
                                                            <TableCell className="w-[50px] text-center">
                                                                <Checkbox
                                                                    checked={data.selectedPermissions.includes(permission.name)}
                                                                    onCheckedChange={(checked) => selectedPermission(checked === true, permission.name)}
                                                                    id={`permission-${i}`}
                                                                />
                                                            </TableCell>
                                                            <TableCell>{permission.name}</TableCell>
                                                        </TableRow>
                                                    ))}
                                                </TableBody>
                                            </Table>
                                        </TableCard>
                                        <p className="text-red-500 text-xs">{errors.selectedPermissions}</p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Button variant="danger" asChild>
                                            <Link href="/dashboard/roles">
                                                <ArrowLeft /> Kembali
                                            </Link>
                                        </Button>
                                        <Button variant="default" type="submit" disabled={processing}>
                                            {processing ? <LoaderCircle className="animate-spin" /> : <Save />} Simpan Data
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    )
}

Edit.layout = (page: React.ReactNode) => <AppLayout children={page} />
