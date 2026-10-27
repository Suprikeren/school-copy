# Controller.md — Urutan pengerjaan & rancangan DB

Sumber desain: `text.md` + `Plan.md`.

## 1. Database: SATU database, semua tabel digabung di `laravel_school`

Tidak ada pemisahan DB per modul. Semua 21 tabel hidup di satu MySQL database
`laravel_school` karena:

- Semua fitur butuh data saling sambung (pelanggaran ↔ sp ↔ tindak_lanjut ↔ razia ↔ 3K).
- Total poin siswa harus hitung lintas modul (razia ikut poin pelanggaran).
- Peringkat kelas + arsip tahun ajaran = query lintas tabel.

Pengelompokan tabel di dalam DB itu (untuk memudahkan kerja):

| Kelompok | Tabel | Dipakai controller |
|---|---|---|
| (a) Sistem & dasar | users, permission*, tahun_ajaran, pengaturan | semua |
| (b) Master | siswa, riwayat_kelas, pasal_tatib, jenis_pelanggaran, jenis_pelanggaran_pasal, tatib_dokumen | 1–3 |
| (c) Alur inti | kasus, pelanggaran, sp_siswa, tindak_lanjut | 4–5 |
| (d) Koreksi & log | pengajuan_perubahan, audit_log, notifikasi | 6–7 |
| (e) Modul 3K | sesi_3k, aspek_3k, nilai_3k, pengingat_3k | urutan 9 |
| (f) Razia | barang_razia | urutan 9 |

> Catatan: tabel kelompok (a–d) **migrasinya sudah ada**, kecuali `pengaturan`
> (kunci/nilai, seeder-nya sudah ada tapi tabel belum). Kelompok (e) dan (f)
> migrasinya belum dibuat — dibuat saat masuk urutan 9.

## 2. Urutan pengerjaan controller

Semua controller di `App\Http\Controllers\Dashboard`, pola mengikuti
`Dashboard\Spatie\UserController`: index → create/store → edit/update → destroy,
FormRequest wiring, paginasi + search, `inertia('dashboard/...')`, semua di dalam
`Route::middleware(['auth','verified'])->prefix('dashboard')->name('dashboard.')`.

### Urutan 1 — PasalTatibController (kerjakan duluan)
- Master pasal, tanpa dependensi lain. Yg sudah siap: model `PasalTatib`,
  FormRequest `PasalTatibRequest` (validasi unik nomor + pesan Indonesia).
- Needs: slug otomatis (paket sluggable sudah terpasang).
- Pages: `dashboard/tatib/pasal/{index,create,edit}` (4 file).
- Route: `Route::resource('tatib/pasal', ...)` + middleware `permission:pasal index` dsb.

### Urutan 2 — JenisPelanggaranController
- Master jenis pelanggaran: nama, kategori (terlambat/umum), poin, pilih pasal (multi dari urutan 1).
- Model + FormRequest sudah siap; hubungan pivot `jenis_pelanggaran_pasal` sudah di model `PasalTatib`.
- 🔒 **DB::transaction di store/update**: `create/update jenis_pelanggaran` + `sync()` pivot
  pasal harus satu transaksi — kalau pivot gagal, jenis dengan poin tanpa pasal itu bocor.
- Pages: `dashboard/tatib/jenis-pelanggaran/{index,create,edit}`.

### Urutan 3 — SiswaController (+ RiwayatKelasController)
- Data siswa & kelas per tahun ajaran aktif. Login pencatat tidak masuk di sini
  (ini kegiatan daftar siswa yang jadi target input di urutan 4).
- FormRequest `SiswaRequest`, `RiwayatKelasRequest` sudah siap.
- Tulis juga fitur cek yang butuh import Excel supaya siap dipakai di form input.
- 🔒 **DB::transaction pada import/update siswa**: upsert per NIS (`updateOrCreate`) +
  `riwayat_kelas` (unik per siswa+TA) harus atomic; gagal di tengah import = kelas dobel/bocor.
- Pages: `dashboard/siswa/{index,create,edit}`.

