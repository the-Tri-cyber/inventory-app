<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

$id = intval($_GET['id']);

// Cek apakah ID valid
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil detail transaksi yang akan dihapus
$query_transaksi = $conn->prepare("SELECT id_barang, jenis, jumlah FROM transaksi WHERE id = ?");
$query_transaksi->bind_param("i", $id);
$query_transaksi->execute();
$result_transaksi = $query_transaksi->get_result();
$transaksi = $result_transaksi->fetch_assoc();

if (!$transaksi) {
    header("Location: index.php?message=Transaksi tidak ditemukan");
    exit;
}

// Menangani penghapusan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = $transaksi['id_barang'];
    $jenis = $transaksi['jenis'];
    $jumlah = $transaksi['jumlah'];

    // Ambil stok barang saat ini
    $query_stok = $conn->prepare("SELECT stok FROM barang WHERE id = ?");
    $query_stok->bind_param("i", $id_barang);
    $query_stok->execute();
    $result_stok = $query_stok->get_result();
    $barang = $result_stok->fetch_assoc();

    if (!$barang) {
        header("Location: index.php?message=Barang tidak ditemukan");
        exit;
    }

    $stok_sekarang = $barang['stok'];

    // Kembalikan stok berdasarkan jenis transaksi
    if ($jenis === 'masuk') {
        $stok_baru = $stok_sekarang - $jumlah; // Kurangi stok
    } elseif ($jenis === 'keluar') {
        $stok_baru = $stok_sekarang + $jumlah; // Tambah stok
    } else {
        header("Location: index.php?message=Jenis transaksi tidak valid");
        exit;
    }

    // Mulai transaksi database
    $conn->begin_transaction();
    try {
        // Perbarui stok barang
        $query_update_stok = $conn->prepare("UPDATE barang SET stok = ? WHERE id = ?");
        $query_update_stok->bind_param("ii", $stok_baru, $id_barang);
        $query_update_stok->execute();

        // Hapus transaksi
        $query_hapus_transaksi = $conn->prepare("DELETE FROM transaksi WHERE id = ?");
        $query_hapus_transaksi->bind_param("i", $id);
        $query_hapus_transaksi->execute();

        // Commit transaksi
        $conn->commit();
        header("Location: index.php?message=Transaksi berhasil dihapus");
        exit;
    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

// Set title dan content untuk layout
$title = "Hapus Transaksi";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Hapus Transaksi</h1>
        <p>Apakah Anda yakin ingin menghapus transaksi ini?</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="' . $id . '">
            <button type="submit" class="btn btn-danger">Hapus</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
';

include '../../views/layout.php';
?>
