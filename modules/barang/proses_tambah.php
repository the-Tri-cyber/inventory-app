<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki hak akses admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $merk = htmlspecialchars(trim($_POST['merk']));
    $id_kategori = htmlspecialchars(trim($_POST['id_kategori']));
    $id_ruangan = htmlspecialchars(trim($_POST['id_ruangan']));
    $stok = intval($_POST['stok']);
    $asal_perolehan = htmlspecialchars(trim($_POST['asal_perolehan']));

    // Jika ada kesalahan, redirect ke halaman tambah.php dengan pesan kesalahan
    if (!empty($error_message)) {
        header("Location: tambah.php?error=" . urlencode($error_message));
        exit;
    }

    // Query untuk menyimpan data barang
    $query = $conn->prepare("INSERT INTO barang (nama_barang, merk, id_kategori, id_ruangan, stok, asal_perolehan) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssiiss", $nama_barang, $merk, $id_kategori, $id_ruangan, $stok, $asal_perolehan);

    if ($query->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error_message = "Error: " . $query->error;
        header("Location: tambah.php?error=" . urlencode($error_message));
        exit;
    }
}
?>