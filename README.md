# Aplikasi Form Pengaduan — CodeIgniter 4

Aplikasi form pengaduan publik + tracking nomor tiket, sesuai desain pada gambar yang diberikan.

## Struktur File

```
app/
 ├─ Config/
 │   └─ Routes.php                          (tambahkan isi ini ke Routes.php project CI4 Anda)
 ├─ Controllers/
 │   ├─ PengaduanController.php              (form publik + halaman status tiket)
 │   └─ Admin/
 │       └─ PengaduanAdminController.php     (admin mengatur SLA & status tiap tahap)
 ├─ Models/
 │   ├─ KategoriModel.php
 │   ├─ LokasiModel.php
 │   ├─ SlaDefaultModel.php
 │   ├─ PengaduanModel.php                   (generator nomor tiket JATIM-2026-00125)
 │   ├─ PengaduanTahapanModel.php            (timeline 4 tahap + SLA per tahap)
 │   ├─ LampiranModel.php
 │   └─ AuditTrailModel.php
 └─ Views/
     ├─ layout/header.php, footer.php
     ├─ pengaduan/form.php                   (= tampilan gambar 1)
     ├─ pengaduan/tiket.php                  (= tampilan gambar 2)
     └─ admin/pengaduan_list.php, pengaduan_sla.php

database/
 └─ pengaduan.sql                            (skema database MySQL, import lewat SQLyog)

public/uploads/bukti_pengaduan/              (folder penyimpanan file lampiran, pastikan writable)
```

## Cara Instalasi

1. **Buat project CodeIgniter 4** (jika belum ada):
   ```
   composer create-project codeigniter4/appstarter pengaduan-ci4
   ```

2. **Salin file-file di folder `app/`** dari paket ini ke folder `app/` project CI4 Anda
   (timpa `app/Config/Routes.php` yang sudah ada — cukup tambahkan isi rute dari file ini ke rute yang sudah ada, atau ganti seluruhnya bila project masih kosong).

3. **Import database**
   - Buka SQLyog (atau phpMyAdmin/HeidiSQL).
   - Jalankan file `database/pengaduan.sql`. Ini akan membuat database `db_pengaduan`
     beserta seluruh tabel dan data awal (kategori, lokasi, SLA default).

4. **Atur koneksi database** di `app/Config/Database.php` atau file `.env`:
   ```
   database.default.hostname = localhost
   database.default.database = db_pengaduan
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```

5. **Pastikan folder upload writable**:
   ```
   public/uploads/bukti_pengaduan/
   ```

6. **Jalankan server**
   ```
   php spark serve
   ```

7. Buka:
   - Form pengaduan: `http://localhost:8080/pengaduan`
   - Setelah kirim aduan, otomatis diarahkan ke halaman status: `http://localhost:8080/pengaduan/status/JATIM-2026-00125`
   - Admin atur SLA: `http://localhost:8080/admin/pengaduan`

## Alur Kerja SLA

- Saat pengaduan pertama kali dikirim, sistem otomatis membuat **4 baris tahapan**
  (Diterima, Diverifikasi, Ditangani Bidang, Selesai) di tabel `pengaduan_tahapan`,
  dengan nilai SLA awal diambil dari tabel `sla_default` (bisa diedit sebelumnya oleh admin
  sebagai nilai default per kategori).
- Admin dapat mengubah **jumlah hari SLA** dan **status** (Menunggu / Proses / Selesai)
  untuk tiap tahap pengaduan tertentu melalui halaman `/admin/pengaduan/{nomor_tiket}`.
- Halaman status tiket (`pengaduan/tiket.php`) menampilkan SLA per tahap dan **Total SLA Maks.**
  (penjumlahan seluruh SLA tahap) sesuai kartu hijau pada gambar kedua.

## Catatan

- Nomor tiket dibuat otomatis dengan format `{KODE_WILAYAH}-{TAHUN}-{5 digit urut}`,
  contoh `JATIM-2026-00125`, berdasarkan `kode_wilayah` pada lokasi kejadian yang dipilih.
- Password admin default pada tabel `admin` adalah `admin123` — bagian login belum
  disertakan (silakan tambahkan `Shield`/auth filter CI4 sesuai kebutuhan keamanan Anda).
- Tampilan menggunakan Tailwind CSS via CDN agar langsung sama persis secara visual
  dengan referensi gambar tanpa perlu proses build tambahan.