### Urutan 4 — KasusController + PelanggaranController (INTI)
- Satu form input pelanggaran bisa pilih banyak siswa:
  - 1 baris `kasus` (keterangan, foto, pencatat = user login, tahun ajaran aktif).
  - 1 baris `pelanggaran` per siswa (salin nama/poin/kelas/pasal dari master).
- Simpan → hitung total poin siswa (dihitung saat itu, tanpa data yang di-softdelete)
  → jika ≥ ambang 30/60/90 (dari tabel `pengaturan`) → buat `sp_siswa` level 1/2/3
  (SP maksimal, tidak naik level) → notif ke Kesiswaan & BK → SweetAlert.
- 🔒 **DB::transaction WAJIB di sini (titik paling kritis)**:
  ```
  DB::transaction(function () use (...) {
      $kasus = Kasus::create(...);              // 1
      foreach ($siswa) {
          Pelanggaran::createSalinan($kasus, ...); // 2 (N baris)
      }
      TotalPoinService::hitung(...)             // 3 (query SUM)
      if (melewati ambang) {
          SpSiswa::firstOrCreate(level tsb)     // 4
          Notifikasi::create([kesiswaan, bk])   // 5
      }
      AuditLog::create('input', 'kasus', ...)   // 6
  });
  ```
  Gagal di tengah = kasus tanpa pelanggaran, atau pelanggaran tanpa SP. Semua balik (rollback).
- Tambahkan **lock** saat hitung total poin supaya tidak balapan: hitung di dalam
  `DB::transaction(fn() => ... )` + `lockForUpdate()` pada baris `sp_siswa` siswa itu,
  tapi jangan dobel-SP: cek `where('level', $levelBaru)->exists()` dulu.
- FormRequest `KasusRequest`, `PelanggaranRequest`, `SpSiswaRequest` sudah siap.
- **Blokir** untuk eksekusi: tabel `pengaturan` belum ada (migrasi ditunda).
  Controller bisa ditulis tapi fitur SP harus ditambah setelah migrasi.
- Pages: `dashboard/pelanggaran/{index,create,show}` (bukan edit: pelanggaran
  cuma bisa ajukan perubahan, bukan diedit langsung - kecuali oleh SA).

### Urutan 5 — TindakLanjutController
- Catat pembinaan (tindakan, tanggal, catatan), status belum/selesai.
- Hanya SA/AD/KS/BK (PG tidak boleh).
- 🔒 **DB::transaction di store/update**: insert/update tindak_lanjut + audit log
  satu transaksi (kalau audit gagal, pembinaan jadi tidak tercatat jejaknya).
- Pages: `dashboard/tindak-lanjut/{index,create,edit}`.

### Urutan 6 — PengajuanPerubahanController
- Tombol "Ajukan Perubahan" di index pelanggaran (data milik sendiri):
  koreksi + alasan → antrean SA → setuju (data berubah) / tolak (data tetap) →
  hasil dikirim ke pengaju lewat notifikasi.
- 🔒 **DB::transaction WAJIB di aksi setuju** yang paling sensitif:
  ```
  DB::transaction(function () {
      $pelanggaran->update($dataBaru);        // 1 data benar-benar berubah
      $pengajuan->update(status: disetujui)   // 2 antrean beres
      Notifikasi::create(ke pengaju)          // 3 hasil ke pengaju
      AuditLog::create('setujui', data_lama, data_baru)  // 4 jejak
  });
  ```
  Tolak: sama polanya (data tetap, catatan keputusan + notif + audit log).
- 🔒 Cegah dobel-putus SA: cek `status == menunggu` dulu
  (`where('status', 'menunggu')->where('id', $id)->update(...)` baris-affected==0 → tolak diam-diam).
- Pages: `dashboard/pengajuan/{index,show}` + aksi setuju/tolak SA.

### Urutan 7 — NotifikasiController + AuditLogController
- Notifikasi: list, tandai dibaca, badge jumlah belum dibaca (untuk lonceng).
- AuditLog: halaman view + filter + export (semua aksi di modul lain wajib menulis ke sini).
- Pages: `dashboard/notifikasi/index`, `dashboard/audit-log/index`.

### Urutan 8 — DashboardController (isi konten)
- Kartu pelanggaran terbaru (nama, kelas, pelanggaran, pasal, poin, waktu, pencatat).
- Daftar siswa mendekati / kena SP.
- Grafik per kelas, hari ini, tren, ranking, pasal terbanyak.

