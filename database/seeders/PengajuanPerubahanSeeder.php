<?php

namespace Database\Seeders;

use App\Models\JenisPelanggaran;
use App\Models\Pelanggaran;
use App\Models\PengajuanPerubahan;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;
use RuntimeException;

class PengajuanPerubahanSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $superAdmin = User::role('super-admin')->firstOrFail();

        $kesiswaan = User::query()->where('email', 'kesiswaan@example.com')->first()
            ?? User::role('kesiswaan')->firstOrFail();

        $bk = User::query()->where('email', 'bk@example.com')->first()
            ?? User::role('bk')->firstOrFail();

        $students = Siswa::query()
            ->whereIn('nis', ['2026001', '2026004', '2026008'])
            ->get()
            ->keyBy('nis');

        $jenisIds = JenisPelanggaran::query()
            ->whereIn('slug', [
                'terlambat-masuk-sekolah',
                'tidak-memakai-atribut-seragam-lengkap',
                'berkelahi',
            ])
            ->pluck('id', 'slug');

        $rows = [
            [
                'nis' => '2026001',
                'jenis' => 'terlambat-masuk-sekolah',
                'pengaju' => $kesiswaan,
                'alasan' => 'Nama pelanggaran perlu dilengkapi agar sesuai catatan petugas.',
                'data_lama' => [
                    'nama_pelanggaran' => 'Terlambat masuk sekolah',
                    'poin' => 5,
                ],
                'data_baru' => [
                    'nama_pelanggaran' => 'Terlambat masuk sekolah (lebih dari 15 menit)',
                    'poin' => 5,
                ],
                'status' => 'menunggu',
                'diputuskan' => null,
                'catatan_keputusan' => null,
                'waktu' => '2026-08-11 08:00:00',
            ],
            [
                'nis' => '2026004',
                'jenis' => 'tidak-memakai-atribut-seragam-lengkap',
                'pengaju' => $bk,
                'alasan' => 'Keterangan pelanggaran perlu diperjelas sesuai temuan pemeriksaan.',
                'data_lama' => [
                    'nama_pelanggaran' => 'Tidak memakai atribut seragam lengkap',
                ],
                'data_baru' => [
                    'nama_pelanggaran' => 'Tidak memakai atribut seragam lengkap (dasi dan topi)',
                ],
                'status' => 'disetujui',
                'diputuskan' => $superAdmin,
                'catatan_keputusan' => 'Disetujui karena perubahan hanya memperjelas keterangan pelanggaran.',
                'waktu' => '2026-08-20 10:00:00',
            ],
            [
                'nis' => '2026008',
                'jenis' => 'berkelahi',
                'pengaju' => $bk,
                'alasan' => 'Pelanggaran seharusnya dikategorikan sebagai pelanggaran ringan.',
                'data_lama' => [
                    'nama_pelanggaran' => 'Berkelahi',
                    'poin' => 30,
                ],
                'data_baru' => [
                    'nama_pelanggaran' => 'Berkelahi',
                    'poin' => 15,
                ],
                'status' => 'ditolak',
                'diputuskan' => $superAdmin,
                'catatan_keputusan' => 'Ditolak karena hasil pemeriksaan menunjukkan pelanggaran tetap berkategori berat.',
                'waktu' => '2026-10-06 09:00:00',
            ],
        ];

        foreach ($rows as $row) {
            $student = $students->get($row['nis']);

            if ($student === null) {
                throw new RuntimeException("Siswa dengan NIS {$row['nis']} tidak ditemukan.");
            }

            $jenisId = $jenisIds->get($row['jenis']);

            if ($jenisId === null) {
                throw new RuntimeException("Jenis pelanggaran {$row['jenis']} tidak ditemukan.");
            }

            $violation = Pelanggaran::query()
                ->where('siswa_id', $student->id)
                ->where('jenis_pelanggaran_id', $jenisId)
                ->firstOrFail();

            $violation->loadMissing('kasus');

            if ($violation->kasus?->pencatat_id !== $row['pengaju']->id) {
                throw new RuntimeException(
                    "Pengajuan untuk NIS {$row['nis']} harus diajukan oleh pencatat kasus yang sama."
                );
            }

            $this->firstOrCreateWithId(
                PengajuanPerubahan::class,
                [
                    'pelanggaran_id' => $violation->id,
                    'alasan' => $row['alasan'],
                ],
                [
                    'diajukan_oleh' => $row['pengaju']->id,
                    'data_lama' => $row['data_lama'],
                    'data_baru' => $row['data_baru'],
                    'status' => $row['status'],
                    'diputuskan_oleh' => $row['diputuskan']?->id,
                    'catatan_keputusan' => $row['catatan_keputusan'],
                    'waktu' => $row['waktu'],
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
