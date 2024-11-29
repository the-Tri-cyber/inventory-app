<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kondisi = intval($_POST['id_kondisi']);
    $kondisi = htmlspecialchars(trim($_POST['kondisi']));

    // Memastikan bahwa nama kondisi tidak kosong
    if (empty($kondisi)) {
        header("Location: edit.php?id=$id_kondisi&error=" . urlencode("Nama kondisi tidak boleh kosong."));
        exit;
    }

    // Query untuk update data
    $query = $conn->prepare("UPDATE kondisi SET kondisi = ? WHERE id_kondisi = ?");
    $query->bind_param("si", $kondisi, $id_kondisi);

    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Kondisi berhasil diperbarui."));
    } else {
        header("Location: edit.php?id=$id_kondisi&error=" . urlencode("Gagal memperbarui kondisi: " . $query->error));
    }

    $query->close();
}
?>