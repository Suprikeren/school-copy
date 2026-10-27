<?php

namespace Database\Seeders;

use App\Models\Notifikasi;
use App\Models\PengajuanPerubahan;
use App\Models\Siswa;
use App\Models\SpSiswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;

class NotifikasiSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $activeYear = TahunAjaran::query()->where('slug', '2026-2027')->first()
            ?? TahunAjaran::query()->where('aktif', true)->firstOrFail();

        $superAdmin = User::role('super-admin')->firstOrFail();

        $admin = User::query()->where('email', 'admin@example.com')->first()
            ?? User::role('admin')->firstOrFail();

        $kesiswaan = User::query()->where('email', 'kesiswaan@example.com')->first()
            ?? User::role('kesiswaan')->firstOrFail();

        $bk = User::query()->where('email', 'bk@example.com')->first()
            ?? User::role('bk')->firstOrFail();

        $gilang = Siswa::query()->where('nis', '2026007')->firstOrFail();

        $sp1 = SpSiswa::query()
            ->where('siswa_id', $gilang->id)
            ->where('tahun_ajaran_id', $activeYear->id)
            ->where('level', 1)
            ->first();

        $sp2 = SpSiswa::query()
            ->where('siswa_id', $gilang->id)
            ->where('tahun_ajaran_id', $activeYear->id)
            ->where('level', 2)
            ->first();

        $pendingRequest = PengajuanPerubahan::query()->where('status', 'menunggu')->first();
        $approvedRequest = PengajuanPerubahan::query()->where('status', 'disetujui')->first();

        $notifications = [
            [
                'user' => $bk,
                'tipe' => 'sp_baru',
                'isi' => 'SP 1 untuk Gilang Saputra telah diterbitkan.',
                'tautan' => $sp1 !== null ? '/sp-siswa/'.$sp1->id : null,
                'dibaca_pada' => null,
            ],
            [
                'user' => $superAdmin,
                'tipe' => 'pengajuan_perubahan',
                'isi' => 'Pengajuan perubahan pelanggaran Ahmad Fauzan menunggu persetujuan.',
                'tautan' => $pendingRequest !== null ? '/pengajuan-perubahan/'.$pendingRequest->id : null,
                'dibaca_pada' => null,
            ],
            [
                'user' => $bk,
                'tipe' => 'keputusan_pengajuan',
                'isi' => 'Pengajuan perubahan pelanggaran Dewi Anggraini disetujui.',
                'tautan' => $approvedRequest !== null ? '/pengajuan-perubahan/'.$approvedRequest->id : null,
                'dibaca_pada' => '2026-08-20 10:05:00',
            ],
            [
                'user' => $kesiswaan,
                'tipe' => 'tindak_lanjut',
                'isi' => 'Tindak lanjut pembinaan untuk Fajar Ramadhan belum diselesaikan.',
                'tautan' => '/tindak-lanjut',
                'dibaca_pada' => null,
            ],
            [
                'user' => $admin,
                'tipe' => 'sistem',
                'isi' => 'Data contoh RELASI berhasil dimuat.',
                'tautan' => null,
                'dibaca_pada' => '2026-07-01 08:00:00',
            ],
            [
                'user' => $kesiswaan,
                'tipe' => 'sp_baru',
                'isi' => 'SP 2 untuk Gilang Saputra telah diterbitkan.',
                'tautan' => $sp2 !== null ? '/sp-siswa/'.$sp2->id : null,
                'dibaca_pada' => '2026-10-05 13:05:00',
            ],
        ];

        foreach ($notifications as $notification) {
            $this->firstOrCreateWithId(
                Notifikasi::class,
                [
                    'user_id' => $notification['user']->id,
                    'tipe' => $notification['tipe'],
                    'isi' => $notification['isi'],
                ],
                [
                    'tautan' => $notification['tautan'],
                    'dibaca_pada' => $notification['dibaca_pada'],
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
