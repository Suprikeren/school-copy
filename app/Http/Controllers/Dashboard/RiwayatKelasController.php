<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKelasRequest;
use App\Models\AuditLog;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;

class RiwayatKelasController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('riwayat index'), only: ['index']),
            new Middleware(PermissionMiddleware::using('riwayat store'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('riwayat edit'), only: ['update', 'edit']),
            new Middleware(PermissionMiddleware::using('riwayat delete'), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $riwayat = RiwayatKelas::query()
            ->with([
                'siswa:id,nis,nama',
                'tahunAjaran:id,nama',
            ])
            ->when(
                $request->search,
                fn ($query) => $query->search($request->search)
            )
            ->when(
                $request->siswa_id,
                fn ($query) => $query->where('siswa_id', $request->siswa_id)
            )
            ->when(
                $request->tahun_ajaran_id,
                fn ($query) => $query->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            )
            ->latest()
            ->paginate($perPage, ['*'], 'page', $currentPage)->withQueryString();

        return inertia('dashboard/riwayat-kelas/index', [
            'riwayat' => $riwayat,
            'tahunAjaran' => TahunAjaran::query()->orderByDesc('aktif')->orderBy('nama')->get(['id', 'nama']),
            'siswa' => $request->siswa_id
                ? Siswa::query()->find($request->siswa_id, ['id', 'nis', 'nama'])
                : null,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'filters' => [
                'search' => $request->search,
                'siswa_id' => $request->siswa_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
            ],
        ]);
    }

    public function create(Request $request)
    {
        return inertia('dashboard/riwayat-kelas/create', [
            'siswaOpsi' => Siswa::query()->orderBy('nama')->get(['id', 'nis', 'nama']),
            'tahunAjaranOpsi' => TahunAjaran::query()->orderByDesc('aktif')->orderBy('nama')->get(['id', 'nama']),
            'siswaTerpilih' => $request->siswa_id,
        ]);
    }

    public function store(RiwayatKelasRequest $request)
    {
        DB::transaction(function () use ($request) {
            $riwayat = RiwayatKelas::create([
                'siswa_id' => $request->siswa_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'kelas' => $request->kelas,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'input',
                'tabel' => 'riwayat_kelas',
                'data_id' => $riwayat->id,
                'data_lama' => null,
                'data_baru' => $riwayat->only(['siswa_id', 'tahun_ajaran_id', 'kelas']),
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.riwayat-kelas.index', ['siswa_id' => $request->siswa_id])
            ->with('success', 'Riwayat kelas berhasil ditambahkan.');
    }

    public function edit(RiwayatKelas $riwayatKelas)
    {
        return inertia('dashboard/riwayat-kelas/edit', [
            'riwayat' => $riwayatKelas,
            'siswaOpsi' => Siswa::query()->orderBy('nama')->get(['id', 'nis', 'nama']),
            'tahunAjaranOpsi' => TahunAjaran::query()->orderByDesc('aktif')->orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function update(RiwayatKelasRequest $request, RiwayatKelas $riwayatKelas)
    {
        $dataLama = $riwayatKelas->only(['siswa_id', 'tahun_ajaran_id', 'kelas']);

        DB::transaction(function () use ($request, $riwayatKelas, $dataLama) {
            $riwayatKelas->update([
                'siswa_id' => $request->siswa_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'kelas' => $request->kelas,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'edit',
                'tabel' => 'riwayat_kelas',
                'data_id' => $riwayatKelas->id,
                'data_lama' => $dataLama,
                'data_baru' => $riwayatKelas->only(['siswa_id', 'tahun_ajaran_id', 'kelas']),
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.riwayat-kelas.index', ['siswa_id' => $riwayatKelas->siswa_id])
            ->with('success', 'Riwayat kelas berhasil diperbarui.');
    }

    public function destroy(Request $request, RiwayatKelas $riwayatKelas)
    {
        $dataLama = $riwayatKelas->only(['siswa_id', 'tahun_ajaran_id', 'kelas']);
        $siswaId = $riwayatKelas->siswa_id;

        DB::transaction(function () use ($request, $riwayatKelas, $dataLama) {
            $riwayatKelas->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'hapus',
                'tabel' => 'riwayat_kelas',
                'data_id' => $dataLama['siswa_id'],
                'data_lama' => $dataLama,
                'data_baru' => null,
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.riwayat-kelas.index', ['siswa_id' => $siswaId])
            ->with('success', 'Riwayat kelas berhasil dihapus.');
    }
}
