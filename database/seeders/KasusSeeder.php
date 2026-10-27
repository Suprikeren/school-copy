<?php

namespace Database\Seeders;

use App\Models\Kasus;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;

class KasusSeeder extends Seeder
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

        $cases = [
            [
                'waktu' => '2026-08-10 07:15:00',
                'pencatat' => $kesiswaan,
                'keterangan' => 'Pemeriksaan keterlambatan siswa di gerbang sekolah.',
            ],
            [
                'waktu' => '2026-08-18 09:30:00',
                'pencatat' => $bk,
                'keterangan' => 'Pemeriksaan kelengkapan atribut seragam pada jam istirahat.',
            ],
            [
                'waktu' => '2026-09-02 10:00:00',
                'pencatat' => $kesiswaan,
                'keterangan' => 'Temuan siswa berada di luar kelas saat jam pelajaran berlangsung.',
            ],
            [
                'waktu' => '2026-09-15 12:30:00',
                'pencatat' => $bk,
                'keterangan' => 'Laporan guru mengenai siswa yang merokok di area belakang sekolah.',
            ],
            [
                'waktu' => '2026-10-05 08:45:00',
                'pencatat' => $bk,
                'keterangan' => 'Laporan perselisihan antarsiswa di area kantin.',
            ],
        ];

        foreach ($cases as $case) {
            $this->firstOrCreateWithId(
                Kasus::class,
                [
                    'tahun_ajaran_id' => $activeYear->id,
                    'waktu' => $case['waktu'],
                    'pencatat_id' => $case['pencatat']->id,
                ],
                [
                    'keterangan' => $case['keterangan'],
                    'foto_path' => null,
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
