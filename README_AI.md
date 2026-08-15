# README_AI.md

# e-ARSIP KAI DIVRE III
## AI Development Guide

Dokumen ini adalah panduan utama untuk AI/Coding Agent yang mengembangkan project
**e-ARSIP KAI DIVRE III (Web Pengarsipan Digital)**.

AI WAJIB membaca dokumen ini sebelum melakukan perubahan atau menambahkan fitur.

---

# 1. IDENTITAS PROJECT

Nama sistem:
**e-ARSIP KAI DIVRE III**

Jenis aplikasi:
**Web Pengarsipan Digital**

Teknologi project saat ini:
- PHP
- MySQL
- XAMPP
- HTML
- CSS
- JavaScript

Project yang sudah tersedia memiliki sistem:
- Login
- Register
- Reset Password
- Logout
- Authentication
- Database connection
- Role menggunakan `role_id`

---

# 2. KONDISI PROJECT SAAT INI

Sistem login dan authentication **SUDAH BERJALAN**.

`role_id` **SUDAH BERFUNGSI** dengan baik pada web dan database MySQL melalui XAMPP.

## ATURAN PENTING

JANGAN merusak sistem authentication yang sudah berjalan.

JANGAN mengubah mekanisme `role_id` tanpa alasan teknis yang jelas.

JANGAN mengganti struktur login hanya karena ingin menyesuaikan desain UI.

Jika membutuhkan perubahan authentication:
1. Periksa kode yang sudah ada.
2. Pertahankan kompatibilitas dengan sistem lama.
3. Jangan menghapus fungsi yang sudah berjalan.
4. Pastikan login tetap berfungsi setelah perubahan.

---

# 3. SUMBER ACUAN PROJECT

Pengembangan sistem WAJIB mengacu pada:

1. Flowchart sistem
2. ERD/IRD/database design
3. Pembagian tugas dan hak akses role
4. Desain UI/UX dari Google Stitch
5. Code prototype dari Google Stitch
6. Source code project PHP yang sudah tersedia

Prioritas:

**FLOWCHART + ERD**
↓
**ATURAN ROLE**
↓
**DESAIN GOOGLE STITCH**
↓
**IMPLEMENTASI PHP**

Jangan membuat fitur yang bertentangan dengan flowchart atau ERD.

---

# 4. ATURAN DATABASE

Database yang sudah ada harus dipertahankan.

Sebelum membuat:
- tabel
- kolom
- foreign key
- relasi
- query baru

AI WAJIB memeriksa ERD/database project terlebih dahulu.

JANGAN membuat tabel baru hanya karena sebuah fitur terlihat membutuhkan tabel.

Jika struktur database belum jelas:

> STOP dan laporkan kebutuhan tersebut.

Jangan mengarang struktur database.

---

# 5. ROLE SISTEM

Sistem memiliki role utama:

## ROLE 1 — ADMIN / PENGELOLA SISTEM

Admin bertugas mengelola konfigurasi dan data dasar sistem.

Admin memiliki akses untuk:
- Dashboard
- Pengelolaan Pengguna
- Hak Akses
- Unit Kerja
- Ruangan
- Lemari
- Rak
- Jenis Surat
- Klasifikasi Arsip
- Monitoring Sistem
- Log Aktivitas

Admin menjadi fondasi data sistem sebelum proses pengarsipan dilakukan.

---

## ROLE 2 — PETUGAS UNIT

Petugas Unit berhubungan dengan proses pengelolaan arsip.

Fungsi utama:
- Input Arsip
- Upload scan/dokumen
- Arsip Digital
- Pencarian Arsip
- Melihat jadwal pemusnahan
- Mengelola arsip sesuai unit kerja dan hak akses

Petugas tidak boleh mengakses fungsi Admin yang tidak diberikan kepadanya.

---

## ROLE 3 — PIMPINAN

Pimpinan berfokus pada:
- Monitoring
- Rekapitulasi
- Statistik
- Laporan
- Melihat data arsip sesuai hak akses
- Jadwal pemusnahan
- Export laporan

Pimpinan bukan pengelola master data sistem kecuali secara eksplisit ditentukan oleh aturan sistem.

---

# 6. HAK AKSES

Hak akses WAJIB diperiksa berdasarkan `role_id`.

Jangan hanya menyembunyikan menu menggunakan CSS.

Sistem harus melakukan validasi hak akses pada:
- halaman
- proses CRUD
- endpoint/API jika ada
- upload
- delete
- edit
- export
- tindakan sensitif lainnya

Keamanan backend lebih penting daripada sekadar menyembunyikan menu.

---

