<?php

namespace Database\Seeders;

use App\Models\PasalTatib;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;

class PasalTatibSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $articles = [
            [
                'nomor' => 'Pasal 1',
                'isi_ringkas' => 'Kewajiban hadir tepat waktu dan mengikuti kegiatan pembelajaran.',
                'slug' => 'pasal-1-ketertiban-kehadiran',
            ],
            [
                'nomor' => 'Pasal 2',
                'isi_ringkas' => 'Kewajiban memakai seragam dan atribut sekolah secara lengkap dan rapi.',
                'slug' => 'pasal-2-kerapian-atribut',
            ],
            [
                'nomor' => 'Pasal 3',
                'isi_ringkas' => 'Kewajiban menjaga etika, sopan santun, dan hubungan baik antarsiswa.',
                'slug' => 'pasal-3-etika-sopan-santun',
            ],
            [
                'nomor' => 'Pasal 4',
                'isi_ringkas' => 'Kewajiban menjaga kebersihan dan merawat sarana prasarana sekolah.',
                'slug' => 'pasal-4-kebersihan-sarana',
            ],
            [
                'nomor' => 'Pasal 5',
                'isi_ringkas' => 'Larangan perbuatan berat seperti merokok, berkelahi, dan sejenisnya.',
                'slug' => 'pasal-5-larangan-berat',
            ],
        ];

        foreach ($articles as $article) {
            $this->firstOrCreateWithId(
                PasalTatib::class,
                ['slug' => $article['slug']],
                [
                    'nomor' => $article['nomor'],
                    'isi_ringkas' => $article['isi_ringkas'],
                    'aktif' => true,
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
