<?php
session_start();

// Periksa autentikasi dan otorisasi
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Ambil ID dari URL dan validasi
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Cek apakah barang dengan ID tersebut ada di database
$query = $conn->prepare("SELECT id FROM barang WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$barang = $result->fetch_assoc();

if (!$barang) {
    $error_message = "Barang tidak ditemukan.";
} else {
    // Jika permintaan adalah POST, proses penghapusan
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $deleteQuery = $conn->prepare("DELETE FROM barang WHERE id = ?");
        $deleteQuery->bind_param("i", $id);

        if ($deleteQuery->execute()) {
            header("Location: index.php?success=" . urlencode("Barang berhasil dihapus."));
            exit;
        } else {
            $error_message = "Terjadi kesalahan: " . $deleteQuery->error;
        }
    }
}

// Set judul dan konten untuk layout
$title = "Hapus Barang";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Hapus Barang</h1>';

if (isset($error_message)) {
    $content .= '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
} else {
    $content .= '
        <p>Apakah Anda yakin ingin menghapus barang ini?</p>
        <form action="" method="POST">
            <button type="submit" class="btn btn-danger">Hapus</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>';
}

$content .= '</div>';

include '../../views/layout.php';
?>