# 7. DESAIN UI/UX

Desain Google Stitch digunakan sebagai referensi utama tampilan.

Jangan mengubah desain secara sembarangan.

Pertahankan:
- layout
- sidebar
- hierarchy
- card
- spacing
- typography
- ikon
- warna
- responsive behavior
- struktur navigasi

Desain Stitch menggunakan:
- Inter
- Material Symbols
- dashboard berbasis card
- sidebar navigasi
- responsive layout

Code Stitch adalah **PROTOTYPE**, bukan implementasi database final.

---

---

# 7A. REFERENSI DESAIN GOOGLE STITCH

Project menggunakan **Google Stitch** sebagai sumber desain/prototype UI/UX.

PENTING:

Desain Google Stitch berlaku sebagai **design system untuk seluruh aplikasi dan seluruh role**, bukan hanya untuk Admin.

Role utama:
- Admin
- Petugas Unit
- Pimpinan

Setiap role memiliki fungsi, menu, data, dan hak akses yang berbeda, tetapi seluruh aplikasi harus memiliki gaya visual yang konsisten.

## File Referensi Desain

Jika desain setiap role sudah tersedia, gunakan struktur:

```text
design/
├── admin-dashboard.html
├── petugas-dashboard.html
└── pimpinan-dashboard.html
```

Jika saat ini baru tersedia:

```text
design/
└── admin-dashboard.html
```

maka `admin-dashboard.html` digunakan sebagai **referensi design system awal untuk seluruh aplikasi**.

JANGAN menganggap bahwa semua menu atau fitur di `admin-dashboard.html` harus diberikan kepada Petugas Unit atau Pimpinan.

Menu dan fitur setiap role WAJIB mengikuti:
- role
- `role_id`
- hak akses
- flowchart
- ERD
- tugas masing-masing role

## Aturan Membaca Desain

Sebelum mengimplementasikan halaman baru, AI WAJIB:

1. Membaca `README_AI.md`.
2. Mencari dan membaca file desain Google Stitch yang relevan.
3. Membaca file PHP yang akan diubah.
4. Membandingkan desain dengan implementasi yang sudah ada.
5. Mempertahankan authentication dan `role_id`.
6. Mengambil data nyata dari database untuk menggantikan data dummy.
7. Tidak mengubah database hanya untuk menyesuaikan desain.

## Design System Yang Harus Konsisten

Pertahankan konsistensi pada seluruh role untuk:

- typography
- warna
- sidebar/navigation
- header/top app bar
- card
- button
- form
- tabel
- modal
- spacing
- border radius
- ikon
- hover state
- active state
- responsive layout
- hierarchy informasi

Prototype menggunakan:
- **Inter**
- **Material Symbols**
- dashboard berbasis card
- sidebar/navigation
- responsive layout

## Perbedaan Antar Role

Jangan menyalin seluruh tampilan Admin ke role lain.

### Admin

Fokus UI:
- Administrasi pengguna
- Hak akses
- Master data
- Monitoring sistem
- Log aktivitas

### Petugas Unit

Fokus UI:
- Pengelolaan arsip
- Input arsip
- Upload dokumen
- Arsip digital
- Pencarian arsip
- Jadwal pemusnahan sesuai hak akses

### Pimpinan

Fokus UI:
- Monitoring
- Statistik
- Rekapitulasi
- Laporan
- Jadwal pemusnahan
- Export laporan

Isi final setiap dashboard harus mengikuti tugas role dan rancangan sistem.

## Elemen Desain Admin

Prototype Admin menggunakan konsep:

- Sidebar Admin
- Header/top app bar
- Welcome banner
- Statistik berbentuk card
- Quick Access Admin
- Tabel pengguna terbaru
- Timeline/log aktivitas
- Monitoring sistem
- Responsive layout
- Inter
- Material Symbols
- Card dengan border dan rounded corner
- Spacing yang konsisten

Sidebar Admin pada prototype mencakup:

```text
Menu Administrasi
├── Dashboard
├── Pengelolaan Pengguna
└── Hak Akses

Master Data
├── Unit Kerja
├── Ruangan
├── Lemari
├── Rak
├── Jenis Surat
└── Klasifikasi Arsip

Monitoring & Sistem
├── Monitoring Sistem
└── Log Aktivitas

Logout
```

Struktur tersebut adalah referensi desain Admin dan harus dicocokkan dengan role, flowchart, serta ERD sebelum setiap menu dihubungkan ke backend.

## PENTING — HTML Stitch Bukan Kode Produksi

JANGAN sekadar copy-paste HTML Stitch menjadi file PHP produksi.

