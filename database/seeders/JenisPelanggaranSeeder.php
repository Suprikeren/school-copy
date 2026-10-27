<?php

namespace Database\Seeders;

use App\Models\JenisPelanggaran;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;

class JenisPelanggaranSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $types = [
            [
                'nama' => 'Terlambat masuk sekolah',
                'kategori' => 'ringan',
                'poin' => 5,
                'slug' => 'terlambat-masuk-sekolah',
            ],
            [
                'nama' => 'Tidak memakai atribut seragam lengkap',
                'kategori' => 'ringan',
                'poin' => 5,
                'slug' => 'tidak-memakai-atribut-seragam-lengkap',
            ],
            [
                'nama' => 'Rambut atau penampilan tidak rapi',
                'kategori' => 'ringan',
                'poin' => 5,
                'slug' => 'rambut-atau-penampilan-tidak-rapi',
            ],
            [
                'nama' => 'Membuang sampah sembarangan',
                'kategori' => 'ringan',
                'poin' => 5,
                'slug' => 'membuang-sampah-sembarangan',
            ],
            [
                'nama' => 'Tidak mengikuti pelajaran tanpa izin',
                'kategori' => 'sedang',
                'poin' => 10,
                'slug' => 'tidak-mengikuti-pelajaran-tanpa-izin',
            ],
            [
                'nama' => 'Berbicara kasar atau tidak sopan',
                'kategori' => 'sedang',
                'poin' => 10,
                'slug' => 'berbicara-kasar-atau-tidak-sopan',
            ],
            [
                'nama' => 'Membolos sekolah',
                'kategori' => 'sedang',
                'poin' => 15,
                'slug' => 'membolos-sekolah',
            ],
            [
                'nama' => 'Merokok di lingkungan sekolah',
                'kategori' => 'berat',
                'poin' => 25,
                'slug' => 'merokok-di-lingkungan-sekolah',
            ],
            [
                'nama' => 'Berkelahi',
                'kategori' => 'berat',
                'poin' => 30,
                'slug' => 'berkelahi',
            ],
            [
                'nama' => 'Merusak sarana prasarana sekolah',
                'kategori' => 'berat',
                'poin' => 25,
                'slug' => 'merusak-sarana-prasarana-sekolah',
            ],
        ];

        foreach ($types as $type) {
            $this->firstOrCreateWithId(
                JenisPelanggaran::class,
                ['slug' => $type['slug']],
                [
                    'nama' => $type['nama'],
                    'kategori' => $type['kategori'],
                    'poin' => $type['poin'],
                    'aktif' => true,
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
