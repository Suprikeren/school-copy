# Plan.md — RENCANA EKSEKUSI CRUD 1–6 (RELASI)

**Scope khusus: hanya urutan Controller 1–6. Urutan 7–10 TIDAK dikerjakan dulu.**

Sumber: `text.md`, `controller.md`.

---

## CATATAN PENEMPATAN (konfirmasi)
- Semua controller domain → namespace `App\Http\Controllers\Dashboard\...` langsung
  (seperti `Dashboard\Spatie\UserController`, tapi tanpa subfolder Spatie).
- Sidebar & layout: JANGAN bikin komponen sendiri. Pakai yang disediakan template:
  `resources/js/components/{app-sidebar,nav-main,app-logo,header,breadcrumbs}.tsx`,
  layout `@/layouts/app-layout`, type `NavItem` dari `@/types/navigation`.
  Menu baru ditambahkan sebagai `mainNavItems`/grup baru di `app-sidebar.tsx`
  (pola `usersNavItems` yang sudah ada).

## ATURAN WAJIB (dipatuhi saat eksekusi)

1. **Ikuti template & gaya penulisan yang sudah ada**, contoh rujukan utama:
   - `app/Http/Controllers/Dashboard/Spatie/UserController.php`
   - `resources/js/pages/dashboard/users/{index,create,edit}.tsx`
   - `app/Http/Requests/*.php` (FormRequest dengan pesan validasi Bahasa Indonesia)
2. **WAJIB memakai types.ts (Inertia/React + TypeScript), BUKAN blade.**
   - Semua tipe data domain sudah ada di `resources/js/types/*.ts`
     (`pasal-tatib.ts`, `jenis-pelanggaran.ts`, `siswa.ts`, `tindak-lanjut.ts`, dst).
   - Type yang belum ada → tambahkan sebagai file baru di `resources/js/types/`
     lalu di-`export type * from` di `resources/js/types/index.ts`.
   - Props halaman dideklarasikan sebagai `interface XxxProps extends PageProps`
     di file page itu — ikuti pola `users/index.tsx`.
3. **WAJIB memakai DB::transaction** pada titik yang disebut di bawah
   (`controller.md` bagian 3). Fungsinya: supaya langkah berantai (insert jenis + pivot,
   kasus + banyak pelanggaran + SP + notifikasi + audit log) **saling menunggu** —
   kalau satu langkah gagal, SEMUA dibatalkan (rollback), tidak ada data setengah jadi.
4. **JANGAN set `created_by`/`updated_by` di controller.** Ini sudah otomatis di
   model lewat hook `booted()` (`creating`/`updating`) seperti model existing —
   controller cukup menyimpan data domainnya.
5. **Slug pakai paket `cviebrock/eloquent-sluggable` (sudah terpasang).**
   Trait `Sluggable` sudah dipasang di model `pasal_tatib` & `jenis_pelanggaran`
   (Urutan 1). Formatnya persis seperti ini — JANGAN diubah bentuknya:
   ```php
   use Cviebrock\EloquentSluggable\Sluggable;

   public function sluggable(): array
   {
       return [
           'slug' => [
               'source' => 'nomor',   // PasalTatib; JenisPelanggaran pakai 'nama'
           ],
       ];
   }
   ```
   Jangan generate slug manual (Str::slug/SlugService) di controller.
6. **Spatie permission: JANGAN ubah middleware apapun.** Keamanan cuma lewat
   pola di controller (contoh dari `RoleController`):
   ```php
   public static function middleware(): array
   {
       return [
           new Middleware(PermissionMiddleware::using('<pile> index'), only: ['index']),
           new Middleware(PermissionMiddleware::using('<pile> store'), only: ['store']),
           new Middleware(PermissionMiddleware::using('<pile> edit'), only: ['edit']),
           new Middleware(PermissionMiddleware::using('<pile> show'), only: ['show']),
           new Middleware(PermissionMiddleware::using('<pile> delete'), only: ['destroy']),
       ];
   }
   ```
   `<pile>` = `pasal`, `jenis`, `siswa`, `pelanggaran`, `tindak`, `pengajuan`.
   Tambah permission baru ke `PermissionSeeder`. **Wajib update `permission.md`**
   setiap kali controller baru dibuat/diubah (lihat `permission.md`).
7. Setiap simpan/edit/hapus menulis `AuditLog`. Setiap controller cukup 1 file,
   namespaced `App\Http\Controllers\Dashboard\...`.
