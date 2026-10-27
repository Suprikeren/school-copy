<?php

namespace Database\Seeders;

use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;
use RuntimeException;

class RiwayatKelasSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $activeYear = TahunAjaran::query()->where('slug', '2026-2027')->first()
            ?? TahunAjaran::query()->where('aktif', true)->firstOrFail();

        $previousYear = TahunAjaran::query()->where('slug', '2025-2026')->first();

        $currentClasses = [
            '2026001' => 'X-1',
            '2026002' => 'X-1',
            '2026003' => 'X-2',
            '2026004' => 'X-2',
            '2026005' => 'XI-1',
            '2026006' => 'XI-1',
            '2026007' => 'XI-2',
            '2026008' => 'XI-2',
            '2026009' => 'XII-1',
            '2026010' => 'XII-1',
            '2026011' => 'XII-2',
            '2026012' => 'XII-2',
        ];

        $previousClasses = [
            '2026005' => 'X-1',
            '2026006' => 'X-1',
            '2026007' => 'X-2',
            '2026008' => 'X-2',
            '2026009' => 'XI-1',
            '2026010' => 'XI-1',
            '2026011' => 'XI-2',
            '2026012' => 'XI-2',
        ];

        $students = Siswa::query()->whereIn('nis', array_keys($currentClasses))->get()->keyBy('nis');

        foreach ($currentClasses as $nis => $kelas) {
            $student = $students->get($nis);

            if ($student === null) {
                throw new RuntimeException("Siswa dengan NIS {$nis} tidak ditemukan untuk riwayat kelas.");
            }

            $this->firstOrCreateWithId(
                RiwayatKelas::class,
                ['siswa_id' => $student->id, 'tahun_ajaran_id' => $activeYear->id],
                [
                    'kelas' => $kelas,
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }

        if ($previousYear === null) {
            return;
        }

        foreach ($previousClasses as $nis => $kelas) {
            $student = $students->get($nis);

            if ($student === null) {
                throw new RuntimeException("Siswa dengan NIS {$nis} tidak ditemukan untuk riwayat kelas.");
            }

            $this->firstOrCreateWithId(
                RiwayatKelas::class,
                ['siswa_id' => $student->id, 'tahun_ajaran_id' => $previousYear->id],
                [
                    'kelas' => $kelas,
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
