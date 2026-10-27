# RELASI

Rekapitulasi Pelanggaran Siswa SACIL. Kanvas perancangan: topologi, hak akses, alur, database, dan catatan. Geser untuk berpindah, scroll atau cubit untuk zoom.

## 1. Topologi sistem

Aplikasi web internal sekolah. Usulan: Laravel + MySQL di satu server.

**Petugas gerbang**input, dashboard

**Kesiswaan dan BK**input, tindak lanjut

**Admin**input, tindak lanjut

**Super admin**kendali penuh

**Jaringan sekolah**browser PC atau HP, HTTPS

**Nginx**HTTPS, pintu masuk

**Aplikasi RELASI**Laravel, cek peran tiap akses

**Database MySQL**21 tabel, hanya dari aplikasi

**Penyimpanan file**foto bukti, PDF tatib, export

**Penjadwal**backup bulanan, notifikasi

**Backup**terpisah dari server utama

**Catatan topologi**

- Semua akses lewat HTTPS dan wajib login.
- Hanya internal sekolah, bisa dibatasi lewat jaringan sekolah atau VPN.
- Database tidak terbuka langsung ke pengguna.
- Backup disimpan terpisah, minimal 3 terakhir.
- PWA dengan notifikasi push ke HP, butuh HTTPS.

## 2. Hak akses

SA super admin, AD admin, KS kesiswaan, BK, PG petugas gerbang.

| Fitur | SA | AD | KS | BK | PG |
| --- | --- | --- | --- | --- | --- |
| Input pelanggaran | ya | ya | ya | ya | ya |
| Lihat daftar input semua petugas | ya | ya | ya | ya | ya |
| Dashboard | ya | ya | ya | ya | ya |
| Rekap lengkap | ya | ya | ya | ya | ya |
| Catat tindak lanjut, ubah status | ya | ya | ya | ya | tidak |
| Ajukan perubahan (input sendiri) | langsung | ya | ya | ya | ya |
| Setujui, edit, hapus data | ya | tidak | tidak | tidak | tidak |
| Cetak dan export PDF, Excel | ya | ya | ya | ya | ya |
| Upload data siswa dan tatib | ya | tidak | tidak | tidak | tidak |
| Master pelanggaran, poin, pasal | ya | tidak | tidak | tidak | tidak |
| Kelola akun | ya | tidak | tidak | tidak | tidak |
| Audit log (lihat, export) | ya | tidak | tidak | tidak | tidak |
| Backup | ya | tidak | tidak | tidak | tidak |
| Ganti tahun ajaran, kenaikan kelas | ya | tidak | tidak | tidak | tidak |
| Input nilai 3K (penilai terpilih) | ya | izin | izin | izin | izin |
| Mulai, akhiri, atur aspek 3K | ya | tidak | tidak | tidak | tidak |
| Lihat peringkat kelas dan log kelas | ya | ya | ya | ya | ya |
| Catat log barang razia | ya | ya | ya | ya | ya |
| Ubah status barang jadi Dikembalikan | ya | ya | ya | ya | ya |

**Aturan pengaman**

- Pencatat selalu akun yang login, tidak bisa input atas nama orang lain.
- Pengajuan perubahan hanya untuk input milik sendiri. Salah input orang lain dilapor ke Kesiswaan.
- Export boleh memuat tindak lanjut untuk semua peran.
- Setiap PDF dan Excel diberi footer: Diunduh oleh \[nama\], \[tanggal-jam\].
- Setiap export dan cetak masuk audit log.
- Password disimpan sebagai hash.

**Pembagian peran**

- Super admin: keaslian data, poin, master, akun, tahun ajaran.
- Admin: operasional harian, tanpa edit atau hapus.
- Kesiswaan dan BK: pembinaan dan tindak lanjut.
- Petugas gerbang: input dan melihat.

## 3. Alur input pelanggaran sampai SP

Dari login sampai notifikasi SP. Semua pop-up memakai SweetAlert.

**Login**username, password. Nama jadi pencatat

**Halaman input**semua peran boleh membuka

