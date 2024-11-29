<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil input dari form
    $ruangan = htmlspecialchars(trim($_POST['ruangan']));

    // Memastikan bahwa nama ruangan tidak kosong
    if (empty($ruangan)) {
        header("Location: tambah.php?error=" . urlencode("Nama ruangan tidak boleh kosong."));
        exit;
    }

    // Siapkan query untuk menambahkan ruangan
    $query = $conn->prepare("INSERT INTO ruang (ruangan) VALUES (?)");
    $query->bind_param("s", $ruangan); // Pastikan formatnya sesuai

    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Ruangan berhasil ditambahkan."));
    } else {
        header("Location: tambah.php?error=" . urlencode("Gagal menambahkan ruangan: " . $query->error));
    }

    $query->close();
}
?>