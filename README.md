# AquaLok Store — Website + REST API (Laravel 12)

Project ini menggabungkan halaman depan AquaLok Store (Blade/CSS) dengan REST API Laravel dalam **satu aplikasi**. Tampilan depan dipertahankan; daftar produk, kategori, harga, stok, dan gambar katalog dimuat dari database melalui `GET /api/products`. Data contoh dikelola lewat migration dan seeder. API mendukung JSON Resource, CRUD produk, upload/update/delete gambar, serta autentikasi token menggunakan Laravel Sanctum.

## Kebutuhan
- PHP 8.2 atau lebih baru, dengan ekstensi Laravel yang diperlukan (termasuk `pdo_sqlite` untuk konfigurasi awal SQLite).
- Composer.
- Node.js dan npm (hanya diperlukan bila ingin membangun ulang aset frontend).
- Postman (opsional, untuk menguji REST API).

## Cara menjalankan di Windows / VS Code

1. Ekstrak ZIP, lalu buka folder `project_laravel` di VS Code.
2. Buka Terminal di folder yang sama dan instal dependency:
   ```bash
   composer install
   ```
   ZIP ini sengaja tidak menyertakan `vendor/` maupun `composer.lock`; Composer akan menyelesaikan dependency yang dinyatakan di `composer.json`, termasuk Laravel Sanctum.
3. Buat `.env` dari template jika belum ada:
   - CMD: `copy .env.example .env`
   - PowerShell: `Copy-Item .env.example .env`
4. Buat application key:
   ```bash
   php artisan key:generate
   ```
5. Siapkan database contoh dari awal:
   ```bash
   php artisan migrate:fresh --seed
   ```
   **Penting:** `migrate:fresh` menghapus tabel yang ada pada database aktif. Gunakan hanya untuk database latihan yang aman di-reset. File `.env.example` memakai SQLite; pastikan ekstensi `pdo_sqlite` aktif. Jika menggunakan MySQL, ubah pengaturan `DB_*` pada `.env` terlebih dahulu.
6. Buat link penyimpanan untuk gambar yang diunggah lewat API:
   ```bash
   php artisan storage:link
   ```
7. Jalankan server:
   ```bash
   php artisan serve
   ```
8. Buka `http://127.0.0.1:8000`. Website harus menampilkan katalog produk yang diambil dari REST API. Periksa endpoint secara terpisah di `http://127.0.0.1:8000/api/products`.

Aset CSS bawaan sudah disertakan di `public/build`, sehingga untuk menjalankan aplikasi seperti adanya biasanya tidak perlu `npm install`. Untuk mengubah CSS/JS Vite, jalankan `npm install` lalu `npm run build`.

## Endpoint API
Seluruh endpoint memakai prefix `/api`. Saat mencoba di Postman, tambahkan header `Accept: application/json` agar error validasi dikirim sebagai JSON.

| Method | Endpoint | Akses | Kegunaan |
|---|---|---|---|
| GET | `/api/categories` | Publik | Daftar kategori produk |
| GET | `/api/products` | Publik | Daftar produk dengan relasi kategori dan pagination |
| GET | `/api/products/{id}` | Publik | Detail produk |
| POST | `/api/register` | Publik | Registrasi dan membuat Bearer token |
| POST | `/api/login` | Publik | Login dan mendapat Bearer token |
| GET | `/api/profile` | Bearer token | Membaca profil pengguna token aktif |
| POST | `/api/logout` | Bearer token | Mencabut token aktif |
| POST | `/api/products` | Bearer token | Membuat produk, termasuk unggah `img` opsional |
| PUT/PATCH | `/api/products/{id}` | Bearer token | Memperbarui produk/gambar |
| DELETE | `/api/products/{id}` | Bearer token | Menghapus produk dan berkas gambar bila ada |

### Format data
Resource produk memuat `id`, `name`, `slug`, `description`, `price`, `stock`, `img_url`, serta `category`. Gambar unggahan dibatasi ke JPG/JPEG/PNG/WEBP maks. 2 MB; file disimpan di `storage/app/public/products`. Produk contoh yang belum punya gambar unggahan menggunakan gambar fallback lokal di `public/images/products`.

### Uji dengan Postman
1. Import `docs/AquaLok-REST-API.postman_collection.json` ke Postman.
2. Coba **Public Catalog → List Categories** dan **Products → List Products (Public)** tanpa token.
3. Jalankan **Register** dengan `name`, `email`, `password` (minimal 8 karakter), dan `password_confirmation`; atau jalankan **Login** jika email sudah terdaftar.
4. Salin token dari response dan pasang sebagai Bearer token/variable `token` collection untuk Profile, Logout, dan endpoint perubahan produk.
5. Untuk unggah gambar, gunakan `Body → form-data`, ubah field `img` menjadi **File**; jangan set `Content-Type` multipart secara manual.
6. Periksa daftar route: `php artisan route:list --path=api`.

## Catatan integrasi dan keamanan
- Halaman depan tidak lagi menyimpan array produk hardcoded; ia memanggil `GET /api/products` pada aplikasi yang sama. Nama, harga, kategori, stok, dan identitas keranjang berasal dari response API. Pencarian/filter berjalan terhadap kartu yang dirender setelah response.
- Membaca katalog publik tidak memerlukan token agar pengunjung bisa berbelanja. Operasi membuat/mengubah/menghapus produk tetap dilindungi `auth:sanctum`.
- Keranjang depan masih disimpan di browser (`localStorage`); checkout dan transaksi belum menjadi bagian dari integrasi ini.
- Jangan membagikan `.env`; file itu menyimpan konfigurasi lokal/rahasia dan tidak disertakan dalam ZIP.
