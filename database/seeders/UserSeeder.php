<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::role('super-admin')->first()
            ?? User::query()->where('email', 'superadmin@example.com')->first();

        if ($superAdmin === null) {
            $superAdmin = User::query()->create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => 'password',
            ]);
        }

        if (! $superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }

        $superAdmin->syncPermissions(
    Permission::all()
);

        $users = [
            ['name' => 'Admin RELASI', 'email' => 'admin@example.com', 'role' => 'admin'],
            ['name' => 'Petugas Kesiswaan', 'email' => 'kesiswaan@example.com', 'role' => 'kesiswaan'],
            ['name' => 'Guru BK', 'email' => 'bk@example.com', 'role' => 'bk'],
            ['name' => 'Petugas Gerbang', 'email' => 'petugas.gerbang@example.com', 'role' => 'petugas_gerbang'],
        ];

        foreach ($users as $data) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => 'password']
            );

            if (! $user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }


    }

}
