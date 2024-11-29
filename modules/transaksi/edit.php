<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Ambil ID transaksi dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Query untuk mengambil data transaksi berdasarkan ID
$query = "
    SELECT t.id, b.nama_barang, t.jenis, t.jumlah, t.tanggal
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id
    WHERE t.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$row = $result->fetch_assoc();

// Menyiapkan variabel untuk layout
$title = "Edit Transaksi";
$content = '
    <h1 class="mb-4">Edit Transaksi</h1>
    <form action="proses_edit.php" method="POST">
        <input type="hidden" name="id" value="' . $row['id'] . '">
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="' . htmlspecialchars($row['nama_barang']) . '" required readonly>
        </div>
        <div class="mb-3">
            <label for="jenis" class="form-label">Jenis</label>
            <input type="text" class="form-control" id="jenis" name="jenis" value="' . htmlspecialchars($row['jenis']) . '" required>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" value="' . $row['jumlah'] . '" required>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal dan Waktu</label>
            <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" value="' . date('Y-m-d\TH:i', strtotime($row['tanggal'])) . '" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
';

// Menggunakan layout
include '../../views/layout.php';
?>