8. Route ditaruh di `routes/web.php` dalam grup
   `Route::middleware(['auth','verified'])->prefix('dashboard')->name('dashboard.')->group(...)`
   — resource biasa, middleware permission diletakkan di controller (poin 6),
   JANGAN di route.
9. Setelah tiap controller selesai: `pint`, cek `npm run dev` + `php artisan serve`.
10. Laravel 13 + Inertia 3: **laravel-wayfinder** dipakai (ada folder `resources/js/wayfinder`),
    tapi halaman rujukan memakai `route('nama.route')` via Ziggy — ikuti halaman contoh.
11. **WAJIB fitur SEARCH di setiap halaman index** — pakai komponen
    `TableFilter` yang sudah tersedia (lihat `pages/dashboard/users/index.tsx`).
    Backend di controller `index()`: terima `?search=`, terapkan `scopeSearch()`
    yang sudah ada di model, paginate dengan `->withQueryString().`
    Rincian per-halaman:
    1. Pasal: cari nomor/isi ringkas — scope sudah siap.
    2. Jenis pelanggaran: cari nama/kategori — scope sudah siap.
    3. Siswa: cari nama/NIS (tambah scopeSearch jika belum ada), filter status.
    4. Pelanggaran: cari nama siswa/jenis/pencatat, filter kelas + tanggal.
    5. Tindak lanjut: cari nama siswa + filter status belum/selesai.
    6. Pengajuan: cari nama pengaju + filter status menunggu/disetujui/ditolak.

---

## CHECKLIST: Migrasi prasyarat (hanya 1 tabel yang kurang)

Controller 1–6 butuh tabel yang **sudah semuanya ada**, KECUALI:

- [x] `pengaturan` (kunci, nilai) — ✅ SELESAI: migrasi dibuat & sudah
  `php artisan migrate` sukses; `PengaturanSeeder` (SP 30/60/90) sudah ter-seed.
  Gaya mengikuti `2026_10_26_000004_create_pasal_tatib_table.php`:
  id manual, created_by/updated_by.
- Syarat DB: **MySQL harus nyala dulu** sebelum `php artisan migrate`
  (status sekarang: MySQL sudah nyala).

---

## URUTAN 1 — PasalTatibController (master pasal tatib)

**Fungsi controller:** kelola daftar pasal dalam buku tata tertib (nomor pasal +
isi ringkas + status aktif). Inilah "kamus pasal" yang dipakai otomatis oleh
master jenis pelanggaran (urutan 2) dan ditampilkan di rekap/dashboard.

- Controller: `app/Http/Controllers/Dashboard/PasalTatibController.php`
  - `index()`  — daftar pasal (paginasi + filter, seperti UserController@index)
  - `create()` / `store(PasalTatibRequest)` — tambah pasal (slug otomatis via sluggable)
  - `edit()`  / `update(PasalTatibRequest)` — ubah pasal (nomor tetap unik)
  - `destroy()` — nonaktifkan/hapus; tulis audit log
- Transaksi: tidak wajib (operasi tunggal), tapi tulis audit log tetap.
- Pages (`resources/js/pages/dashboard/tatib/pasal/`): `index.tsx`, `create.tsx`, `edit.tsx`
  — komponen: `Header`, `TableCard`, `Table*`, `ActionButton`, `Pagination`,
  hapus pakai `confirmDelete` dari `@/utils/sweetAlert`.
- Route: `Route::resource('tatib/pasal', PasalTatibController::class)` + middleware permission.
- Types: sudah ada `resources/js/types/pasal-tatib.ts` — tinggal pakai.

## URUTAN 2 — JenisPelanggaranController (master jenis + pasal)

**Fungsi controller:** kelola daftar jenis pelanggaran (nama, kategori
terlambat/umum, poin) dan hubungkan ke 1..N pasal. Ngoper poin + pasal otomatis
di form input (urutan 4) diambil dari master ini.

- 🔒 `DB::transaction` di `store()`/`update()`:
  ```php
  DB::transaction(function () use ($request) {
      $jenis = JenisPelanggaran::create([...]);   // 1. jenis dibuat
      $jenis->pasalTatib()->sync($request->pasal_ids); // 2. pivot pasal
      // gagal di step 2 → jenis ikut dibatalkan (rollback)
  });
  ```
- Pages: `dashboard/tatib/jenis-pelanggaran/{index,create,edit}.tsx`
  (form multiselect pasal, badge poin).
