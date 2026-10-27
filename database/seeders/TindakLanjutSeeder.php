<?php

namespace Database\Seeders;

use App\Models\JenisPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\SpSiswa;
use App\Models\TahunAjaran;
use App\Models\TindakLanjut;
use App\Models\User;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;
use RuntimeException;

class TindakLanjutSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $activeYear = TahunAjaran::query()->where('slug', '2026-2027')->first()
            ?? TahunAjaran::query()->where('aktif', true)->firstOrFail();

        $kesiswaan = User::query()->where('email', 'kesiswaan@example.com')->first()
            ?? User::role('kesiswaan')->firstOrFail();

        $bk = User::query()->where('email', 'bk@example.com')->first()
            ?? User::role('bk')->firstOrFail();

        $students = Siswa::query()
            ->whereIn('nis', ['2026001', '2026006', '2026007', '2026008', '2026010'])
            ->get()
            ->keyBy('nis');

        $spRecords = SpSiswa::query()
            ->where('tahun_ajaran_id', $activeYear->id)
            ->whereIn('siswa_id', $students->pluck('id'))
            ->get()
            ->keyBy(fn (SpSiswa $sp) => $sp->siswa_id.'-'.$sp->level);

        $jenisIds = JenisPelanggaran::query()
            ->whereIn('slug', [
                'terlambat-masuk-sekolah',
                'tidak-mengikuti-pelajaran-tanpa-izin',
                'merokok-di-lingkungan-sekolah',
                'berkelahi',
            ])
            ->pluck('id', 'slug');

        $findViolation = function (string $nis, string $jenisSlug) use ($students, $jenisIds): Pelanggaran {
            $student = $students->get($nis);

            if ($student === null) {
                throw new RuntimeException("Siswa dengan NIS {$nis} tidak ditemukan.");
            }

            $jenisId = $jenisIds->get($jenisSlug);

            if ($jenisId === null) {
                throw new RuntimeException("Jenis pelanggaran {$jenisSlug} tidak ditemukan.");
            }

            return Pelanggaran::query()
                ->where('siswa_id', $student->id)
                ->where('jenis_pelanggaran_id', $jenisId)
                ->firstOrFail();
        };

        $rows = [
            [
                'nis' => '2026007',
                'sp_level' => 1,
                'jenis' => 'merokok-di-lingkungan-sekolah',
                'tindakan' => 'Panggilan orang tua',
                'tanggal' => '2026-09-17',
                'catatan' => 'Orang tua/wali hadir dan bersedia mendampingi pembinaan siswa.',
                'status' => 'selesai',
                'handler' => $bk,
            ],
            [
                'nis' => '2026007',
                'sp_level' => 2,
                'jenis' => 'berkelahi',
                'tindakan' => 'Pembinaan BK',
                'tanggal' => '2026-10-07',
                'catatan' => null,
                'status' => 'belum',
                'handler' => $bk,
            ],
            [
                'nis' => '2026008',
                'sp_level' => 1,
                'jenis' => 'berkelahi',
                'tindakan' => 'Teguran lisan',
                'tanggal' => '2026-10-06',
                'catatan' => 'Siswa mengakui perbuatan dan berkomitmen tidak mengulangi.',
                'status' => 'selesai',
                'handler' => $kesiswaan,
            ],
            [
                'nis' => '2026010',
                'sp_level' => 1,
                'jenis' => 'merokok-di-lingkungan-sekolah',
                'tindakan' => 'Surat pernyataan',
                'tanggal' => '2026-09-17',
                'catatan' => 'Surat pernyataan siswa dan orang tua telah diterima.',
                'status' => 'selesai',
                'handler' => $kesiswaan,
            ],
            [
                'nis' => '2026001',
                'sp_level' => null,
                'jenis' => 'terlambat-masuk-sekolah',
                'tindakan' => 'Teguran lisan',
                'tanggal' => '2026-08-11',
                'catatan' => 'Siswa diberi teguran dan diingatkan kembali tentang ketertiban kehadiran.',
                'status' => 'selesai',
                'handler' => $kesiswaan,
            ],
            [
                'nis' => '2026006',
                'sp_level' => null,
                'jenis' => 'tidak-mengikuti-pelajaran-tanpa-izin',
                'tindakan' => 'Pembinaan BK',
                'tanggal' => '2026-09-03',
                'catatan' => null,
                'status' => 'belum',
                'handler' => $bk,
            ],
        ];

        foreach ($rows as $row) {
            $student = $students->get($row['nis']);

            if ($student === null) {
                throw new RuntimeException("Siswa dengan NIS {$row['nis']} tidak ditemukan.");
            }

            $sp = $row['sp_level'] === null
                ? null
                : $spRecords->get($student->id.'-'.$row['sp_level']);

            if ($row['sp_level'] !== null && $sp === null) {
                throw new RuntimeException("SP level {$row['sp_level']} untuk NIS {$row['nis']} tidak ditemukan.");
            }

            $violation = $findViolation($row['nis'], $row['jenis']);

            $this->firstOrCreateWithId(
                TindakLanjut::class,
                [
                    'siswa_id' => $student->id,
                    'tindakan' => $row['tindakan'],
                    'tanggal' => $row['tanggal'],
                ],
                [
                    'sp_siswa_id' => $sp?->id,
                    'pelanggaran_id' => $violation->id,
                    'catatan' => $row['catatan'],
                    'status' => $row['status'],
                    'ditangani_oleh' => $row['handler']->id,
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
