# SITEMAN-SURAT

Aplikasi pengelolaan surat masuk dan keluar untuk mendukung alur kerja Sekretariat, Caraka, Bagian TU Pimpinan, serta Persuratan dan Kearsipan.

## Fitur

- Pencatatan dan pengelolaan surat masuk dan surat keluar.
- Pengelompokan surat biasa, rahasia, dan sangat rahasia.
- Pengiriman kepada penerima internal atau penerima eksternal melalui alur Caraka.
- Pencarian satu kata kunci pada judul, nomor surat, nama file, pengirim, dan penerima.
- Penyimpanan file surat dan bukti penerimaan.
- Pengaturan akun dan role pengguna.

Nama asli file yang diunggah disimpan untuk ditampilkan pada hasil pencarian. Untuk surat lama yang belum memiliki nama asli tersimpan, aplikasi menggunakan judul surat dan ekstensi file sebagai nama tampilan.

## Teknologi

- PHP 8.2 atau lebih baru
- Laravel 11
- Composer
- Node.js dan npm
- Database yang didukung Laravel, seperti MySQL atau SQLite

## Instalasi Lokal

1. Pasang dependency PHP dan JavaScript:

   ```powershell
   composer install
   npm install
   ```

2. Buat file environment dan application key:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Atur koneksi database di `.env`. Untuk MySQL lokal, buat database `persuratan`, lalu atur:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=persuratan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   `.env.example` menggunakan SQLite. Jika memilih SQLite, pastikan `DB_CONNECTION=sqlite` dan file database tersedia di `database/database.sqlite`.

4. Jalankan migration dan seeder, lalu buat symbolic link untuk file publik:

   ```powershell
   php artisan migrate --seed
   php artisan storage:link
   ```

   Seeder membuat role aplikasi, tetapi tidak membuat akun pengguna atau akun admin. Registrasi membuat akun dengan role `user`; siapkan akun admin melalui proses provisioning yang berlaku di lingkungan instalasi.

5. Build aset frontend:

   ```powershell
   npm run build
   ```

6. Jalankan server lokal:

   ```powershell
   php artisan serve
   ```

   Buka URL yang ditampilkan oleh Artisan, biasanya `http://127.0.0.1:8000`.

## Email

Konfigurasi contoh menggunakan `MAIL_MAILER=log`, sehingga email notifikasi ditulis ke log aplikasi. Atur koneksi SMTP pada `.env` untuk mengirim email sungguhan.

## Pengujian

Jalankan test suite dengan:

```powershell
php artisan test
```

## Catatan Keamanan

- Jangan commit `.env`, kredensial, atau file surat yang diunggah.
- Gunakan `APP_ENV=production` dan `APP_DEBUG=false` di lingkungan produksi.
- Pastikan akses database, penyimpanan file, dan role pengguna sesuai kebijakan organisasi.