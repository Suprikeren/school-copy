<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $hasActiveYear = TahunAjaran::query()->where('aktif', true)->exists();

        $years = [
            [
                'nama' => '2025/2026',
                'slug' => '2025-2026',
                'tgl_mulai' => '2025-07-14',
                'tgl_selesai' => '2026-06-30',
                'aktif' => false,
            ],
            [
                'nama' => '2026/2027',
                'slug' => '2026-2027',
                'tgl_mulai' => '2026-07-13',
                'tgl_selesai' => '2027-06-30',
                'aktif' => ! $hasActiveYear,
            ],
        ];

        foreach ($years as $year) {
            $this->firstOrCreateWithId(
                TahunAjaran::class,
                ['slug' => $year['slug']],
                [
                    'nama' => $year['nama'],
                    'tgl_mulai' => $year['tgl_mulai'],
                    'tgl_selesai' => $year['tgl_selesai'],
                    'aktif' => $year['aktif'],
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
