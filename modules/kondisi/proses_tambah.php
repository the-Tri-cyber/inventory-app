<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil input dari form
    $kondisi = htmlspecialchars(trim($_POST['kondisi']));

    // Memastikan bahwa nama kondisi tidak kosong
    if (empty($kondisi)) {
        header("Location: tambah.php?error=" . urlencode("Nama kondisi tidak boleh kosong."));
        exit;
    }

    // Siapkan query untuk menambahkan kondisi
    $query = $conn->prepare("INSERT INTO kondisi (kondisi) VALUES (?)");
    $query->bind_param("s", $kondisi); // Pastikan formatnya sesuai

    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Kondisi berhasil ditambahkan."));
    } else {
        header("Location: tambah.php?error=" . urlencode("Gagal menambahkan kondisi: " . $query->error));
    }

    $query->close();
}
?>