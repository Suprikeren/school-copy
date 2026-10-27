<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasalTatibRequest;
use App\Models\AuditLog;
use App\Models\PasalTatib;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class PasalTatibController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('pasal index'), only: ['index']),
            new Middleware(PermissionMiddleware::using('pasal store'), only: ['store', 'create']),
            new Middleware(PermissionMiddleware::using('pasal edit'), only: ['update', 'edit']),
            new Middleware(PermissionMiddleware::using('pasal show'), only: ['show']),
            new Middleware(PermissionMiddleware::using('pasal delete'), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $pasal = PasalTatib::query()
            ->select('id', 'nomor', 'isi_ringkas', 'aktif', 'created_by', 'created_at')
            ->when(
                $request->search,
                fn ($query) => $query->search($request->search)
            )
            ->latest()
            ->paginate($perPage, ['*'], 'page', $currentPage)->withQueryString();

        return inertia('dashboard/tatib/pasal/index', [
            'pasal' => $pasal,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'filters' => ['search' => $request->search],
        ]);
    }

    public function create()
    {
        return inertia('dashboard/tatib/pasal/create');
    }

    public function store(PasalTatibRequest $request)
    {
        $pasal = PasalTatib::create([
            'nomor' => $request->nomor,
            'isi_ringkas' => $request->isi_ringkas,
            'aktif' => $request->aktif,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'input',
            'tabel' => 'pasal_tatib',
            'data_id' => $pasal->id,
            'data_lama' => null,
            'data_baru' => $pasal->only(['nomor', 'isi_ringkas', 'aktif']),
            'ip' => $request->ip(),
            'waktu' => now(),
        ]);

        return to_route('dashboard.tatib.pasal.index')->with('success', 'Pasal tatib berhasil ditambahkan.');
    }

    public function show(PasalTatib $pasal)
    {
        return inertia('dashboard/tatib/pasal/show', ['pasal' => $pasal]);
    }

    public function edit(PasalTatib $pasal)
    {
        return inertia('dashboard/tatib/pasal/edit', ['pasal' => $pasal]);
    }

    public function update(PasalTatibRequest $request, PasalTatib $pasal)
    {
        $dataLama = $pasal->only(['nomor', 'isi_ringkas', 'aktif']);

        $pasal->update([
            'nomor' => $request->nomor,
            'isi_ringkas' => $request->isi_ringkas,
            'aktif' => $request->aktif,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'edit',
            'tabel' => 'pasal_tatib',
            'data_id' => $pasal->id,
            'data_lama' => $dataLama,
            'data_baru' => $pasal->only(['nomor', 'isi_ringkas', 'aktif']),
            'ip' => $request->ip(),
            'waktu' => now(),
        ]);

        return to_route('dashboard.tatib.pasal.index')->with('success', 'Pasal tatib berhasil diperbarui.');
    }

    public function destroy(Request $request, PasalTatib $pasal)
    {
        $dataLama = $pasal->only(['nomor', 'isi_ringkas', 'aktif']);

        $pasal->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'hapus',
            'tabel' => 'pasal_tatib',
            'data_id' => $pasal->id,
            'data_lama' => $dataLama,
            'data_baru' => null,
            'ip' => $request->ip(),
            'waktu' => now(),
        ]);

        return back()->with('success', 'Pasal tatib berhasil dihapus.');
    }
}