**Cari siswa**nama atau NIS, boleh banyak siswa

**Pilih jenis pelanggaran**ketik untuk menyempitkan daftar

**Ada di daftar?**

**Poin dan pasal otomatis**dari master, bisa lebih dari 1 pasal

**Pilih Lainnya**ditandai pasal belum terpetakan

**Simpan**pencatat, waktu, tahun ajaran, salinan poin dan pasal

**Hitung total poin**siswa, tahun ajaran aktif, tanpa data terhapus

**Lewat 30, 60, 90?**

**SweetAlert sukses**dashboard diperbarui

**Catat SP di sp_siswa**level 1, 2, atau 3

**Notifikasi lonceng**ke Kesiswaan dan BK

**SweetAlert peringatan**siswa baru kena SP

**Catatan input**

- Banyak siswa dalam satu kasus: satu baris kasus, satu baris pelanggaran per siswa.
- Foto bukti dan keterangan opsional.
- Tanggal dan waktu otomatis, tidak bisa diubah petugas.
- Kelas terisi otomatis dari data tahun ajaran aktif.

**Aturan poin dan SP**

- 30 poin SP 1, 60 poin SP 2, 90 poin SP 3 (maksimal).
- Ambang disimpan di tabel pengaturan.
- Total poin dihitung, tidak disimpan sebagai angka.
- SP tidak bisa direset atau diturunkan manual.
- Satu pelanggaran kena banyak pasal tetap satu kali poin.

**Terlambat dan Lainnya**

- Terlambat adalah jenis pelanggaran dengan poin dari Kesiswaan dan BK.
- Di form disatukan, di dashboard dipisah.
- Pilihan Lainnya: Kesiswaan menentukan pasalnya, super admin menambah ke master bila perlu.

**Dashboard setelah simpan**

- Kartu Pelanggaran Terbaru: nama, kelas, pelanggaran, pasal, poin, waktu, pencatat.
- Daftar siswa mendekati atau kena SP.
- Tindak lanjut berstatus Belum.
- Grafik kelas, hari ini, tren, ranking, pasal terbanyak.

## 4. Tindak lanjut

Pembinaan setelah SP muncul.

**Notifikasi SP**muncul di lonceng

**Buka profil siswa**Kesiswaan, BK, atau admin

**Catat tindak lanjut**tindakan, tanggal, catatan

**Status Belum**tampil di dashboard

**Ubah ke Selesai**isi catatan hasil

**Audit log**tercatat dan tampil di rekap

**Contoh tindakan**

- Teguran lisan
- Panggilan orang tua
- Surat pernyataan
- Pembinaan BK
- Skorsing

Tindak lanjut terhubung ke siswa, SP, dan pelanggaran. Petugas gerbang tidak mencatat tindak lanjut.

## 5. Pengajuan perubahan data

Koreksi tanpa mengubah data langsung.

**Temukan kesalahan**di input miliknya sendiri

**Klik Ajukan Perubahan**pada data yang salah

**Isi koreksi dan alasan**

**Masuk antrean**lonceng ke super admin

**Keputusan super admin**

**Setujui**data berubah

**Tolak**data tetap

**Audit log**data lama, baru, pengaju, penyetuju, waktu

**Catatan pengajuan**

- Hanya super admin yang setujui, edit, dan hapus.
- Hapus bersifat soft delete, alasan wajib.
- Alasan penolakan dikirim ke pengaju lewat lonceng.
- Pelanggaran tidak pernah dihapus permanen.

## 6. Pergantian tahun ajaran

Poin dan SP mulai dari 0, data lama jadi arsip.

**Tambah tahun ajaran**super admin

**Upload ulang data siswa**kelas diperbarui berdasarkan NIS

**Tandai alumni**siswa XII yang lulus

**Ganti tahun ajaran aktif**hanya satu yang aktif

**Poin dan SP jadi 0**dihitung per tahun ajaran

**Data lama jadi arsip**bisa difilter

**Audit log**pergantian tercatat

**Catatan arsip**