HTML Stitch adalah prototype UI/UX.

Implementasi final harus mengikuti alur:

```text
Google Stitch HTML
        ↓
Referensi UI/UX
        ↓
PHP project yang sudah ada
        ↓
Authentication + role_id
        ↓
Backend
        ↓
Database
        ↓
Data nyata
```

Jangan membuat project baru hanya karena prototype menggunakan struktur HTML yang berbeda.

Jangan mengganti PHP menjadi framework lain tanpa instruksi khusus.

## Data Dummy

Jika prototype berisi:
- angka
- nama pengguna
- tanggal
- aktivitas
- statistik
- status
- data tabel

jangan menganggapnya sebagai data produksi.

Contoh:

```text
48 pengguna
42 pengguna aktif
6 pengguna nonaktif
126 aktivitas
```

Data tersebut harus diganti dengan hasil query database jika tabel/kolomnya tersedia.

## Aturan Saat Desain Bertentangan Dengan Sistem

Jika desain Google Stitch berbeda dengan:
- ERD
- flowchart
- database
- role
- authentication

JANGAN mengambil keputusan sendiri untuk mengubah database atau sistem.

Laporkan konflik terlebih dahulu.

Prioritas:

```text
Database + ERD
       ↓
Flowchart
       ↓
Role & Hak Akses
       ↓
Google Stitch UI
```

## Aturan Implementasi Seluruh Role

Untuk setiap halaman/dashboard:

1. Gunakan desain Stitch yang relevan sebagai referensi tampilan.
2. Jika desain role belum tersedia, gunakan design system Stitch yang sudah tersedia.
3. Jangan menyalin menu role lain.
4. Pertahankan authentication yang sudah berjalan.
5. Pertahankan `role_id`.
6. Terapkan hak akses di backend.
7. Ubah data dummy menjadi data database.
8. Jangan membuat database baru tanpa dasar ERD.
9. Jangan membuat data palsu.
10. Jangan menghapus fungsi lama tanpa alasan.
11. Pastikan halaman responsive.
12. Test akses sesuai role setelah perubahan.


# 8. DATA DUMMY

JANGAN menggunakan data dummy sebagai data produksi.

Contoh data prototype seperti:

```text
48 pengguna
42 pengguna aktif
6 pengguna nonaktif
126 aktivitas
```

harus diganti dengan data nyata dari database.

SALAH:

```php
$total_users = 48;
```

BENAR secara konsep:

```php
// Ambil COUNT dari tabel users sesuai struktur database yang sebenarnya.
```

Gunakan query yang sesuai dengan struktur database yang sebenarnya.

Jangan mengarang nama tabel atau kolom.

---

# 9. DASHBOARD ADMIN

Dashboard Admin harus mengikuti desain Google Stitch.

Komponen utama:
- Header
- Nama pengguna
- Role
- Profile
- Notifikasi jika tersedia
- Welcome banner
- Statistik
- Quick access
- Data pengguna terbaru
- Log aktivitas
- Monitoring sistem

Data harus dinamis dari database jika sumber datanya tersedia.

---

# 10. STATISTIK DASHBOARD

Dashboard Admin dapat menampilkan statistik sesuai data yang tersedia di database.

Contoh:
- Total Pengguna
- Pengguna Aktif
- Pengguna Nonaktif
- Log Aktivitas
- Total Unit Kerja
- Total Ruangan
- Total Lemari
- Total Rak
- Jenis Surat
- Klasifikasi Arsip

Jangan menampilkan angka yang tidak berasal dari database.

Jika tabel belum tersedia, jangan membuat angka palsu.

---

# 11. SIDEBAR ADMIN

Struktur navigasi Admin mengikuti desain:

## Menu Administrasi
- Dashboard
- Pengelolaan Pengguna
- Hak Akses

## Master Data
- Unit Kerja
- Ruangan
- Lemari
- Rak
- Jenis Surat
- Klasifikasi Arsip

## Monitoring
- Monitoring Sistem
- Log Aktivitas

Setiap menu harus memiliki tujuan yang jelas.

Jika halaman belum dibuat:
- jangan membuat halaman kosong yang terlihat seolah sudah berfungsi
- boleh membuat placeholder jika diperlukan
- beri status yang jelas
- jangan membuat fungsi palsu

---

# 12. MASTER DATA

Master data harus dibuat secara bertahap.

Urutan yang disarankan:
1. Unit Kerja
2. Ruangan
3. Lemari
4. Rak
5. Jenis Surat
6. Klasifikasi Arsip

Pastikan foreign key dan relasi mengikuti ERD.

