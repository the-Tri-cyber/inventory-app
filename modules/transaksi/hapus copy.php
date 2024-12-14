<?php
session_start();
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager')) {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = explode(',', $_POST['hapus_ids']); // Ambil ID dari POST dan pisahkan menjadi array

    // Mulai transaksi database
    $conn->begin_transaction();
    try {
        foreach ($ids as $id) {
            $id = intval($id); // Pastikan ID adalah integer

            // Ambil detail transaksi yang akan dihapus
            $query_transaksi = $conn->prepare("SELECT id_barang, jenis, jumlah FROM transaksi WHERE id = ?");
            $query_transaksi->bind_param("i", $id);
            $query_transaksi->execute();
            $result_transaksi = $query_transaksi->get_result();
            $transaksi = $result_transaksi->fetch_assoc();

            if (!$transaksi) {
                continue; // Jika transaksi tidak ditemukan, lanjutkan ke ID berikutnya
            }

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
                continue; // Jika barang tidak ditemukan, lanjutkan ke ID berikutnya
            }

            $stok_sekarang = $barang['stok'];

            // Kembalikan stok berdasarkan jenis transaksi
            if ($jenis === 'masuk') {
                $stok_baru = $stok_sekarang - $jumlah; // Kurangi stok
            } elseif ($jenis === 'keluar') {
                $stok_baru = $stok_sekarang + $jumlah; // Tambah stok
            } else {
                continue; // Jika jenis transaksi tidak valid, lanjutkan ke ID berikutnya
            }

            // Perbarui stok barang
            $query_update_stok = $conn->prepare("UPDATE barang SET stok = ? WHERE id = ?");
            $query_update_stok->bind_param("ii", $stok_baru, $id_barang);
            $query_update_stok->execute();

            // Hapus transaksi
            $query_hapus_transaksi = $conn->prepare("DELETE FROM transaksi WHERE id = ?");
            $query_hapus_transaksi->bind_param("i", $id);
            $query_hapus_transaksi->execute();
        }

        // Commit transaksi
        $conn->commit();
        header("Location: index.php?message=Transaksi berhasil dihapus");
        exit;
    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        $conn->rollback();
        header("Location: index.php?message=Terjadi kesalahan saat menghapus transaksi");
        exit;
    }
}
?>