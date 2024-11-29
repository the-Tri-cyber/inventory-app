<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Periksa apakah data yang diperlukan ada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['jenis'], $_POST['jumlah'], $_POST['tanggal'])) {
        $id = intval($_POST['id']);
        $jenis = htmlspecialchars($_POST['jenis']);
        $jumlah = intval($_POST['jumlah']);
        $tanggal = htmlspecialchars($_POST['tanggal']);

        // Query untuk memperbarui data transaksi
        $query = "
            UPDATE transaksi 
            SET jenis = ?, 
                jumlah = ?, 
                tanggal = ?
            WHERE id = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisi", $jenis, $jumlah, $tanggal, $id);

        if ($stmt->execute()) {
            header("Location: index.php?message=Transaksi berhasil diperbarui");
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Data tidak lengkap.";
    }
} else {
    header("Location: index.php");
    exit;
}
?>