Jangan mengubah hubungan database tanpa mengacu pada ERD yang sebenarnya.

---

# 13. PENGELOLAAN PENGGUNA

Admin dapat mengelola pengguna sesuai struktur database.

Fungsi yang dapat diperlukan:
- Lihat pengguna
- Tambah pengguna
- Edit pengguna
- Aktif/nonaktif pengguna
- Atur role
- Atur unit kerja
- Reset password jika sistem mendukung

Semua field harus mengikuti database.

Jangan membuat field baru tanpa alasan dan tanpa mengacu pada struktur database.

---

# 14. HAK AKSES

Modul Hak Akses harus mengikuti sistem role yang sudah ada.

Role utama:

```text
role_id = 1
Admin
```

Role lainnya mengikuti database/project yang sudah tersedia.

Jangan mengganti ID role yang sudah berjalan.

Jika ditemukan perbedaan antara dokumentasi dan database:

> Prioritaskan database/project yang sudah berjalan dan laporkan perbedaannya sebelum melakukan perubahan besar.

---

# 15. ARSIP DIGITAL

Modul arsip harus mengikuti flowchart dan ERD.

Data arsip tidak boleh dibuat dengan struktur sendiri.

AI harus memeriksa:
- nomor surat/registrasi
- jenis surat
- klasifikasi
- tanggal
- unit kerja
- lokasi penyimpanan
- masa surat
- file scan
- status arsip

Field final mengikuti ERD.

---

# 16. ATURAN UPLOAD FILE

## WAJIB

**Ukuran maksimal satu file = 20 MB.**

20 MB adalah batas untuk **SATU FILE**, bukan kapasitas total storage.

Contoh:

```text
file1.pdf = 8 MB       → BOLEH
file2.pdf = 19 MB      → BOLEH
file3.pdf = 20 MB      → BOLEH
file4.pdf = 20.5 MB    → TOLAK
```

Validasi harus dilakukan di backend.

Jika ukuran file melebihi 20 MB:
- upload ditolak
- file tidak disimpan
- tampilkan pesan error yang jelas

Jangan hanya mengandalkan validasi JavaScript.

---

# 17. KEAMANAN FILE

Untuk upload file:
- Validasi ukuran
- Validasi extension
- Validasi MIME type
- Gunakan nama file yang aman
- Hindari path traversal
- Jangan menjalankan file upload sebagai script
- Jangan percaya nama file dari user
- Jangan percaya MIME type dari browser saja
- Gunakan validasi server-side

Jangan membuka celah upload file PHP/script berbahaya.

---

# 18. PENCARIAN ARSIP

Fitur pencarian harus mengikuti kebutuhan flowchart.

Pencarian dapat menggunakan data yang memang tersedia dalam database.

Contoh:
- Nomor registrasi
- Nomor surat
- Jenis surat
- Tanggal
- Unit kerja
- Klasifikasi
- Lokasi
- Status

Jangan membuat filter yang tidak memiliki sumber data.

---

# 19. JADWAL PEMUSNAHAN

Fitur pemusnahan mengikuti flowchart.

Sistem harus dapat menangani:
- masa arsip
- jadwal pemusnahan
- status arsip
- pengingat jika tersedia
- riwayat tindakan

Jangan langsung membuat proses pemusnahan tanpa memeriksa struktur ERD dan flowchart.

---

# 20. PENGEMBANGAN BERTAHAP

Jangan mengerjakan seluruh sistem sekaligus.

## TAHAP 1 — LOGIN + AUTHENTICATION

STATUS:
**SUDAH ADA / BERJALAN**

Jangan merusak.

## TAHAP 2 — DASHBOARD ADMIN

Target:
- Layout Stitch
- Sidebar
- Header
- Welcome banner
- Statistik
- Quick access
- Data terbaru
- Monitoring

Semua data diarahkan ke database jika sumber datanya tersedia.

## TAHAP 3 — PENGELOLAAN PENGGUNA

Target:
- daftar pengguna
- tambah
- edit
- status
- role
- unit kerja

## TAHAP 4 — HAK AKSES

Target:
- role
- permission
- proteksi halaman
- proteksi aksi

## TAHAP 5 — MASTER DATA

Target:
- Unit Kerja
- Ruangan
- Lemari
- Rak
- Jenis Surat
- Klasifikasi Arsip

## TAHAP 6 — PETUGAS UNIT

Target:
- Dashboard Petugas
- Input Arsip
- Upload dokumen
- Validasi file maksimal 20 MB
- Arsip Digital

## TAHAP 7 — PENCARIAN & PEMUSNAHAN

