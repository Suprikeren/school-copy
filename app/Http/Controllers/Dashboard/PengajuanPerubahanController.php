<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notifikasi;
use App\Models\Pelanggaran;
use App\Models\PengajuanPerubahan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;

class PengajuanPerubahanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('pengajuan index'), only: ['index']),
            new Middleware(PermissionMiddleware::using('pengajuan store'), only: ['store']),
            new Middleware(PermissionMiddleware::using('pengajuan show'), only: ['show']),
            new Middleware(PermissionMiddleware::using('pengajuan putuskan'), only: ['putuskan']),
        ];
    }

    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $pengajuan = PengajuanPerubahan::query()
            ->with([
                'pelanggaran:id,siswa_id,nama_pelanggaran,kelas,poin,pasal',
                'pelanggaran.siswa:id,nis,nama',
                'user:id,name',
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

        return inertia('dashboard/pengajuan/index', [
            'pengajuan' => $pengajuan,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggaran_id' => 'required|exists:pelanggaran,id',
            'alasan' => 'required|string',
            'data_baru' => 'required|array',
            'data_baru.kelas' => 'required|string|max:255',
            'data_baru.poin' => 'required|integer|min:0',
            'data_baru.pasal' => 'required|string',
        ], [
            'pelanggaran_id.required' => 'Pelanggaran tidak boleh kosong.',
            'pelanggaran_id.exists' => 'Pelanggaran tidak ditemukan.',
            'alasan.required' => 'Alasan koreksi wajib diisi.',
            'data_baru.kelas.required' => 'Kelas wajib diisi.',
            'data_baru.poin.required' => 'Poin wajib diisi.',
            'data_baru.poin.integer' => 'Poin harus berupa angka.',
            'data_baru.pasal.required' => 'Pasal wajib diisi.',
        ]);

        $pelanggaran = Pelanggaran::query()->findOrFail($validated['pelanggaran_id']);

        DB::transaction(function () use ($request, $validated, $pelanggaran) {
            $pengajuan = PengajuanPerubahan::create([
                'pelanggaran_id' => $pelanggaran->id,
                'diajukan_oleh' => auth()->id(),
                'alasan' => $validated['alasan'],
                'data_lama' => $pelanggaran->only(['kelas', 'poin', 'pasal']),
                'data_baru' => $validated['data_baru'],
                'status' => 'menunggu',
                'waktu' => now(),
            ]);

            $penerima = User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', 'super-admin'))
                ->pluck('id');

            foreach ($penerima as $userId) {
                Notifikasi::create([
                    'user_id' => $userId,
                    'tipe' => 'pengajuan',
                    'isi' => 'Pengajuan perubahan pelanggaran dari '
                        .auth()->user()->name.' menunggu keputusan.',
                    'tautan' => route('dashboard.pengajuan.show', $pengajuan->id),
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'input',
                'tabel' => 'pengajuan_perubahan',
                'data_id' => $pengajuan->id,
                'data_lama' => null,
                'data_baru' => [
                    'pelanggaran_id' => $pelanggaran->id,
                    'alasan' => $validated['alasan'],
                    'data_baru' => $validated['data_baru'],
                ],
                'ip' => $request->ip(),
                'waktu' => now(),
            ]);
        });

        return back()->with('success', 'Pengajuan perubahan berhasil dikirim.');
    }

    public function show(PengajuanPerubahan $pengajuan)
    {
        $pengajuan->load([
            'pelanggaran.siswa:id,nis,nama',
            'user:id,name',
            'pemutus:id,name',
        ]);

        return inertia('dashboard/pengajuan/show', [
            'pengajuan' => $pengajuan,
            'bisaPutuskan' => auth()->user()->hasRole('super-admin'),
        ]);
    }

    public function putuskan(Request $request, PengajuanPerubahan $pengajuan)
    {
        $validated = $request->validate([
            'keputusan' => 'required|in:disetujui,ditolak',
            'catatan_keputusan' => 'nullable|string',
        ], [
            'keputusan.required' => 'Keputusan wajib dipilih.',
            'keputusan.in' => 'Keputusan hanya disetujui atau ditolak.',
        ]);

        $berhasil = DB::transaction(function () use ($request, $pengajuan, $validated) {
            $terupdate = PengajuanPerubahan::query()
                ->where('id', $pengajuan->id)
                ->where('status', 'menunggu')
                ->update([
                    'status' => $validated['keputusan'],
                    'diputuskan_oleh' => auth()->id(),
                    'catatan_keputusan' => $validated['catatan_keputusan'],
                ]);

            if ($terupdate === 0) {
                return false;
            }

            if ($validated['keputusan'] === 'disetujui') {
                $pelanggaran = $pengajuan->pelanggaran;
                $dataLama = $pelanggaran->only(['kelas', 'poin', 'pasal']);

                $pelanggaran->update([
                    'kelas' => $pengajuan->data_baru['kelas'],
                    'poin' => $pengajuan->data_baru['poin'],
                    'pasal' => $pengajuan->data_baru['pasal'],
                ]);

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'aksi' => 'setujui',
                    'tabel' => 'pelanggaran',
                    'data_id' => $pelanggaran->id,
                    'data_lama' => $dataLama,
                    'data_baru' => $pelanggaran->only(['kelas', 'poin', 'pasal']),
                    'ip' => $request->ip(),
                    'waktu' => now(),
                ]);
            } else {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'aksi' => 'tolak',
                    'tabel' => 'pengajuan_perubahan',
                    'data_id' => $pengajuan->id,
                    'data_lama' => ['status' => 'menunggu'],
                    'data_baru' => ['status' => 'ditolak'],
                    'ip' => $request->ip(),
                    'waktu' => now(),
                ]);
            }

            Notifikasi::create([
                'user_id' => $pengajuan->diajukan_oleh,
                'tipe' => 'pengajuan',
                'isi' => 'Pengajuan perubahan Anda '.$validated['keputusan'].'.'
                    .($validated['catatan_keputusan'] ? ' Catatan: '.$validated['catatan_keputusan'] : ''),
                'tautan' => route('dashboard.pelanggaran.show', $pengajuan->pelanggaran_id),
            ]);

            return true;
        });

        if (! $berhasil) {
            return back()->with('error', 'Pengajuan sudah diputuskan sebelumnya.');
        }

        return back()->with(
            'success',
            'Pengajuan '.$validated['keputusan'].'.'
        );
    }
}
