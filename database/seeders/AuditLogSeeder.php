<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\JenisPelanggaran;
use App\Models\Kasus;
use App\Models\Pelanggaran;
use App\Models\PengajuanPerubahan;
use App\Models\Siswa;
use App\Models\SpSiswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
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

        $ahmad = Siswa::query()->where('nis', '2026001')->firstOrFail();
        $dewi = Siswa::query()->where('nis', '2026004')->firstOrFail();
        $gilang = Siswa::query()->where('nis', '2026007')->firstOrFail();

        $case = Kasus::query()
            ->where('tahun_ajaran_id', $activeYear->id)
            ->where('waktu', '2026-08-10 07:15:00')
            ->firstOrFail();

        $jenisAtribut = JenisPelanggaran::query()
            ->where('slug', 'tidak-memakai-atribut-seragam-lengkap')
            ->firstOrFail();

        $dewiViolation = Pelanggaran::query()
            ->where('siswa_id', $dewi->id)
            ->where('jenis_pelanggaran_id', $jenisAtribut->id)
            ->firstOrFail();

        $approvedRequest = PengajuanPerubahan::query()->where('status', 'disetujui')->firstOrFail();

        $sp1 = SpSiswa::query()
            ->where('siswa_id', $gilang->id)
            ->where('tahun_ajaran_id', $activeYear->id)
            ->where('level', 1)
            ->firstOrFail();

        $logs = [
            [
                'user' => $superAdmin,
                'aksi' => 'create',
                'tabel' => 'tahun_ajaran',
                'data_id' => $activeYear->id,
                'data_lama' => null,
                'data_baru' => [
                    'nama' => $activeYear->nama,
                    'slug' => $activeYear->slug,
                    'aktif' => (bool) $activeYear->aktif,
                ],
                'ip' => '127.0.0.1',
                'waktu' => '2026-07-01 08:00:00',
            ],
            [
                'user' => $admin,
                'aksi' => 'create',
                'tabel' => 'siswa',
                'data_id' => $ahmad->id,
                'data_lama' => null,
                'data_baru' => [
                    'nis' => $ahmad->nis,
                    'nama' => $ahmad->nama,
                    'status' => $ahmad->status,
                ],
                'ip' => '127.0.0.1',
                'waktu' => '2026-07-10 09:00:00',
            ],
            [
                'user' => $kesiswaan,
                'aksi' => 'create',
                'tabel' => 'kasus',
                'data_id' => $case->id,
                'data_lama' => null,
                'data_baru' => [
                    'tahun_ajaran_id' => $activeYear->id,
                    'waktu' => '2026-08-10 07:15:00',
                    'pencatat_id' => $kesiswaan->id,
                ],
                'ip' => '192.168.1.10',
                'waktu' => '2026-08-10 07:20:00',
            ],
            [
                'user' => $superAdmin,
                'aksi' => 'update',
                'tabel' => 'pengajuan_perubahan',
                'data_id' => $approvedRequest->id,
                'data_lama' => [
                    'status' => 'menunggu',
                ],
                'data_baru' => [
                    'status' => 'disetujui',
                ],
                'ip' => '127.0.0.1',
                'waktu' => '2026-08-20 10:05:00',
            ],
            [
                'user' => $bk,
                'aksi' => 'create',
                'tabel' => 'sp_siswa',
                'data_id' => $sp1->id,
                'data_lama' => null,
                'data_baru' => [
                    'siswa_id' => $gilang->id,
                    'level' => 1,
                    'poin_saat_tercapai' => $sp1->poin_saat_tercapai,
                ],
                'ip' => '192.168.1.11',
                'waktu' => '2026-09-15 13:00:00',
            ],
            [
                'user' => null,
                'aksi' => 'login_gagal',
                'tabel' => 'users',
                'data_id' => null,
                'data_lama' => null,
                'data_baru' => [
                    'email' => 'tidak.dikenal@example.com',
                ],
                'ip' => '103.20.30.40',
                'waktu' => '2026-09-20 06:55:00',
            ],
        ];

        foreach ($logs as $log) {
            $this->firstOrCreateWithId(
                AuditLog::class,
                [
                    'user_id' => $log['user']?->id,
                    'aksi' => $log['aksi'],
                    'tabel' => $log['tabel'],
                    'data_id' => $log['data_id'],
                    'waktu' => $log['waktu'],
                ],
                [
                    'data_lama' => $log['data_lama'],
                    'data_baru' => $log['data_baru'],
                    'ip' => $log['ip'],
                    'created_by' => 'seeder',
                    'updated_by' => 'seeder',
                ]
            );
        }
    }
}
