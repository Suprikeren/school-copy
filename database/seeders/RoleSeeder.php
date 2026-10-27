<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        $roles = [
            'super-admin',
            'admin',
            'kesiswaan',
            'bk',
            'petugas_gerbang',
        ];

        foreach ($roles as $name) {
            $role = Role::query()->where('name', $name)->first();

            if ($role === null) {
                $role = Role::create([
                    'name' => $name,
                    'guard_name' => $guard,
                ]);
            }

            $role->syncPermissions($this->permissionsFor($role->name, $guard));
        }
    }

    private function permissionsFor(string $role, string $guard): array
    {
        $all = Permission::query()
            ->where('guard_name', $guard)
            ->pluck('name')
            ->all();

        $inputDanLihat = ['pelanggaran index', 'pelanggaran store', 'pelanggaran show'];
        $pengajuan = ['pengajuan index', 'pengajuan store', 'pengajuan show'];

        $map = [
            'super-admin' => $all,
            'admin' => array_merge($inputDanLihat, $pengajuan, [
                'tindak index', 'tindak store', 'tindak edit', 'tindak delete',
            ]),
            'kesiswaan' => array_merge($inputDanLihat, $pengajuan, [
                'tindak index', 'tindak store', 'tindak edit', 'tindak delete',
            ]),
            'bk' => array_merge($inputDanLihat, $pengajuan, [
                'tindak index', 'tindak store', 'tindak edit', 'tindak delete',
            ]),
            'petugas_gerbang' => array_merge($inputDanLihat, $pengajuan),
        ];

        return $map[$role] ?? [];
    }
}
