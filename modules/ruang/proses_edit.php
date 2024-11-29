<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_ruangan = intval($_POST['id_ruangan']);
    $ruangan = htmlspecialchars(trim($_POST['ruangan']));

    // Memastikan bahwa nama ruangan tidak kosong
    if (empty($ruangan)) {
        header("Location: edit.php?id=$id_ruangan&error=" . urlencode("Nama ruangan tidak boleh kosong."));
        exit;
    }

    // Query untuk update data
    $query = $conn->prepare("UPDATE ruang SET ruangan = ? WHERE id_ruangan = ?");
    $query->bind_param("si", $ruangan, $id_ruangan);

    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Ruangan berhasil diperbarui."));
    } else {
        header("Location: edit.php?id=$id_ruangan&error=" . urlencode("Gagal memperbarui ruangan: " . $query->error));
    }

    $query->close();
}
?>