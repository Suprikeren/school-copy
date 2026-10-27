<?php

namespace Database\Seeders;

use App\Models\JenisPelanggaran;
use App\Models\JenisPelanggaranPasal;
use App\Models\PasalTatib;
use Illuminate\Database\Seeder;
use RuntimeException;

class JenisPelanggaranPasalSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            'terlambat-masuk-sekolah' => [
                'pasal-1-ketertiban-kehadiran',
            ],
            'tidak-memakai-atribut-seragam-lengkap' => [
                'pasal-2-kerapian-atribut',
            ],
            'rambut-atau-penampilan-tidak-rapi' => [
                'pasal-2-kerapian-atribut',
            ],
            'membuang-sampah-sembarangan' => [
                'pasal-4-kebersihan-sarana',
            ],
            'tidak-mengikuti-pelajaran-tanpa-izin' => [
                'pasal-1-ketertiban-kehadiran',
            ],
            'berbicara-kasar-atau-tidak-sopan' => [
                'pasal-3-etika-sopan-santun',
            ],
            'membolos-sekolah' => [
                'pasal-1-ketertiban-kehadiran',
            ],
            'merokok-di-lingkungan-sekolah' => [
                'pasal-5-larangan-berat',
            ],
            'berkelahi' => [
                'pasal-3-etika-sopan-santun',
                'pasal-5-larangan-berat',
            ],
            'merusak-sarana-prasarana-sekolah' => [
                'pasal-4-kebersihan-sarana',
            ],
        ];

        $jenisMap = JenisPelanggaran::query()
            ->whereIn('slug', array_keys($links))
            ->get()
            ->keyBy('slug');

        $pasalSlugs = collect($links)->flatten()->unique()->values();
        $pasalMap = PasalTatib::query()
            ->whereIn('slug', $pasalSlugs)
            ->get()
            ->keyBy('slug');

        foreach ($links as $jenisSlug => $pasalSlugsForType) {
            $jenis = $jenisMap->get($jenisSlug);

            if ($jenis === null) {
                throw new RuntimeException("Jenis pelanggaran {$jenisSlug} tidak ditemukan.");
            }

            foreach ($pasalSlugsForType as $pasalSlug) {
                $pasal = $pasalMap->get($pasalSlug);

                if ($pasal === null) {
                    throw new RuntimeException("Pasal {$pasalSlug} tidak ditemukan.");
                }

                JenisPelanggaranPasal::query()->firstOrCreate(
                    [
                        'jenis_pelanggaran_id' => $jenis->id,
                        'pasal_id' => $pasal->id,
                    ],
                    [
                        'created_by' => 'seeder',
                        'updated_by' => 'seeder',
                    ]
                );
            }
        }
    }
}
