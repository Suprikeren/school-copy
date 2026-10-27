<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SiswaSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $students = [
            ['nis' => '2026001', 'nama' => 'Ahmad Fauzan'],
            ['nis' => '2026002', 'nama' => 'Budi Santoso'],
            ['nis' => '2026003', 'nama' => 'Citra Lestari'],
            ['nis' => '2026004', 'nama' => 'Dewi Anggraini'],
            ['nis' => '2026005', 'nama' => 'Eko Prasetyo'],
            ['nis' => '2026006', 'nama' => 'Fajar Ramadhan'],
            ['nis' => '2026007', 'nama' => 'Gilang Saputra'],
            ['nis' => '2026008', 'nama' => 'Hendra Wijaya'],
            ['nis' => '2026009', 'nama' => 'Intan Permata'],
            ['nis' => '2026010', 'nama' => 'Joko Susilo'],
            ['nis' => '2026011', 'nama' => 'Kartika Sari'],
            ['nis' => '2026012', 'nama' => 'Lukman Hakim'],
        ];

        foreach ($students as $student) {
            $this->firstOrCreateWithId(
                Siswa::class,
                ['nis' => $student['nis']],
                [
                    'nama' => $student['nama'],
                    'status' => 'aktif',
                    'slug' => Str::slug($student['nama'].'-'.$student['nis']),
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
