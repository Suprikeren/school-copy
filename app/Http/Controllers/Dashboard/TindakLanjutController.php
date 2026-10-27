<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\TindakLanjutRequest;
use App\Models\AuditLog;
use App\Models\Siswa;
use App\Models\SpSiswa;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;

class TindakLanjutController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('tindak index'), only: ['index']),
            new Middleware(PermissionMiddleware::using('tindak store'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('tindak edit'), only: ['update', 'edit']),
            new Middleware(PermissionMiddleware::using('tindak delete'), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $tindak = TindakLanjut::query()
            ->with([
                'siswa:id,nis,nama',
                'user:id,name',
                'spSiswa:id,level',
            ])
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

        return inertia('dashboard/tindak-lanjut/index', [
            'tindak' => $tindak,
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
        return inertia('dashboard/tindak-lanjut/create', [
            'siswa' => $this->siswaOptions(),
            'sp' => $this->spOptions(),
        ]);
    }

    public function store(TindakLanjutRequest $request)
    {
        DB::transaction(function () use ($request) {
            $tindak = TindakLanjut::create([
                'siswa_id' => $request->siswa_id,
                'sp_siswa_id' => $request->sp_siswa_id,
                'pelanggaran_id' => $request->pelanggaran_id,
                'tindakan' => $request->tindakan,
                'tanggal' => $request->tanggal,
                'catatan' => $request->catatan,
                'status' => $request->status,
                'ditangani_oleh' => auth()->id(),
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'input',
                'tabel' => 'tindak_lanjut',
                'data_id' => $tindak->id,
                'data_lama' => null,
                'data_baru' => $tindak->only([
                    'siswa_id',
                    'tindakan',
                    'tanggal',
                    'catatan',
                    'status',
                ]),
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil disimpan.');
    }

    public function edit(TindakLanjut $tindak_lanjut)
    {
        return inertia('dashboard/tindak-lanjut/edit', [
            'tindak' => $tindak_lanjut,
            'siswa' => $this->siswaOptions(),
            'sp' => $this->spOptions(),
        ]);
    }

    public function update(TindakLanjutRequest $request, TindakLanjut $tindak_lanjut)
    {
        $dataLama = $tindak_lanjut->only([
            'siswa_id',
            'tindakan',
            'tanggal',
            'catatan',
            'status',
        ]);

        DB::transaction(function () use ($request, $tindak_lanjut, $dataLama) {
            $tindak_lanjut->update([
                'siswa_id' => $request->siswa_id,
                'sp_siswa_id' => $request->sp_siswa_id,
                'pelanggaran_id' => $request->pelanggaran_id,
                'tindakan' => $request->tindakan,
                'tanggal' => $request->tanggal,
                'catatan' => $request->catatan,
                'status' => $request->status,
                'ditangani_oleh' => auth()->id(),
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'edit',
                'tabel' => 'tindak_lanjut',
                'data_id' => $tindak_lanjut->id,
                'data_lama' => $dataLama,
                'data_baru' => $tindak_lanjut->only([
                    'siswa_id',
                    'tindakan',
                    'tanggal',
                    'catatan',
                    'status',
                ]),
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil diubah.');
    }

    public function destroy(Request $request, TindakLanjut $tindak_lanjut)
    {
        $dataLama = $tindak_lanjut->only(['siswa_id', 'tindakan', 'status']);

        $tindak_lanjut->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'hapus',
            'tabel' => 'tindak_lanjut',
            'data_id' => $tindak_lanjut->id,
            'data_lama' => $dataLama,
            'data_baru' => null,
            'ip' => $request->ip(),
            'waktu' => now(),
        ]);

        return back()->with('success', 'Tindak lanjut berhasil dihapus.');
    }

    private function siswaOptions()
    {
        return Siswa::query()
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama']);
    }

    private function spOptions()
    {
        return SpSiswa::query()
            ->with('siswa:id,nama')
            ->orderByDesc('tanggal')
            ->get(['id', 'siswa_id', 'level', 'poin_saat_tercapai']);
    }
}
