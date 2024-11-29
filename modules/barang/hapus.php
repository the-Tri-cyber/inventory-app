<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

$id = intval($_GET['id']);

// Cek apakah ID valid
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil nama file gambar dari database sebelum menghapus
$query = $conn->prepare("SELECT gambar FROM barang WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$barang = $result->fetch_assoc();

if ($barang) {
    $gambarPath = '../uploads/' . $barang['gambar']; // Path lengkap ke file gambar

    // Menangani penghapusan
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Hapus file gambar jika ada
        if (file_exists($gambarPath)) {
            unlink($gambarPath); // Menghapus file gambar
        }

        // Hapus baris dari tabel barang
        $deleteQuery = $conn->prepare("DELETE FROM barang WHERE id = ?");
        $deleteQuery->bind_param("i", $id);
        
        if ($deleteQuery->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $error_message = "Error: " . $deleteQuery->error;
        }
    }
} else {
    $error_message = "Barang tidak ditemukan.";
}

// Set title dan content untuk layout
$title = "Hapus Barang";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Hapus Barang</h1>
        <p>Apakah Anda yakin ingin menghapus barang ini?</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="' . $id . '">
            <button type="submit" class="btn btn-danger">Hapus</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
';

if (isset($error_message)) {
    $content .= '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
}

include '../../views/layout.php';
?>