- Satu database, tiap pelanggaran ditandai tahun ajaran.
- Kelas disimpan per tahun ajaran, arsip tetap menampilkan kelas aslinya.
- NIS sudah ada: kelas diperbarui, tidak dobel.
- Rekap per siswa tetap menampilkan riwayat semua tahun.

## 7. Backup bulanan

File backup berisi data semua siswa.

**Penjadwal server**tiap bulan

**Buat backup**database dan file

**Simpan**server atau drive sekolah

**Notifikasi lonceng**ke super admin

**Super admin unduh**simpan di flashdisk atau drive

**Catatan backup**

- Simpan minimal 3 backup terakhir.
- Jangan kirim sebagai lampiran email.
- Akses hanya super admin.

## 8. Rancangan database

16 tabel inti, plus 5 tabel modul 3K dan razia di bagian 10 dan 11. Garis putus-putus: relasi utama (1 ke banyak).

#### users

- id, nama, username (unik)
- password (hash)
- role: super_admin, admin, kesiswaan, bk, petugas_gerbang
- aktif

#### tahun_ajaran

- id, nama (2026/2027)
- tgl_mulai, tgl_selesai
- aktif (hanya satu)

#### siswa

- id, nis (unik)
- nama
- status: aktif, alumni, pindah

#### riwayat_kelas

- id
- siswa_id (FK)
- tahun_ajaran_id (FK)
- kelas (unik per siswa dan tahun ajaran)

#### pasal_tatib

- id
- nomor (Pasal 7 ayat 2)
- isi_ringkas
- aktif

#### jenis_pelanggaran_pasal

- jenis_pelanggaran_id (FK)
- pasal_id (FK)
- penghubung banyak ke banyak

#### jenis_pelanggaran

- id
- nama
- kategori: terlambat atau umum
- poin
- aktif

#### tatib_dokumen

- id
- nama_file, path
- diunggah_oleh (FK)
- tanggal

#### kasus

- id
- tahun_ajaran_id (FK)
- waktu (otomatis)
- pencatat_id (FK users)
- keterangan
- foto_path

#### pelanggaran

- id
- kasus_id (FK)
- siswa_id (FK)
- tahun_ajaran_id (FK)
- jenis_pelanggaran_id (FK)
- salinan: nama_pelanggaran
- salinan: poin
- salinan: kelas
- salinan: pasal
- deleted_at
- alasan_hapus

#### sp_siswa

- id
- siswa_id (FK)
- tahun_ajaran_id (FK)
- level 1 sampai 3
- poin_saat_tercapai
- tanggal

#### tindak_lanjut

- id
- siswa_id (FK)
- sp_siswa_id (FK, boleh kosong)
- pelanggaran_id (FK, boleh kosong)
- tindakan, tanggal, catatan
- status: belum atau selesai
- ditangani_oleh (FK)

#### pengajuan_perubahan

- id
- pelanggaran_id (FK)
- diajukan_oleh (FK)
- alasan
- data_lama, data_baru
- status: menunggu, disetujui, ditolak
- diputuskan_oleh (FK)
- catatan_keputusan
- waktu

#### notifikasi

- id
- user_id (penerima)
- tipe, isi, tautan
- dibaca_pada

#### audit_log

- id
- user_id
- aksi: login, input, edit, hapus, setujui, export, cetak, ganti_tahun
- tabel, data_id
- data_lama, data_baru
- ip, waktu

#### pengaturan

- kunci
- nilai (misalnya ambang SP 30, 60, 90)

## 9. Keputusan desain, notifikasi, dan persiapan

Catatan yang perlu diingat saat membangun.

**Keputusan desain database**

1. Kelas disimpan per tahun ajaran (riwayat_kelas), bukan di data siswa.
2. Poin, nama pelanggaran, kelas, dan pasal disalin ke pelanggaran saat input, jadi master atau tatib berubah tidak mengubah riwayat.
3. Total poin dihitung, bukan disimpan.
4. Soft delete: hanya isi deleted_at dan alasan.
5. Satu kasus bisa banyak siswa.
6. Pasal banyak ke banyak lewat tabel penghubung.

