<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiswaRequest;
use App\Models\AuditLog;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;

class SiswaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('siswa index'), only: ['index']),
            new Middleware(PermissionMiddleware::using('siswa store'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('siswa edit'), only: ['update', 'edit']),
            new Middleware(PermissionMiddleware::using('siswa show'), only: ['show']),
            new Middleware(PermissionMiddleware::using('siswa delete'), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first();

        $siswa = Siswa::query()
            ->with(['riwayatKelas' => function ($query) use ($tahunAjaran) {
                $query->select('id', 'siswa_id', 'tahun_ajaran_id', 'kelas')
                    ->where('tahun_ajaran_id', $tahunAjaran?->id);
            }])
            ->when(
                $request->search,
                fn ($query) => $query->search($request->search)
            )
            ->when(
                $request->status,
                fn ($query) => $query->where('status', $request->status)
            )
            ->latest()
            ->paginate($perPage, ['*'], 'page', $currentPage)->withQueryString();

        return inertia('dashboard/siswa/index', [
            'siswa' => $siswa,
            'tahunAjaran' => $tahunAjaran,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function create()
    {
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first();

        return inertia('dashboard/siswa/create', [
            'tahunAjaran' => $tahunAjaran,
        ]);
    }

    public function store(SiswaRequest $request)
    {
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first();

        DB::transaction(function () use ($request, $tahunAjaran) {
            $siswa = Siswa::create([
                'nis' => $request->nis,
                'nama' => $request->nama,
                'status' => $request->status,
            ]);

            if ($tahunAjaran && $request->kelas) {
                RiwayatKelas::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kelas' => $request->kelas,
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'input',
                'tabel' => 'siswa',
                'data_id' => $siswa->id,
                'data_lama' => null,
                'data_baru' => $siswa->only(['nis', 'nama', 'status']) + [
                    'kelas' => $request->kelas,
                ],
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        $siswa->load([
            'riwayatKelas.tahunAjaran',
            'spSiswa' => fn ($query) => $query->orderBy('level'),
        ]);

        return inertia('dashboard/siswa/show', ['siswa' => $siswa]);
    }

    public function edit(Siswa $siswa)
    {
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first();
        $riwayat = $siswa->riwayatKelas()
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->first();

        return inertia('dashboard/siswa/edit', [
            'siswa' => $siswa,
            'tahunAjaran' => $tahunAjaran,
            'kelas' => $riwayat?->kelas,
        ]);
    }

    public function update(SiswaRequest $request, Siswa $siswa)
    {
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first();
        $dataLama = $siswa->only(['nis', 'nama', 'status']);

        DB::transaction(function () use ($request, $siswa, $tahunAjaran, $dataLama) {
            $siswa->update([
                'nis' => $request->nis,
                'nama' => $request->nama,
                'status' => $request->status,
            ]);

            if ($tahunAjaran && $request->kelas) {
                RiwayatKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                    ],
                    ['kelas' => $request->kelas]
                );
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'edit',
                'tabel' => 'siswa',
                'data_id' => $siswa->id,
                'data_lama' => $dataLama,
                'data_baru' => $siswa->only(['nis', 'nama', 'status']) + [
                    'kelas' => $request->kelas,
                ],
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Request $request, Siswa $siswa)
    {
        $dataLama = $siswa->only(['nis', 'nama', 'status']);
        $siswaId = $siswa->id;

        try {
            DB::transaction(function () use ($request, $siswa, $dataLama, $siswaId) {
                RiwayatKelas::query()->where('siswa_id', $siswaId)->delete();

                $siswa->delete();

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'aksi' => 'hapus',
                    'tabel' => 'siswa',
                    'data_id' => $siswaId,
                    'data_lama' => $dataLama,
                    'data_baru' => null,
                    'ip' => $request->ip(),
                    'waktu' => now(),
                ]);
            });
        } catch (QueryException) {
            return back()->with(
                'error',
                'Siswa tidak dapat dihapus karena masih memiliki data pelanggaran, SP, atau tindak lanjut.'
            );
        }

        return back()->with('success', 'Siswa berhasil dihapus.');
    }
}
