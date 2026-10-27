<?php

namespace Database\Seeders;

use App\Models\JenisPelanggaran;
use App\Models\Kasus;
use App\Models\Pelanggaran;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;
use RuntimeException;

class PelanggaranSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $activeYear = TahunAjaran::query()->where('slug', '2026-2027')->first()
            ?? TahunAjaran::query()->where('aktif', true)->firstOrFail();

        $rows = [
            ['waktu' => '2026-08-10 07:15:00', 'nis' => '2026001', 'jenis' => 'terlambat-masuk-sekolah'],
            ['waktu' => '2026-08-10 07:15:00', 'nis' => '2026002', 'jenis' => 'terlambat-masuk-sekolah'],
            ['waktu' => '2026-08-10 07:15:00', 'nis' => '2026003', 'jenis' => 'terlambat-masuk-sekolah'],
            ['waktu' => '2026-08-10 07:15:00', 'nis' => '2026007', 'jenis' => 'terlambat-masuk-sekolah'],
            ['waktu' => '2026-08-18 09:30:00', 'nis' => '2026004', 'jenis' => 'tidak-memakai-atribut-seragam-lengkap'],
            ['waktu' => '2026-08-18 09:30:00', 'nis' => '2026005', 'jenis' => 'tidak-memakai-atribut-seragam-lengkap'],
            ['waktu' => '2026-08-18 09:30:00', 'nis' => '2026010', 'jenis' => 'tidak-memakai-atribut-seragam-lengkap'],
            ['waktu' => '2026-09-02 10:00:00', 'nis' => '2026006', 'jenis' => 'tidak-mengikuti-pelajaran-tanpa-izin'],
            ['waktu' => '2026-09-02 10:00:00', 'nis' => '2026007', 'jenis' => 'membolos-sekolah'],
            ['waktu' => '2026-09-15 12:30:00', 'nis' => '2026007', 'jenis' => 'merokok-di-lingkungan-sekolah'],
            ['waktu' => '2026-09-15 12:30:00', 'nis' => '2026010', 'jenis' => 'merokok-di-lingkungan-sekolah'],
            ['waktu' => '2026-10-05 08:45:00', 'nis' => '2026008', 'jenis' => 'berkelahi'],
            ['waktu' => '2026-10-05 08:45:00', 'nis' => '2026007', 'jenis' => 'berkelahi'],
        ];

        $students = Siswa::query()
            ->whereIn('nis', array_unique(array_column($rows, 'nis')))
            ->get()
            ->keyBy('nis');

        $classes = RiwayatKelas::query()
            ->where('tahun_ajaran_id', $activeYear->id)
            ->whereIn('siswa_id', $students->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        $cases = Kasus::query()
            ->where('tahun_ajaran_id', $activeYear->id)
            ->whereIn('waktu', array_unique(array_column($rows, 'waktu')))
            ->get()
            ->keyBy(fn (Kasus $case) => $case->waktu->format('Y-m-d H:i:s'));

        $types = JenisPelanggaran::query()
            ->with('pasalTatib')
            ->whereIn('slug', array_unique(array_column($rows, 'jenis')))
            ->get()
            ->keyBy('slug');

        foreach ($rows as $row) {
            $case = $cases->get($row['waktu']);
            $student = $students->get($row['nis']);
            $class = $classes->get($student?->id);
            $type = $types->get($row['jenis']);

            if ($case === null) {
                throw new RuntimeException("Kasus pada waktu {$row['waktu']} tidak ditemukan.");
            }

            if ($student === null) {
                throw new RuntimeException("Siswa dengan NIS {$row['nis']} tidak ditemukan.");
            }

            if ($class === null) {
                throw new RuntimeException("Riwayat kelas siswa dengan NIS {$row['nis']} tidak ditemukan.");
            }

            if ($type === null) {
                throw new RuntimeException("Jenis pelanggaran {$row['jenis']} tidak ditemukan.");
            }

            $pasal = $type->pasalTatib
                ->map(fn ($pasal) => "{$pasal->nomor}: {$pasal->isi_ringkas}")
                ->implode('; ');

            $this->firstOrCreateWithId(
                Pelanggaran::class,
                [
                    'kasus_id' => $case->id,
                    'siswa_id' => $student->id,
                    'jenis_pelanggaran_id' => $type->id,
                ],
                [
                    'tahun_ajaran_id' => $activeYear->id,
                    'nama_pelanggaran' => $type->nama,
                    'poin' => $type->poin,
                    'kelas' => $class->kelas,
                    'pasal' => $pasal !== '' ? $pasal : '-',
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ],
                true
            );
        }
    }
}
