import React from "react";
import AppLayout from "@/layouts/app-layout";
import { Header } from "@/components/header";
import {
    TableFilter,
    TableCard,
    Table,
    TableHead,
    TableHeader,
    TableRow,
    TableBody,
    TableCell,
    TableEmpty,
} from "@/components/ui/table";
import { PageProps, User, UserLink } from '@/types';
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Head, useForm, usePage, Link, router } from "@inertiajs/react";
import { PlusCircle, UserCheck } from "lucide-react";
import { ActionButton } from "@/components/action-button";
import { confirmDelete } from '@/utils/sweetAlert';
import Pagination from '@/components/ui/pagination';

export default function Index() {
    const { users, perPage, currentPage } = usePage<PageProps<{ users: { data: User[]; links: UserLink[]; current_page: number; per_page: number }; perPage: number; currentPage: number }>>().props;

    const { setData, reset } = useForm({
        id: 0,
    });

    const handleModalDelete = (user: User) => {
        confirmDelete(`Yakin ingin menghapus pengguna "${user.name}"?`, () => {
            router.delete(route('apps.users.destroy', user.id), {
                preserveScroll: true,
                onSuccess: () => {
                    reset();
                },
            });
        });
    };

    return (
        <>
            <Head title="Pengguna" />

            <Header
                title="Pengguna"
                subtitle="Halaman ini digunakan untuk mengelola data pengguna"
            >
                <Button asChild variant='outline'>
                    <Link href={route('dashboard.users.create')}>
                        <PlusCircle className="size-4"/>
                        <span className="hidden sm:inline-flex">Tambah Data Pengguna</span>
                    </Link>
                </Button>
            </Header>

            <div className="p-6">
                <div className="mb-5">
                    <TableFilter
                        // withFilterPage={true}
                        // perPage={perPage}
                        // currentPage={currentPage}
                        // url={route('dashboard.users.index')}
                        placeholder="Cari data pengguna berdasarkan nama pengguna"
                    />
                </div>

                <TableCard>
                    <Table
                        title="users"
                        // subtitle="List of all users"
                        // icon={<UserCheck className="size-6 text-gray-700"
                        // />}
                    >
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[10px] text-center">No</TableHead>
                                <TableHead>Nama</TableHead>
                                {/* <TableHead>Username</TableHead> */}
                                <TableHead>Email</TableHead>
                                <TableHead>Akses Group</TableHead>
                                <TableHead className="w-[10px] text-center">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {users.data.length === 0 ? (
                                <TableEmpty colSpan={6} message="Data pengguna"/>
                            ) : (
                                users.data.map((user, index) => (
                                    <TableRow key={user.id}>
                                        <TableCell className="text-center">
                                            {++index + (users.current_page - 1) * users.per_page}
                                        </TableCell>
                                        <TableCell>{user.name}</TableCell>
                                        {/* <TableCell>{user.username}</TableCell> */}
                                        <TableCell>{user.email}</TableCell>
                                        <TableCell>
                                            <div className="flex flex-wrap gap-2">
                                                {user.roles.slice(0, 3).map((role, i) => (
                                                    <Badge variant="default" key={i}>
                                                        {role.name}
                                                    </Badge>
                                                ))}
                                                {user.roles.length > 3 && (
                                                    <Badge variant="outline">
                                                        + {user.roles.length - 3} lainnya
                                                    </Badge>
                                                )}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center justify-center">
                                                <ActionButton
                                                    withDetail
                                                    actionEditHref={route('dashboard.users.edit', user.id)}
                                                    actionDetailHref={route('dashboard.users.show', user.id)}
                                                    actionDelete={() => handleModalDelete(user)}
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </TableCard>

                <div className="mt-4 flex w-full justify-end">
                    <Pagination links={users.links} />
                </div>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <AppLayout children={page} />;
