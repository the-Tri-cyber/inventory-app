<?php
session_start();

// Periksa autentikasi dan otorisasi
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi dan sanitasi input
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama_barang = isset($_POST['nama_barang']) ? htmlspecialchars(trim($_POST['nama_barang'])) : '';
    $merk = isset($_POST['merk']) ? htmlspecialchars(trim($_POST['merk'])) : '';
    $id_kategori = isset($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 0;
    $id_ruangan = isset($_POST['id_ruangan']) ? intval($_POST['id_ruangan']) : 0;
    $stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;
    $asal_perolehan = isset($_POST['asal_perolehan']) ? htmlspecialchars(trim($_POST['asal_perolehan'])) : '';

    // Validasi apakah semua input telah diisi
    if ($id <= 0 || empty($nama_barang) || empty($merk) || $id_kategori <= 0 || $id_ruangan <= 0 || $stok < 0 || empty($asal_perolehan)) {
        $error_message = "Data tidak valid. Mohon periksa kembali input Anda.";
        header("Location: edit.php?id=" . $id . "&error=" . urlencode($error_message));
        exit;
    }

    // Siapkan query untuk update barang
    $query = $conn->prepare("
        UPDATE barang 
        SET nama_barang = ?, 
            merk = ?, 
            id_kategori = ?, 
            id_ruangan = ?, 
            stok = ?, 
            asal_perolehan = ?, 
            updated_at = NOW() 
        WHERE id = ?
    ");
    $query->bind_param("ssiiisi", $nama_barang, $merk, $id_kategori, $id_ruangan, $stok, $asal_perolehan, $id);

    // Eksekusi query
    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Data barang berhasil diperbarui."));
        exit;
    } else {
        $error_message = "Terjadi kesalahan: " . $query->error;
        header("Location: edit.php?id=" . $id . "&error=" . urlencode($error_message));
        exit;
    }
}
?>