**Otomasi pasal tatib**

1. Super admin mengisi master: tiap pelanggaran dihubungkan ke satu atau beberapa pasal.
2. Petugas memilih pelanggaran dari daftar.
3. Pasal tampil otomatis di dashboard, rekap, cetakan per siswa, dan notifikasi SP.

Sistem tidak menebak pasal dari teks PDF karena rawan salah. PDF tatib hanya rujukan. Pengisian awal lewat Excel oleh super admin.

**Notifikasi**

- Notifikasi push ke HP (PWA) dan lonceng di aplikasi. Di iPhone, aplikasi harus ditambahkan ke Layar Utama dulu.
- SP baru ke Kesiswaan dan BK.
- Pengingat 3K harian ke super admin dan penilai.
- Pengajuan baru ke super admin.
- Hasil keputusan ke pengaju.
- Backup siap diunduh ke super admin.
- SweetAlert untuk semua pop-up: sukses, konfirmasi, peringatan SP.

**Keamanan dan pemeliharaan**

- Password hash, akun bisa dinonaktifkan.
- HTTPS dan kontrol akses per peran.
- Audit log: login, input, edit, hapus, persetujuan, export, cetak, ganti tahun ajaran.
- Data siswa sensitif, export hanya untuk keperluan sekolah.

**Persiapan sebelum membangun**

1. Buku tatib dalam bentuk file, disusun jadi daftar pelanggaran, poin, dan pasal.
2. Besaran poin terlambat dari Kesiswaan dan BK.
3. Daftar tindak lanjut baku per tingkat SP.
4. Data siswa awal (nama, NIS, kelas) dalam Excel.
5. Penetapan super admin dan admin.
6. Tempat penyimpanan backup.

**Tahapan pembangunan**

1. Database, migrasi, akun, peran.
2. Upload siswa, master pelanggaran, pasal.
3. Input, perhitungan poin, SP.
4. Dashboard, rekap, export, cetak.
5. Tindak lanjut, pengajuan perubahan, notifikasi.
6. Audit log, backup, arsip tahun ajaran.
7. Uji coba dengan petugas lalu peluncuran.

**Fitur Admin (super admin)**

- Upload dan upload ulang data siswa, upload PDF tatib.
- Buat user, aktif atau nonaktif.
- Master pelanggaran, poin, pasal.
- Antrean pengajuan, audit log (bisa export), backup, ganti tahun ajaran.

## 10. Lomba 3K antar kelas

Kebersihan, kerapihan, ketertiban. Tiap kelas mendapat 3 nilai. Periode lomba tidak menentu, dibuka dan ditutup super admin.

**Mulai lomba 3K**super admin membuat sesi

**Pengingat harian**push ke HP super admin dan penilai

**Penilai input**kebersihan dan kerapihan, 1 nilai per kelas

**Ketertiban otomatis**poin pelanggaran kelas dibagi jumlah siswa

**Skor akhir kelas**3 nilai, bobot sama rata

**Peringkat lomba**tampil di dashboard

**Akhiri lomba**nilai terkunci, jadi arsip

**Aturan skor 3K**

- Tiap kelas mendapat 3 nilai sekaligus: ketertiban, kebersihan, kerapihan.
- Ketertiban tidak diinput. Diambil dari total poin pelanggaran kelas dalam rentang sesi, dibagi jumlah siswa kelas itu.
- Kebersihan dan kerapihan diinput penilai, 1 nilai per kelas per sesi. Cara memeriksa bebas.
- Skor akhir: tiga nilai sama rata, tanpa pengaturan bobot.
- Aspek dan skala nilai diatur super admin sebelum sesi dimulai.

**Sesi dan pengingat**

- Super admin menekan Mulai lomba dan Akhiri lomba.
- Selama sesi aktif, pengingat harian: Sekarang waktunya 3K, jangan lupa input nilai.
- Isi pesan dan jam pengingat bisa dikustom.
- Notifikasi push muncul di HP seperti aplikasi biasa (PWA), tidak perlu membuka aplikasi.
- Pengingat berhenti saat sesi diakhiri.

