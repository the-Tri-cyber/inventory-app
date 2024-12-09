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
    SELECT t.id, b.nama_barang, t.jenis, t.jumlah, k.kondisi, t.id_kondisi, t.nomer_surat_jalan, t.tanggal
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id
    JOIN kondisi k ON t.id_kondisi = k.id_kondisi
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

// Query untuk mengambil semua kondisi
$kondisi_query = $conn->query("SELECT * FROM kondisi");
$kondisi_options = '';
while ($kondisi = $kondisi_query->fetch_assoc()) {
    // Tandai opsi yang sesuai dengan id_kondisi dari transaksi
    $selected = ($kondisi['id_kondisi'] == $row['id_kondisi']) ? 'selected' : '';
    $kondisi_options .= '<option value="' . $kondisi['id_kondisi'] . '" ' . $selected . '>' . htmlspecialchars($kondisi['kondisi']) . '</option>';
}

// Menyiapkan variabel untuk layout
$title = "Edit Transaksi";
$content = '
    <h1 class="mb-4">Edit Transaksi</h1>
    <form action="proses_edit.php" method="POST">
        <input type="hidden" name="id" value="' . $row['id'] . '">
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Item</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="' . htmlspecialchars($row['nama_barang']) . '" required readonly>
        </div>
        <div class="mb-3">
            <label for="jenis" class="form-label">Jenis Transaksi:</label>
            <select name="jenis" id="jenis" class="form-select" required>
                <option value="masuk" ' . ($row['jenis'] === 'masuk' ? 'selected' : '') . '>Masuk</option>
                <option value="keluar" ' . ($row['jenis'] === 'keluar' ? 'selected' : '') . '>Keluar</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" value="' . $row['jumlah'] . '" required>
        </div>
        <div class="mb-3">
            <label for="id_kondisi" class="form-label">ID Kondisi:</label>
            <select class="form-select" id="id_kondisi" name="id_kondisi" required>
                <option value="">Pilih Kondisi</option>
                ' . $kondisi_options . '
            </select>
            <div class="invalid-feedback">ID Kondisi harus diisi.</div>
        </div>
        <div class="mb-3">
            <label for="nomer_surat_jalan" class="form-label">No Surat Jalan</label>
            <input type="text" class="form-control" id="nomer_surat_jalan" name="nomer_surat_jalan" value="' . $row['nomer_surat_jalan'] . '" required>
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
