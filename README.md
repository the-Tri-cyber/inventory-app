# Inventory App

Inventory App adalah aplikasi manajemen inventaris yang memungkinkan pengguna untuk mengelola barang, kategori, kondisi, dan transaksi dengan mudah. Aplikasi ini dibangun menggunakan PHP dan MySQL.

## Syarat Penggunaan

Sebelum memulai, pastikan Anda memiliki perangkat lunak berikut terinstal:

- **Node.js** v22.11.0
- **PHP** v8.2.12
- **Composer** v2.8.3

## Langkah-Langkah Penggunaan

Ikuti langkah-langkah berikut untuk mengatur dan menjalankan aplikasi:

Clone repository ini menggunakan perintah berikut:
`git clone https://github.com/the-tri-cyber/inventory-app.git`
Buka folder repository yang telah di clone menggunakan perintah berikut:
`cd inventory-app`
Install tcpdf library menggunakan perintah berikut:
`composer require tecnickcom/tcpdf` (extension zip pada php harus aktif, dengan menghilangkan tanda ";")
Install phpspreadsheet library menggunakan perintah berikut:
`composer require phpoffice/phpspreadsheet` (extension gd pada php harus aktif, dengan menghilangkan tanda ";")
jalankan mysql dan apache server
buat database baru dengan nama inventory
import database inventory.sql ke dalam database inventory
jalankan aplikasi menggunakan perintah berikut:
`localhost/inventory-app/`
login menggunakan email dan password dibawah.

note: jangan ubah nama folder `inventory-app` karena akan menyebabkan aplikasi tidak dapat berjalan dengan benar. Jika ingin mengubah nama folder, pastikan untuk mengubah nama folder di dalam file `config.php` dan mengubah nama folder di dalam file `index.php` juga. serta path yang berkaitan dengan nama folder tersebut.

# Pembagian Akses Berdasarkan Role

## 1. Admin

**Akses Penuh:** Admin memiliki hak akses penuh ke semua fitur dan data dalam aplikasi.

### Hak Akses:

- Melihat, menambah, mengedit, dan menghapus barang.
- Melihat, menambah, mengedit, dan menghapus kategori, kondisi, dan ruang.
- Melihat dan mengelola transaksi.
- Mengelola pengguna (menambah, mengedit, dan menghapus pengguna).
- Melihat laporan dan statistik.

## 2. Manager

**Akses Terbatas:** Manager memiliki hak akses untuk mengelola barang dan transaksi, tetapi tidak dapat mengelola pengguna.

### Hak Akses:

- Melihat dan mengelola barang (menambah, mengedit, dan menghapus barang).
- Melihat dan mengelola transaksi (masuk dan keluar).
- Melihat laporan dan statistik terkait barang dan transaksi.

## 3. User

**Akses Sangat Terbatas:** User hanya dapat melihat informasi barang dan transaksi yang mereka lakukan sendiri.

### Hak Akses:

- Melihat daftar barang.
- Melihat transaksi yang telah dilakukan.
- Tidak dapat menambah, mengedit, atau menghapus barang atau transaksi.

## Informasi Login

Aplikasi ini dibangun menggunakan PHP Procedural versi 8.2.12 dan database MySQL. Berikut adalah detail login untuk masing-masing role:

### 1. Login Admin

- **Email:** `admin1@gmail.com`
- **Password:** `i`

### 2. Login Manager

- **Email:** `manager1@gmail.com`
- **Password:** `i`

### 3. Login User

- **Email:** `user1@gmail.com`
- **Password:** `i`

## Pendaftaran Pengguna

Pengguna baru dapat mendaftar, tetapi akan terdaftar sebagai User. Harap diperhatikan bahwa alamat email yang digunakan untuk pendaftaran tidak boleh sama dengan yang sudah terdaftar.
