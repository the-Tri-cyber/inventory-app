<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil input dari form
    $kategori = htmlspecialchars(trim($_POST['kategori']));

    // Memastikan bahwa nama kategori tidak kosong
    if (empty($kategori)) {
        header("Location: tambah.php?error=" . urlencode("Nama kategori tidak boleh kosong."));
        exit;
    }

    // Siapkan query untuk menambahkan kategori
    $query = $conn->prepare("INSERT INTO kategori (kategori) VALUES (?)");
    $query->bind_param("s", $kategori); // Pastikan formatnya sesuai

    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Kategori berhasil ditambahkan."));
    } else {
        header("Location: tambah.php?error=" . urlencode("Gagal menambahkan kategori: " . $query->error));
    }

    $query->close();
}
?>