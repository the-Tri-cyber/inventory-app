<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Ambil data kondisi
$kondisi_query = $conn->query("SELECT * FROM kondisi");
$kondisi_options = '';
while ($row = $kondisi_query->fetch_assoc()) {
    $kondisi_options .= '<option value="' . $row['id_kondisi'] . '">' . htmlspecialchars($row['kondisi']) . '</option>';
}

$query = "SELECT id, nama_barang FROM barang";
$result = $conn->query($query);

// Menyiapkan variabel untuk layout
$title = "Tambah Transaksi";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Tambah Transaksi</h1>
        <form action="proses_tambah.php" method="POST">
            <div class="mb-3">
                <label for="id_barang" class="form-label">Item:</label>
                <select name="id_barang" id="id_barang" class="form-select" required>
                    <option value="">Pilih Item</option>';
                    while ($row = $result->fetch_assoc()) {
                        $content .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nama_barang']) . '</option>';
                    }
$content .= '
                </select>
            </div>
            <div class="mb-3">
                <label for="jenis" class="form-label">Jenis Transaksi:</label>
                <select name="jenis" id="jenis" class="form-select" required>
                    <option value="masuk">Masuk</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah:</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label for="id_kondisi" class="form-label">Kondisi:</label>
                <select class="form-select" id="id_kondisi" name="id_kondisi" required>
                    <option value="">Pilih Kondisi</option>
                    ' . $kondisi_options . '
                </select>
                <div class="invalid-feedback">Kondisi harus diisi.</div>
            </div>
            <div class="mb-3">
                <label for="nomer_surat_jalan" class="form-label">No Surat Jalan:</label>
                <input type="text" name="nomer_surat_jalan" id="nomer_surat_jalan" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal dan Waktu:</label>
                <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
';

// Menggunakan layout
include '../../views/layout.php';