Target:
- Pencarian
- Filter
- Jadwal pemusnahan
- Pengingat
- Status arsip

## TAHAP 8 — PIMPINAN

Target:
- Dashboard
- Statistik
- Monitoring
- Laporan
- Export

## TAHAP 9 — TESTING

Testing:
- Login
- Role
- Permission
- CRUD
- Upload
- Search
- Filter
- Security
- Responsive UI
- Database
- Error handling

---

# 21. ATURAN SAAT MENGUBAH CODE

Sebelum mengubah file:

1. Baca file tersebut.
2. Pahami fungsi yang sudah ada.
3. Cari dependensi.
4. Periksa database query.
5. Periksa authentication.
6. Periksa role.
7. Baru lakukan perubahan.

JANGAN mengganti seluruh file hanya karena ingin memasukkan desain baru jika bagian lama masih diperlukan.

Prioritaskan:

**Perubahan kecil + terkontrol + mudah diuji**

daripada:

**Rewrite seluruh project**

---

# 22. JANGAN MERUSAK FITUR YANG SUDAH BERJALAN

Sebelum perubahan:
- Login harus tetap berjalan.
- Logout harus tetap berjalan.
- Database connection harus tetap berjalan.
- `role_id` harus tetap berjalan.
- Authentication harus tetap berjalan.

Setelah perubahan:
- Test login Admin.
- Test role.
- Test akses halaman.
- Test logout.
- Test database.

---

# 23. JIKA MENEMUKAN MASALAH

Jika AI menemukan:
- tabel tidak ada
- kolom tidak ada
- relasi tidak jelas
- role tidak sesuai
- flowchart berbeda dengan database
- ERD berbeda dengan implementasi
- desain Stitch membutuhkan data yang tidak tersedia

JANGAN mengarang solusi database.

Laporkan:

```text
MASALAH:
...

FILE/TABEL TERKAIT:
...

YANG DIBUTUHKAN:
...

SARAN:
...
```

Kemudian jangan melakukan perubahan struktural besar sebelum masalah tersebut jelas.

---

# 24. PROSEDUR SETIAP TASK

Sebelum coding:

### STEP 1
Baca `README_AI.md`.

### STEP 2
Identifikasi task.

### STEP 3
Cari file yang berkaitan.

### STEP 4
Periksa database dan ERD.

### STEP 5
Periksa role/hak akses.

### STEP 6
Implementasikan perubahan seminimal mungkin.

### STEP 7
Test fitur.

### STEP 8
Pastikan fitur lama tidak rusak.

### STEP 9
Laporkan file yang diubah.

---

# 25. FORMAT LAPORAN SETELAH CODING

Setelah menyelesaikan task, AI harus memberikan laporan:

```text
TASK:
Nama task

STATUS:
Selesai / Sebagian / Gagal

FILE YANG DIUBAH:
- file1.php
- file2.php

FILE BARU:
- file.php

DATABASE:
Tidak ada perubahan
atau
Perubahan: ...

ROLE:
Admin / Petugas / Pimpinan

TEST:
- Login: OK
- Role: OK
- Dashboard: OK
- Database: OK

CATATAN:
...
```

---

# 26. ATURAN UTAMA

AI WAJIB mengikuti prinsip:

1. Jangan merusak fitur yang sudah berjalan.
2. Jangan mengubah `role_id` sembarangan.
3. Jangan mengarang database.
4. Jangan mengarang field.
5. Jangan mengarang role.
6. Jangan mengabaikan flowchart.
7. Jangan mengabaikan ERD.
8. Gunakan desain Stitch sebagai acuan UI.
9. Data prototype harus diganti dengan data database.
10. Upload maksimal 20 MB per file.
11. Validasi keamanan harus dilakukan di server.
12. Kerjakan sistem secara bertahap.
13. Test setiap perubahan.
14. Jangan melakukan rewrite besar tanpa alasan.
15. Jika informasi tidak cukup, laporkan dan jangan menebak.

---

# 27. TUJUAN AKHIR

Tujuan akhir project adalah membangun sistem **e-ARSIP KAI DIVRE III** yang:

- memiliki authentication
- memiliki role dan hak akses
- memiliki dashboard sesuai role
- memiliki pengelolaan pengguna
- memiliki master data
- memiliki pengelolaan arsip
- memiliki arsip digital
- memiliki pencarian
- memiliki jadwal pemusnahan
- memiliki monitoring
- memiliki laporan
- aman
- responsive
- terhubung dengan database
- mengikuti flowchart dan ERD
- menggunakan desain UI/UX yang telah dibuat di Google Stitch

---

# END OF README_AI.md
