# Permission.md — Daftar Permission Spatie per Controller

**ATURAN KERJA (WAJIB):**
1. Setiap kali AI membuat/merubah sebuah controller, AI WAJIB meng-update file
   ini: tambah/ubah daftar permission sesuai nama yang dipakai di
   `public static function middleware(): array` controller itu.
2. JANGAN mengubah konfigurasi middleware apapun (bootstrap, config, alias,
   `app/Http/Kernel`). Keamanan hanya lewat `PermissionMiddleware` di controller:
   ```php
   use Illuminate\Routing\Controllers\Middleware;
   use Spatie\Permission\Middleware\PermissionMiddleware;

   public static function middleware(): array
   {
       return [
           new Middleware(PermissionMiddleware::using('pasal index'), only: ['index']),
           new Middleware(PermissionMiddleware::using('pasal store'), only: ['store']),
           new Middleware(PermissionMiddleware::using('pasal edit'), only: ['edit']),
           new Middleware(PermissionMiddleware::using('pasal show'), only: ['show']),
           new Middleware(PermissionMiddleware::using('pasal delete'), only: ['destroy']),
       ];
   }
   ```
3. Nama permission = `<pile> index|store|edit|show|delete` (huruf kecil, tanpa titik),
   sama dengan pola `permissions index`, `roles index`, `users index` yang sudah ada.
4. Daftar permission baru WAJIB ditambahkan juga ke
   `database/seeders/PermissionSeeder.php` (pola `Permission::firstOrCreate`).
5. Jangan lupa: role punya permission lewat `PermissionSeeder` + `RoleSeeder`.

---

## STATUS PERMISSION (diperbarui tiap controller selesai)

### Sudah ada di PermissionSeeder (template starter)
- [x] `permissions index|store|edit|show|delete` — PermissionController
- [x] `roles index|store|edit|show|delete` — RoleController
- [x] `users index|store|edit|show|delete` — UserController

### Urutan 1 — PasalTatibController (index/store/edit/show/delete)
- [x] `pasal index` ✅ seeder
- [x] `pasal store` ✅ seeder
- [x] `pasal edit` ✅ seeder
- [x] `pasal show` ✅ seeder
- [x] `pasal delete` ✅ seeder

### Urutan 2 — JenisPelanggaranController
- [x] `jenis index|store|edit|show|delete` ✅ seeder (asal dilihat dari PermissionSeeder)

### Urutan 3 — SiswaController
- [x] `siswa index|store|edit|show|delete` ✅ seeder

### Urutan 4 — KasusController + PelanggaranController
- [x] `pelanggaran index|store|show` ✅ seeder
- [ ] Catatan: TIDAK ada `pelanggaran edit`/`delete` — pelanggaran tidak bisa diedit
  langsung (jalur resmi lewat pengajuan perubahan); SA pun lewat antrean pengajuan.

### Urutan 5 — TindakLanjutController
- [x] `tindak index|store|edit|delete` ✅ seeder

### Urutan 6 — PengajuanPerubahanController
- [x] `pengajuan index|store|show` ✅ seeder
- [x] `pengajuan putuskan` ✅ seeder — SA menyetujui/menolak

### Tambahan — RiwayatKelasController (CRUD terpisah)
- [x] `riwayat index|store|edit|delete` ✅ seeder — hanya SA (selaras permission master siswa)
- [x] Entry CRUD dari sidebar "Riwayat Kelas" + tombol di detail siswa.

### Pemetaan Role (RoleSeeder — syncPermissions)
- [x] `super-admin` → semua permission
- [x] `admin`, `kesiswaan`, `bk` → `pelanggaran index|store|show`, `tindak index|store|edit|delete`, `pengajuan index|store|show`
- [x] `petugas_gerbang` → `pelanggaran index|store|show`, `pengajuan index|store|show`
- [x] Hanya `super-admin` yang punya `pengajuan putuskan` (dan seluruh permission master/akun)

---

## CHECKLIST SAAT MEMBUAT CONTROLLER BARU
1. [ ] Tambah `public static function middleware(): array` di controller dengan
       nama permission sesuai fiturnya — JANGAN ubah middleware global.
2. [ ] Perbarui file `permission.md` ini (tambah di daftar sesuai urutan).
3. [ ] Tambah permission di `database/seeders/PermissionSeeder.php`.
4. [ ] Jalankan `php artisan db:seed --class=PermissionSeeder` (atau `migrate:fresh --seed`).
5. [ ] Role yang berkaitan diberi permission di `RoleSeeder` (juga di sesuaikan).
