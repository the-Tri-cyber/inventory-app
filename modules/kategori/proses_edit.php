<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kategori = intval($_POST['id_kategori']);
    $kategori = htmlspecialchars(trim($_POST['kategori']));

    // Memastikan bahwa nama kategori tidak kosong
    if (empty($kategori)) {
        header("Location: edit.php?id=$id_kategori&error=" . urlencode("Nama kategori tidak boleh kosong."));
        exit;
    }

    // Query untuk update data
    $query = $conn->prepare("UPDATE kategori SET kategori = ? WHERE id_kategori = ?");
    $query->bind_param("si", $kategori, $id_kategori);

    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Kategori berhasil diperbarui."));
    } else {
        header("Location: edit.php?id=$id_kategori&error=" . urlencode("Gagal memperbarui kategori: " . $query->error));
    }

    $query->close();
}
?>