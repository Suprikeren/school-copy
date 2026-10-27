// wajib yg  ini yah
import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { useForm, usePage } from '@inertiajs/react';
// import { PageProps } from '@/types';
import { PageProps } from '@/types';
import { Header } from '@/components/header'; //buat sendiri
import { LoaderCircle, PlusCircle, Save, X } from 'lucide-react';

// modal delete sweetalert
import { router } from '@inertiajs/react';
import { confirmDelete } from '@/utils/sweetAlert';
// selesai modal delete sweetalert

import {
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/components/ui/dialog';
import {
    Table,
    TableBody,
    TableCard,
    TableCell,
    TableEmpty,
    TableFilter,
    // TableFilter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'; //buat sendiri
import { Label } from '@/components/ui/label'; //buat sendiri
import { Input } from '@/components/ui/input'; //buat sendiri
import { Button } from '@/components/ui/button'; //buat sendiri
import { ActionButton } from '@/components/action-button'; //buat sendiri

//
import { Permission, PermissionLink } from '@/types/permission';
import { use } from 'react';
import Pagination from '@/components/ui/pagination';
import { useDebounce } from 'use-debounce';

export default function Index() {
    // Ambil permissions dan filters (jika ada) dari props
    const { permissions, filters } = usePage<PageProps<{ permissions: { data: Permission[]; links: PermissionLink[]; current_page: number; per_page: number }; filters: { search?: string } }>>().props;

    // 1. Setup State Search
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');
    const [debouncedSearch] = useDebounce(searchTerm, 500);

    // 2. Logic Auto Search saat user berhenti mengetik
    useEffect(() => {
        router.get(
            route('dashboard.permissions.index'), // Sesuaikan nama route-nya
            { search: debouncedSearch },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true
            }
        );
    }, [debouncedSearch]);
    // const [deleteModal, setDeleteModal] = React.useState(false);
    const { data, setData, post, processing, errors, reset, transform } =
        useForm({
            id: 0,
            name: '',
            open: false as boolean,
            isUpdate: false as boolean,
        });

    transform((data) => ({
        ...data,
        _method: data.isUpdate ? 'put' : 'post',
    }));

    // modal update
    const handleModalUpdate = (permission: Permission) => {
        setData((prevData) => ({
            ...prevData,
            id: permission.id,
            name: permission.name,
            open: true,
            isUpdate: true,
        }));
        // Handle the updated permission data here
        console.log('Updated Permission:', permission);
    };
    // modal delete

    const handleModalDelete = (permission: Permission) => {
        // Panggil helper confirmDelete kita
        confirmDelete(
            `Yakin ingin menghapus hak akses "${permission.name}"?`,
            () => {
                // Ini dijalankan jika user klik "Ya, Hapus"
                router.delete(
                    route('dashboard.permissions.destroy', permission.id),
                    {
                        preserveScroll: true, // Biar scroll gak balik ke atas
                        onSuccess: () => {
                            setData('open', false);
                            reset();
                        },
                    },
                );
            },
        );
    };
    // store data

    const storeData = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        if (data.isUpdate)
            post(route('dashboard.permissions.update', data.id), {
                onSuccess: () => {
                    setData('open', false);
                    reset();
                },
            });
        else
            post(route('dashboard.permissions.store'), {
                onSuccess: () => {
                    setData('open', false);
                    reset();
                },
            });
    };

    return (
        <>
            <AppLayout>
                <Head title="Hak Akses" />
                <div className="w-full">
                    <Header
                        title="Hak Akses"
                        subtitle="Halaman ini digunakan untuk mengelola data hak akses pengguna"
                    >
                        <Dialog
                            open={data.open}
                            onOpenChange={(open) =>
                                setData({
                                    ...data,
                                    open: open,
                                    name: '',
                                    id: 0,
                                    isUpdate: false,
                                })
                            }
                        >
                            {/* {hasAnyPermission(['permissions-create']) && ( */}
                            <DialogTrigger className="flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 focus:outline-none dark:text-gray-200 dark:hover:bg-gray-900">
                                <PlusCircle className="size-4" />{' '}
                                <span className="hidden sm:inline-flex">
                                    Tambah Hak Akses
                                </span>
                            </DialogTrigger>
                            {/* )} */}
                            <DialogContent className="p-0">
                                <DialogHeader className="border-b p-4">
                                    <DialogTitle>
                                        {data.isUpdate ? 'Ubah' : 'Tambah'} Hak
                                        Akses
                                    </DialogTitle>
                                    <DialogDescription className="text-sm">
                                        Form ini digunakan untuk{' '}
                                        {data.isUpdate
                                            ? 'mengubah data hak akses'
                                            : 'menambahkan data hak akses'}
                                    </DialogDescription>
                                </DialogHeader>
                                <form onSubmit={storeData}>
                                    <div className="p-4">
                                        <div className="mb-4 flex flex-col gap-2">
                                            <Label>Nama Hak Akses</Label>
                                            <Input
                                                type="text"
                                                name="name"
                                                value={data.name}
                                                onChange={(e) =>
                                                    setData(
                                                        'name',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Masukkan nama hak akses"
                                            />
                                            <p className="text-xs text-red-500">
                                                {errors.name}
                                            </p>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <Button
                                                variant="danger"
                                                type="button"
                                                onClick={() => reset()}
                                            >
                                                <X /> Cancel
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
                                    </div>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </Header>
                </div>
                <div className="p-6">
                    <div className="mb-5">
                        {/* <TableFilter
                            withFilterPage
                            currentPage={currentPage}
                            perPage={perPage}
                            url={route('apps.permissions.index')}
                            placeholder="Cari data hak akses berdasarkan nama hak akses"
                        /> */}
                        <TableFilter
                            placeholder="Cari data hak akses berdasarkan nama hak akses"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                        />
                    </div>
                    <TableCard>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-[10px] text-center">
                                        No
                                    </TableHead>
                                    <TableHead>Nama Hak Akses</TableHead>
                                    <TableHead className="w-[10px] text-center">
                                        Aksi
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {permissions.data.length === 0 ? (
                                    <TableEmpty
                                        colSpan={3}
                                        message="data hak akses"
                                    />
                                ) : (
                                    permissions.data.map(
                                        (permission, index) => (
                                            <TableRow key={permission.id}>
                                                <TableCell className="text-center">
                                                    {++index +
                                                        (permissions.current_page -
                                                            1) *
                                                            permissions.per_page}
                                                </TableCell>
                                                <TableCell>
                                                    {permission.name}
                                                </TableCell>
                                                <TableCell className="flex items-center justify-center gap-2">
                                                    {/* {(hasAnyPermission(['permissions-update']) || hasAnyPermission(['permissions-delete'])) && ( */}
                                                    <ActionButton
                                                        // permissionPrefix="permissions"
                                                        isModal={true}
                                                        actionEdit={() =>
                                                            handleModalUpdate(
                                                                permission,
                                                            )
                                                        }
                                                        actionDelete={() =>
                                                            handleModalDelete(
                                                                permission,
                                                            )
                                                        }
                                                    />
                                                    {/* )} */}
                                                </TableCell>
                                            </TableRow>
                                        ),
                                    )
                                )}
                            </TableBody>
                        </Table>
                    </TableCard>
                    {/* <ModalDelete open={deleteModal} onOpenChange={setDeleteModal} url={route('apps.permissions.destroy', data.id)}/> */}
                    {/* <Pagination links={permissions.links} /> */}
                    <div className="mt-4 flex w-full justify-end">
                        <Pagination links={permissions.links} />
                    </div>
                </div>
            </AppLayout>
        </>
    );
}