- Types: sudah ada `jenis-pelanggaran.ts`, `jenis-pelanggaran-pasal.ts`.
- Route: `Route::resource('tatib/jenis-pelanggaran', ...)`.

## URUTAN 3 — SiswaController (+ RiwayatKelasController)

**Fungsi controller:** kelola data siswa (nama, NIS, status aktif/alumni/pindah)
dan kelas tiap tahun ajaran (`riwayat_kelas`, unik per siswa+TA). Data ini
yang dipilih di form input pelanggaran (urutan 4), kelasnya diambil otomatis.

- 🔒 `DB::transaction` pada import/update:
  ```php
  DB::transaction(function () use ($rows, $ta) {
      foreach ($rows as $r) {
          $siswa = Siswa::updateOrCreate(['nis' => $r->nis], [...]); // NIS ada → update
          RiwayatKelas::updateOrCreate(
              ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $ta->id],
              ['kelas' => $r->kelas]                                    // unik, tidak dobel
          );
      }
  });
  ```
- Pages: `dashboard/siswa/{index,create,edit}.tsx`.
- Types: sudah ada `siswa.ts`, `riwayat-kelas.ts`, `tahun-ajaran.ts`.
- Route: `Route::resource('siswa', ...)` (SA/AD/KS/BK; kelola kelas via sub-route atau inline).

## URUTAN 4 — KasusController + PelanggaranController (INTI)

**Fungsi controller:** 
- `KasusController`: membungkus satu kejadian pelanggaran (keterangan, foto
  bukti, pencatat otomatis = user login, tahun ajaran aktif, waktu otomatis).
  Satu kasus bisa melibatkan banyak siswa.
- `PelanggaranController`: baris pelanggaran per siswa di dalam kasus
  (salinan nama/poin/kelas/pasal supaya master berubah tak merusak riwayat),
  daftar semua input, dan aksi "Ajukan Perubahan" (menyerahkan ke urutan 6).

- 🔒 **DB::transaction WAJIB di `store()` (titik paling kritis)**:
  ```php
  DB::transaction(function () use ($request) {
      $kasus = Kasus::create([...]);                          // 1
      foreach ($request->siswa_ids) {
          Pelanggaran::create([...salinan dari master...]);   // 2 (N baris)
      }
      $total = hitungTotalPoin($siswa);                       // 3 (SUM query)
      $level = levelSp($total, Pengaturan::ambangSp());       // 3b 30/60/90
      if ($level) {
          SpSiswa::firstOrCreate([...level...]);              // 4 (SP maksimal 3, tidak naik)
          Notifikasi::create([kesiswaan]); Notifikasi::create([bk]); // 5
      }
      AuditLog::create(aksi:'input', tabel:'kasus', ...);     // 6
  });
  ```
  Gagal mana pun → semua rollback: tidak ada kasus bocor / pelanggaran tanpa SP.
- Tabel `pengaturan` ✅ sudah dimigrasi & ter-seed → fitur SP bebas dieksekusi.
- Pages: `dashboard/pelanggaran/{index,create,show}.tsx`
  (TIDAK ADA edit.tsx — pelanggaran tidak diedit langsung; hanya izin SA, jalur resminya lewat pengajuan).
- Types: `kasus.ts`, `pelanggaran.ts`, `sp-siswa.ts`, `tahun-ajaran.ts` — sudah ada.
- Route: `Route::resource('pelanggaran', ...)` (skip edit), dan kasus digabung di belakang layar.

## URUTAN 5 — TindakLanjutController

**Fungsi controller:** pencatatan pembinaan setelah pelanggaran/SP: tindakan
(teguran, panggilan ortu, dsb), tanggal, catatan, status belum/selesai.
Hanya SA/AD/KS/BK (petugas gerbang tidak boleh).

- 🔒 `DB::transaction` di `store()`/`update()`:
  ```php
  DB::transaction(function () use (...) {
      TindakLanjut::updateOrCreate([...]);   // 1 pembinaan
      AuditLog::create(...);                 // 2 jejak
  });
  ```
- Status: belum → selesai, butuh catatan hasil.Dashboard juga pakai ini (daftar status belum).
- Pages: `dashboard/tindak-lanjut/{index,create,edit}.tsx`.
- Types: `tindak-lanjut.ts` — sudah ada.
- Route: `Route::resource('tindak-lanjut', ...)`.