**Pengaman anti-curang**

- Bobot tetap sama rata, tidak bisa diubah.
- Rumus skor ditampilkan terbuka di halaman lomba.
- Nilai 3K terkunci setelah sesi diakhiri.
- Setiap input dan perubahan nilai masuk audit log.
- Audit log hanya super admin. Backup bulanan memuat audit log sebagai pembanding.

**Hak akses 3K**

- Input nilai: akun penilai (orang tertentu), diberi izin oleh super admin. Hak dasarnya seperti petugas gerbang.
- Mulai, akhiri, aspek, skala, pengingat: super admin.
- Lihat peringkat lomba: semua peran.

#### sesi_3k

- id, nama sesi
- tgl_mulai, tgl_akhir
- status: aktif atau selesai
- dibuat_oleh (FK)
- terkunci_pada

#### aspek_3k

- id
- nama (kebersihan, kerapihan)
- skala_min, skala_maks
- aktif

#### nilai_3k

- id
- sesi_3k_id (FK)
- kelas
- aspek_3k_id (FK)
- nilai
- penilai_id (FK)
- tanggal, catatan
- unik per sesi, kelas, aspek

#### pengingat_3k

- id
- sesi_3k_id (FK)
- jam
- isi_pesan (bisa dikustom)
- aktif

## 11. Razia barang

Razia bersifat spontan. Log barang tersimpan, temuan sekaligus jadi pelanggaran.

**Razia spontan**siapa saja yang bertugas

**Catat barang**nama barang, pemilik (siswa), foto

**Simpan log**tanggal dan pencatat otomatis

**Status: Disita**awal setiap barang

**Pelanggaran otomatis**poin dan pasal dari master

**Dimusnahkan**status akhir

**Dikembalikan**semua peran, foto bukti wajib, nama pengambil

**Audit log**setiap perubahan status tercatat

**Catatan razia**

- Pemilik dipilih dari siswa (nama atau NIS), kelas terisi otomatis.
- Temuan jadi baris pelanggaran sehingga ikut poin dan SP.
- Foto bukti pengambilan dan nama pengambil wajib saat Dikembalikan.
- Hak input log razia: sementara sama seperti input pelanggaran.

#### barang_razia

- id
- nama_barang
- siswa_id (FK)
- kelas (salinan)
- tanggal (otomatis)
- dicatat_oleh (FK)
- pelanggaran_id (FK, boleh kosong)
- foto_barang
- status: disita, dikembalikan, dimusnahkan
- tgl_kembali, diambil_oleh
- foto_bukti_kembali

## 12. Peringkat kelas

Semua log pelanggaran siswa per kelas, peringkat, dan total poin.

Contoh tampilan. Klik baris kelas untuk membuka log. Urutkan: Abjad, Paling sering melanggar (rata-rata per siswa), Paling sering melanggar (total poin).

| Peringkat | Kelas | Jumlah siswa | Total poin | Rata-rata per siswa |
| --- | --- | --- | --- | --- |
| 1 | Kelas A | ... | ... | ... |
| 2 | Kelas B | ... | ... | ... |
| 3 | Kelas C | ... | ... | ... |

Contoh tampilan log kelas yang dipilih, berisi seluruh pelanggaran semua siswa kelas itu.

| Tanggal | Siswa | Pelanggaran | Pasal | Poin | Pencatat |
| --- | --- | --- | --- | --- | --- |
| ... | nama siswa | jenis pelanggaran | pasal | ... | nama pencatat |
| ... | nama siswa | jenis pelanggaran | pasal | ... | nama pencatat |

**Catatan peringkat kelas**

- Bisa diurutkan sesuai abjad atau berdasarkan yang paling sering melanggar (rata-rata per siswa atau total poin). Total poin selalu tampil.
- Filter: tahun ajaran atau sesi lomba 3K. Export PDF dan Excel.
- Bisa dilihat semua peran, sama seperti rekap.

100%

Aksi penggunaProses sistemPenyimpananKeputusan
