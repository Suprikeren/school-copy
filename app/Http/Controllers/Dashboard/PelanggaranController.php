<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePelanggaranRequest;
use App\Models\AuditLog;
use App\Models\JenisPelanggaran;
use App\Models\Kasus;
use App\Models\Notifikasi;
use App\Models\Pelanggaran;
use App\Models\Pengaturan;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\SpSiswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;

class PelanggaranController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('pelanggaran index'), only: ['index']),
            new Middleware(PermissionMiddleware::using('pelanggaran store'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('pelanggaran show'), only: ['show']),
        ];
    }

    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $pelanggaran = Pelanggaran::query()
            ->with([
                'siswa:id,nis,nama',
                'jenisPelanggaran:id,nama,poin',
                'kasus:id,pencatat_id,waktu',
                'kasus.pencatat:id,name',
            ])
            ->when(
                $request->search,
                fn ($query) => $query->search($request->search)
            )
            ->when(
                $request->kelas,
                fn ($query) => $query->where('kelas', $request->kelas)
            )
            ->when(
                $request->tanggal,
                fn ($query) => $query->whereDate('created_at', $request->tanggal)
            )
            ->latest()
            ->paginate($perPage, ['*'], 'page', $currentPage)->withQueryString();

        $kelas = Pelanggaran::query()
            ->select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        return inertia('dashboard/pelanggaran/index', [
            'pelanggaran' => $pelanggaran,
            'kelas' => $kelas,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'filters' => [
                'search' => $request->search,
                'kelas' => $request->kelas,
                'tanggal' => $request->tanggal,
            ],
        ]);
    }

    public function create()
    {
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first();

        $siswa = Siswa::query()
            ->where('status', 'aktif')
            ->with(['riwayatKelas' => function ($query) use ($tahunAjaran) {
                $query->select('id', 'siswa_id', 'tahun_ajaran_id', 'kelas')
                    ->where('tahun_ajaran_id', $tahunAjaran?->id);
            }])
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama']);

        $jenis = JenisPelanggaran::query()
            ->where('aktif', true)
            ->with(['pasalTatib' => fn ($query) => $query->select('pasal_tatib.id', 'nomor')])
            ->orderBy('nama')
            ->get(['id', 'nama', 'kategori', 'poin']);

        return inertia('dashboard/pelanggaran/create', [
            'siswa' => $siswa,
            'jenis' => $jenis,
            'tahunAjaran' => $tahunAjaran,
        ]);
    }

    public function store(StorePelanggaranRequest $request)
    {
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first();

        if (! $tahunAjaran) {
            return back()->with('error', 'Tahun ajaran aktif belum ditentukan.');
        }

        $jenis = JenisPelanggaran::query()
            ->with('pasalTatib')
            ->findOrFail($request->jenis_pelanggaran_id);

        $pasal = $jenis->pasalTatib->pluck('nomor')->implode(', ');
        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('bukti', 'public')
            : null;

        $spBaru = DB::transaction(function () use ($request, $tahunAjaran, $jenis, $pasal, $fotoPath) {
            $kasus = Kasus::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'waktu' => now(),
                'pencatat_id' => auth()->id(),
                'keterangan' => $request->keterangan,
                'foto_path' => $fotoPath,
            ]);

            $spBaru = [];

            foreach ($request->siswa_ids as $siswaId) {
                $kelas = RiwayatKelas::query()
                    ->where('siswa_id', $siswaId)
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->value('kelas') ?? '-';

                Pelanggaran::create([
                    'kasus_id' => $kasus->id,
                    'siswa_id' => $siswaId,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'jenis_pelanggaran_id' => $jenis->id,
                    'nama_pelanggaran' => $jenis->nama,
                    'poin' => $jenis->poin,
                    'kelas' => $kelas,
                    'pasal' => $pasal,
                ]);

                $total = (int) Pelanggaran::query()
                    ->where('siswa_id', $siswaId)
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->sum('poin');

                $ambang = Pengaturan::ambangSp();
                $levelTercapai = 0;
                foreach ($ambang as $level => $poin) {
                    if ($total >= $poin) {
                        $levelTercapai = $level;
                    }
                }

                if ($levelTercapai > 0) {
                    for ($level = 1; $level <= $levelTercapai; $level++) {
                        $sp = SpSiswa::query()->firstOrCreate(
                            [
                                'siswa_id' => $siswaId,
                                'tahun_ajaran_id' => $tahunAjaran->id,
                                'level' => $level,
                            ],
                            [
                                'poin_saat_tercapai' => $total,
                                'tanggal' => now()->toDateString(),
                            ]
                        );

                        if ($sp->wasRecentlyCreated) {
                            $spBaru[] = [
                                'siswa_id' => $siswaId,
                                'level' => $level,
                                'poin' => $total,
                            ];
                        }
                    }
                }
            }

            foreach ($spBaru as $sp) {
                $siswa = Siswa::query()->find($sp['siswa_id']);

                $penerima = User::query()
                    ->whereHas('roles', fn ($query) => $query->whereIn('name', ['kesiswaan', 'bk']))
                    ->pluck('id');

                foreach ($penerima as $userId) {
                    Notifikasi::create([
                        'user_id' => $userId,
                        'tipe' => 'sp',
                        'isi' => "{$siswa->nama} mencapai SP {$sp['level']} dengan total {$sp['poin']} poin.",
                        'tautan' => route('dashboard.siswa.show', $siswa->id),
                    ]);
                }
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'input',
                'tabel' => 'pelanggaran',
                'data_id' => $kasus->id,
                'data_lama' => null,
                'data_baru' => [
                    'kasus_id' => $kasus->id,
                    'jenis_pelanggaran_id' => $jenis->id,
                    'siswa_ids' => $request->siswa_ids,
                    'poin' => $jenis->poin,
                    'pasal' => $pasal,
                ],
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);

            return $spBaru;
        });

        $pesan = 'Pelanggaran berhasil disimpan.';
        if (count($spBaru) > 0) {
            $detail = collect($spBaru)
                ->map(fn ($sp) => "SP {$sp['level']} ({$sp['poin']} poin)")
                ->implode(', ');
            $pesan .= " Peringatan: siswa baru mencapai {$detail}.";
        }

        return to_route('dashboard.pelanggaran.index')->with('success', $pesan);
    }

    public function show(Pelanggaran $pelanggaran)
    {
        $pelanggaran->load([
            'siswa:id,nis,nama',
            'jenisPelanggaran:id,nama,kategori,poin',
            'kasus:id,tahun_ajaran_id,waktu,pencatat_id,keterangan,foto_path',
            'kasus.pencatat:id,name',
            'kasus.tahunAjaran:id,nama',
        ]);

        return inertia('dashboard/pelanggaran/show', [
            'pelanggaran' => $pelanggaran,
            'pengajuan' => $pelanggaran->pengajuan()->latest()->get(),
            'bisaAjukan' => $pelanggaran->created_by === auth()->user()->name,
        ]);
    }
}
