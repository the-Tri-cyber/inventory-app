<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $id_barang = intval($_POST['id_barang']);
    $jenis = htmlspecialchars($_POST['jenis']);
    $jumlah = intval($_POST['jumlah']);
    $id_kondisi = intval($_POST['id_kondisi']); // Ubah menjadi integer
    $nomer_surat_jalan = htmlspecialchars(trim($_POST['nomer_surat_jalan']));
    $tanggal = htmlspecialchars($_POST['tanggal']); // Format datetime-local

    // Validasi input jumlah
    if ($jumlah <= 0) {
        echo "Jumlah harus lebih besar dari 0.";
        exit;
    }

    // Ambil stok saat ini
    $query_stok = $conn->prepare("SELECT stok FROM barang WHERE id = ?");
    $query_stok->bind_param("i", $id_barang);
    $query_stok->execute();
    $result_stok = $query_stok->get_result();
    $barang = $result_stok->fetch_assoc();

    if (!$barang) {
        echo "Barang tidak ditemukan.";
        exit;
    }

    $stok_sekarang = $barang['stok'];

    // Perbarui stok berdasarkan jenis transaksi
    if ($jenis === 'masuk') {
        $stok_baru = $stok_sekarang + $jumlah;
    } elseif ($jenis === 'keluar') {
        if ($jumlah > $stok_sekarang) {
            echo "Stok tidak mencukupi untuk transaksi keluar.";
            exit;
        }
        $stok_baru = $stok_sekarang - $jumlah;
    } else {
        echo "Jenis transaksi tidak valid.";
        exit;
    }

    // Mulai transaksi database
    $conn->begin_transaction();
    try {
        // Perbarui stok di tabel barang
        $query_update_stok = $conn->prepare("UPDATE barang SET stok = ? WHERE id = ?");
        $query_update_stok->bind_param("ii", $stok_baru, $id_barang);
        $query_update_stok->execute();

        // Tambahkan transaksi ke tabel transaksi
        $query_tambah_transaksi = $conn->prepare("
            INSERT INTO transaksi (id_barang, jenis, jumlah, id_kondisi, nomer_surat_jalan, tanggal) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $query_tambah_transaksi->bind_param("isiiss", $id_barang, $jenis, $jumlah, $id_kondisi, $nomer_surat_jalan, $tanggal);
        $query_tambah_transaksi->execute();

        // Commit transaksi
        $conn->commit();
        header("Location: index.php");
    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
