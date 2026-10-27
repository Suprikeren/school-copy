<?php

namespace Database\Seeders;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\SpSiswa;
use App\Models\TahunAjaran;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;
use RuntimeException;

class SpSiswaSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $activeYear = TahunAjaran::query()->where('slug', '2026-2027')->first()
            ?? TahunAjaran::query()->where('aktif', true)->firstOrFail();

        $thresholds = [
            1 => 30,
            2 => 60,
            3 => 90,
        ];

        $rows = [
            ['nis' => '2026007', 'level' => 1, 'tanggal' => '2026-09-15'],
            ['nis' => '2026010', 'level' => 1, 'tanggal' => '2026-09-15'],
            ['nis' => '2026008', 'level' => 1, 'tanggal' => '2026-10-05'],
            ['nis' => '2026007', 'level' => 2, 'tanggal' => '2026-10-05'],
        ];

        $students = Siswa::query()
            ->whereIn('nis', array_unique(array_column($rows, 'nis')))
            ->get()
            ->keyBy('nis');

        foreach ($rows as $row) {
            $student = $students->get($row['nis']);

            if ($student === null) {
                throw new RuntimeException("Siswa dengan NIS {$row['nis']} tidak ditemukan.");
            }

            $sp = SpSiswa::query()
                ->where('siswa_id', $student->id)
                ->where('tahun_ajaran_id', $activeYear->id)
                ->where('level', $row['level'])
                ->first();

            if ($sp !== null) {
                continue;
            }

            $totalPoints = (int) Pelanggaran::query()
                ->where('siswa_id', $student->id)
                ->where('tahun_ajaran_id', $activeYear->id)
                ->whereHas('kasus', fn ($query) => $query->where('waktu', '<=', $row['tanggal'].' 23:59:59'))
                ->sum('poin');

            if ($totalPoints < $thresholds[$row['level']]) {
                throw new RuntimeException(
                    "Poin siswa dengan NIS {$row['nis']} belum mencapai ambang SP {$row['level']}."
                );
            }

            $this->firstOrCreateWithId(
                SpSiswa::class,
                [
                    'siswa_id' => $student->id,
                    'tahun_ajaran_id' => $activeYear->id,
                    'level' => $row['level'],
                ],
                [
                    'poin_saat_tercapai' => $totalPoints,
                    'tanggal' => $row['tanggal'],
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
