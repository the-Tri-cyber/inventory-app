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
