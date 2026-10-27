<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('pengaturan')) {
            $this->command?->warn(
                'PengaturanSeeder dilewati: tabel pengaturan belum tersedia. '
                .'Buat migration tabel pengaturan terlebih dahulu.'
            );

            return;
        }

        if (! Schema::hasColumn('pengaturan', 'kunci') || ! Schema::hasColumn('pengaturan', 'nilai')) {
            $this->command?->warn(
                'PengaturanSeeder dilewati: kolom kunci atau nilai tidak ditemukan pada tabel pengaturan.'
            );

            return;
        }

        $settings = [
            'sp_1_poin' => '30',
            'sp_2_poin' => '60',
            'sp_3_poin' => '90',
        ];

        $hasId = Schema::hasColumn('pengaturan', 'id');
        $hasCreatedAt = Schema::hasColumn('pengaturan', 'created_at');
        $hasUpdatedAt = Schema::hasColumn('pengaturan', 'updated_at');
        $hasCreatedBy = Schema::hasColumn('pengaturan', 'created_by');
        $hasUpdatedBy = Schema::hasColumn('pengaturan', 'updated_by');

        foreach ($settings as $key => $value) {
            if (DB::table('pengaturan')->where('kunci', $key)->exists()) {
                continue;
            }

            $row = [
                'kunci' => $key,
                'nilai' => $value,
            ];

            if ($hasId) {
                $row['id'] = ((int) DB::table('pengaturan')->max('id')) + 1;
            }

            if ($hasCreatedAt) {
                $row['created_at'] = now();
            }

            if ($hasUpdatedAt) {
                $row['updated_at'] = now();
            }

            if ($hasCreatedBy) {
                $row['created_by'] = 'seeder';
            }

            if ($hasUpdatedBy) {
                $row['updated_by'] = 'seeder';
            }

            DB::table('pengaturan')->insert($row);
        }
    }
}