## URUTAN 6 — PengajuanPerubahanController

**Fungsi controller:** koreksi data pelanggaran TANPA edit langsung. Petugas
yang dapat "Ajukan Perubahan" hanya melalui siapa yang urutan 4, mengisi
koreksi + alasan → data lama & baru disimpan → menunggu keputusan SA.
SA menyetujui (data berubah) atau menolak (data tetap) → hasil dikirim lagi
ke pengaju lewat notifikasi.

- 🔒 `DB::transaction WAJIB` di aksi setuju:
  ```php
  DB::transaction(function () use ($pengajuan) {
      $pelanggaran->update($pengajuan->data_baru);   // 1 data berubah
      $pengajuan->update(status: 'disetujui', diputuskan_oleh, catatan); // 2 antrean beres
      Notifikasi::create(ke pengaju);                // 3 hasil ke pengaju
      AuditLog::create('setujui', data_lama, data_baru); // 4 jejak
  });
  ```
- Tolak: pola sama tapi data pelanggaran TIDAK berubah (catatan_keputusan wajib).
- 🔒 Cegah dobel-putus SA: cek status masih `menunggu` dulu
  (query conditional update di atas → baris yang benar-benar terupdate == 1).
- Hanya pengajuan atas data milik sendiri (tabel `pengajuan_perubahan` punya `diajukan_oleh`).
- Pages: `dashboard/pengajuan/{index,show}.tsx` (SA melihat antrean + tombol setuju/tolak).
- Types: `pengajuan-perubahan.ts` — sudah ada.
- Route: `Route::resource('pengajuan', ...)` + custom PUT `disetujui`/`tolak`.

---

## UJI SETIAP URUTAN (sebelum lanjut ke berikutnya)

1. `php artisan migrate --force` (tabel `pengaturan` ✅ sudah dibuat; sisa migrate berikutnya hanya tabel modul lanjutan bila ada)
2. Buka browser di `/dashboard/tatib/pasal` (urutan 1, ✅ sudah dieksekusi) dst.
3. Pengujian transaksi: coba buat jenis pelanggaran tanpa pilih pasal
   (harus tetap lancar), dan saat import siswa macet → pastikan tidak ada baris
   yang "setengah jadi" (semua balik semua / semuanya tersimpan).
4. `php artisan serve` + `npm run dev` jalan bersamaan.
5. Setiap controller baru → ikuti CHECKLIST di `permission.md`.

## STATUS EKSEKUSI SAAT INI
- [x] Migrasi `pengaturan` + seeder ambang SP — SELESAI
- [x] Urutan 1 PasalTatibController (controller + routes + pages types.ts + search + audit log + sidebar) — SELESAI
- [x] Urutan 2 JenisPelanggaranController (transaksi + sync pivot + pages) — SELESAI
- [x] Urutan 3 SiswaController (transaksi siswa + riwayat kelas + pages) — SELESAI
- [x] Urutan 4 PelanggaranController (transaksi Kasus+N pelanggaran, SP, notifikasi, audit; KasusController terpisah tidak dibuat) — SELESAI
- [x] Urutan 5 TindakLanjutController (transaksi + audit + scope search) — SELESAI
- [x] Urutan 6 PengajuanPerubahanController (store + putuskan transaksi, guard status menunggu, notifikasi, audit + pages index/show) — SELESAI
- [x] Routes pelanggaran/tindak-lanjut/pengajuan + entri sidebar — SELESAI
- [x] RoleSeeder: syncPermissions per role (SA semua; admin/kesiswaan/bk input+tindak+pengajuan; petugas_gerbang input+pengajuan) — SELESAI
- [x] Tambahan: CRUD Riwayat Kelas terpisah (controller/routes/pages/sidebar) — SELESAI
- [x] Fix hapus siswa: hapus riwayat kelas dulu dalam transaksi; jika masih punya pelanggaran/SP/tindak → error ramah (FK restrict) — SELESAI
- [x] Fix `PasalTatibRequest`: route param `pasal` (bukan `pasal_tatib`) + null-safe unique ignore — SELESAI
- [x] Share `flash.success/error` di HandleInertiaRequests agar toast delete/validasi tampil — SELESAI

## YANG TIDAK DIBUAT (tar)
- Urutan 7–10 dari controller.md (notifikasi UI, dashboard, 3K, razia,
  export/backup/tahun ajaran).
- Semua modul 3K & razia & peringkat kelas.