### Urutan 9 — Modul 3K + Razia (5 controller, butuh migrasi tabel dibuat dulu)
- **SesiTigaKController**: mulai/akhiri sesi, atur aspek & skala (SA).
- **NilaiTigaKController**: penilai (izin khusus) input kebersihan & kerapihan.
  Ketertiban otomatis = total poin pelanggaran kelas ÷ jumlah siswa.
- **SesiTigaK** menghitung skor akhir: rata 3 nilai, sama rata, tanpa bobot.
- **PengingatTigaKController**: jam & pesan kustom; berhenti saat sesi diakhiri.
- **BarangRaziaController**: catat barang (siswa & kelas salinan, foto),
  temuan jadi baris `pelanggaran` otomatis, status disita → dikembalikan
  (wajib foto bukti + nama pengambil, semua peran) / dimusnahkan.

### Urutan 10 — Pelengkap
- **ExportController**: PDF/Excel rekap lengkap & per siswa & per kelas,
  footer "Diunduh oleh [nama], [tanggal-jam]", tiap export masuk audit log.
- **BackupController**: backup bulanan (scheduler), simpan ≥3 terakhir, lonceng ke SA.
- **TahunAjaranController**: tambah TA, upload ulang siswa (update kelas by NIS,
  tidak dobel), tandai alumni XII, aktifkan hanya 1 TA, poin & SP reset per TA.

## 3. DB transaction (ringkasan saat sudah selesai semua)

| Titik | Transaksi? | Kenapa |
|---|---|---|
| Urutan 1 (pasal) standalone | 🔒 TIDAK wajib | gampang, kalau gagal tinggal insert kembali |
| Urutan 2 (jenis + pivot pasal) | 🔒 WAJIB | jenis + pasal harus selalu komplit |
| Urutan 3 (siswa + riwayat_kelas import) | 🔒 WAJIB | kelas dobel / siswa setengah |
| Urutan 4 (kasus + pelanggaran + SP + notif) | 🔒 **WAJIB BANGET** | titik paling dalam: 6 step saling ketergan[ga]n; gagal di tengah = kasus bocor |
| Urutan 5 (tindak lanjut) | 🔒 WAJIB | tindakan + audit log setimbang |
| Urutan 6 (setuju/tolak pengajuan) | 🔒 **WAJIB** | data siswa berubah + antrean + notif + audit log semua harus atomik |
| Urutan 1 & 8 (pasal, dashboard) | ❌ tidak perlu | read-only / 1 step saja |

## 4. Migrasi yang DIBUTUHKAN untuk urutan 1–6 (diambil dari setiap controller)

Tabel yang sudah aman untuk controller 1–6: `users, tahun_ajaran, siswa, riwayat_kelas,
pasal_tatib, jenis_pelanggaran, jenis_pelanggaran_pasal, kasus, pelanggaran, sp_siswa,
tindak_lanjut, pengajuan_perubahan, notifikasi, audit_log`.

Yang **belum ada** (wajib dibuat sebelum controller 4 jalan):

```php
// 2026_10_27_000001_create_pengaturan_table.php
Schema::create('pengaturan', function (Blueprint $table) {
    $table->unsignedBigInteger('id');
    $table->primary('id');
    $table->string('kunci')->unique();
    $table->string('nilai');
    $table->string('created_by')->nullable();
    $table->string('updated_by')->nullable();
    $table->timestamps();
});
```
+ Seeder `PengaturanSeeder` (sudah ada, sp_1/2/3 = 30/60/90) sudah menunggu tabel ini.
`php artisan migrate` baru bisa jalan setelah MySQL nyala.

## 5. Catatan pengerjaan
- MySQL komputer belum nyala → `php artisan migrate` gagal; nyalakan MySQL dulu
  (XAMPP/Laragon) saat mulai eksekusi urutan apa pun yang butuh DB.
- Tiap controller selesai: `pint` (format Laravel) + cek di browser via `npm run dev`
  + `php artisan serve`.
- Jangan lupa: setiap simpan/edit/hapus pasang `AuditLog::create(...)` terutama
  di urutan 4–7.
