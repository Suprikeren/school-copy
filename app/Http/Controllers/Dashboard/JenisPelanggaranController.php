<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\JenisPelanggaranRequest;
use App\Models\AuditLog;
use App\Models\JenisPelanggaran;
use App\Models\PasalTatib;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;

class JenisPelanggaranController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('jenis index'), only: ['index']),
            new Middleware(PermissionMiddleware::using('jenis store'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('jenis edit'), only: ['update', 'edit']),
            new Middleware(PermissionMiddleware::using('jenis show'), only: ['show']),
            new Middleware(PermissionMiddleware::using('jenis delete'), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $jenis = JenisPelanggaran::query()
            ->with(['pasalTatib' => fn ($query) => $query->select('pasal_tatib.id', 'nomor')])
            ->when(
                $request->search,
                fn ($query) => $query->search($request->search)
            )
            ->latest()
            ->paginate($perPage, ['*'], 'page', $currentPage)->withQueryString();

        return inertia('dashboard/tatib/jenis-pelanggaran/index', [
            'jenis' => $jenis,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'filters' => ['search' => $request->search],
        ]);
    }

    public function create()
    {
        $pasal = PasalTatib::query()
            ->where('aktif', true)
            ->orderBy('nomor')
            ->get(['id', 'nomor', 'isi_ringkas']);

        return inertia('dashboard/tatib/jenis-pelanggaran/create', [
            'pasal' => $pasal,
        ]);
    }

    public function store(JenisPelanggaranRequest $request)
    {
        $request->validate([
            'pasal_ids' => 'required|array|min:1',
            'pasal_ids.*' => 'exists:pasal_tatib,id',
        ], [
            'pasal_ids.required' => 'Pilih minimal satu pasal.',
            'pasal_ids.min' => 'Pilih minimal satu pasal.',
            'pasal_ids.*.exists' => 'Pasal yang dipilih tidak ditemukan.',
        ]);

        DB::transaction(function () use ($request) {
            $jenis = JenisPelanggaran::create([
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'poin' => $request->poin,
                'aktif' => $request->aktif,
            ]);

            $jenis->pasalTatib()->sync($request->pasal_ids);

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'input',
                'tabel' => 'jenis_pelanggaran',
                'data_id' => $jenis->id,
                'data_lama' => null,
                'data_baru' => $jenis->only(['nama', 'kategori', 'poin', 'aktif']) + [
                    'pasal_ids' => $request->pasal_ids,
                ],
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.tatib.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function show(JenisPelanggaran $jenis_pelanggaran)
    {
        $jenis_pelanggaran->load('pasalTatib');

        return inertia('dashboard/tatib/jenis-pelanggaran/show', [
            'jenis' => $jenis_pelanggaran,
        ]);
    }

    public function edit(JenisPelanggaran $jenis_pelanggaran)
    {
        $jenis_pelanggaran->load('pasalTatib');

        $pasal = PasalTatib::query()
            ->where('aktif', true)
            ->orderBy('nomor')
            ->get(['id', 'nomor', 'isi_ringkas']);

        return inertia('dashboard/tatib/jenis-pelanggaran/edit', [
            'jenis' => $jenis_pelanggaran,
            'pasal' => $pasal,
        ]);
    }

    public function update(JenisPelanggaranRequest $request, JenisPelanggaran $jenis_pelanggaran)
    {
        $request->validate([
            'pasal_ids' => 'required|array|min:1',
            'pasal_ids.*' => 'exists:pasal_tatib,id',
        ], [
            'pasal_ids.required' => 'Pilih minimal satu pasal.',
            'pasal_ids.min' => 'Pilih minimal satu pasal.',
            'pasal_ids.*.exists' => 'Pasal yang dipilih tidak ditemukan.',
        ]);

        $dataLama = $jenis_pelanggaran->only(['nama', 'kategori', 'poin', 'aktif']) + [
            'pasal_ids' => $jenis_pelanggaran->pasalTatib()->pluck('pasal_tatib.id')->all(),
        ];

        DB::transaction(function () use ($request, $jenis_pelanggaran) {
            $jenis_pelanggaran->update([
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'poin' => $request->poin,
                'aktif' => $request->aktif,
            ]);

            $jenis_pelanggaran->pasalTatib()->sync($request->pasal_ids);

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'edit',
                'tabel' => 'jenis_pelanggaran',
                'data_id' => $jenis_pelanggaran->id,
                'data_lama' => $jenis_pelanggaran->only(['nama', 'kategori', 'poin', 'aktif']) + [
                    'pasal_ids' => $request->pasal_ids,
                ],
                'data_baru' => $jenis_pelanggaran->only(['nama', 'kategori', 'poin', 'aktif']) + [
                    'pasal_ids' => $request->pasal_ids,
                ],
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return to_route('dashboard.tatib.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function destroy(Request $request, JenisPelanggaran $jenis_pelanggaran)
    {
        $dataLama = $jenis_pelanggaran->only(['nama', 'kategori', 'poin', 'aktif']);

        $jenis_pelanggaran->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'hapus',
            'tabel' => 'jenis_pelanggaran',
            'data_id' => $jenis_pelanggaran->id,
            'data_lama' => $dataLama,
            'data_baru' => null,
            'ip' => $request->ip(),
            'waktu' => now(),
        ]);

        return back()->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
