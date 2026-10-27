<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            TahunAjaranSeeder::class,
            UserSeeder::class,
            SiswaSeeder::class,
            RiwayatKelasSeeder::class,
            PasalTatibSeeder::class,
            JenisPelanggaranSeeder::class,
            JenisPelanggaranPasalSeeder::class,
            TatibDokumenSeeder::class,
            KasusSeeder::class,
            PelanggaranSeeder::class,
            SpSiswaSeeder::class,
            TindakLanjutSeeder::class,
            PengajuanPerubahanSeeder::class,
            NotifikasiSeeder::class,
            AuditLogSeeder::class,
            PengaturanSeeder::class,
        ]);
    }
}
