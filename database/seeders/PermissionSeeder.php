<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Permissions
            'permissions index',
            'permissions store',
            'permissions edit',
            'permissions show',
            'permissions delete',

            // Roles
            'roles index',
            'roles store',
            'roles edit',
            'roles show',
            'roles delete',

            // users
            'users index',
            'users store',
            'users edit',
            'users show',
            'users delete',

            // Pasal Tatib
            'pasal index',
            'pasal store',
            'pasal edit',
            'pasal show',
            'pasal delete',

            // Jenis Pelanggaran
            'jenis index',
            'jenis store',
            'jenis edit',
            'jenis show',
            'jenis delete',

            // Siswa
            'siswa index',
            'siswa store',
            'siswa edit',
            'siswa show',
            'siswa delete',

            // Riwayat Kelas
            'riwayat index',
            'riwayat store',
            'riwayat edit',
            'riwayat delete',

            // Pelanggaran (kasus)
            'pelanggaran index',
            'pelanggaran store',
            'pelanggaran show',

            // Tindak Lanjut
            'tindak index',
            'tindak store',
            'tindak edit',
            'tindak delete',

            // Pengajuan Perubahan
            'pengajuan index',
            'pengajuan store',
            'pengajuan show',
            'pengajuan putuskan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
