<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['jenis'], $_POST['jumlah'], $_POST['tanggal'], $_POST['id_kondisi'], $_POST['nomer_surat_jalan'])) {
        $id = intval($_POST['id']);
        $jenisBaru = htmlspecialchars($_POST['jenis']);
        $jumlahBaru = intval($_POST['jumlah']);
        $idKondisi = intval($_POST['id_kondisi']);
        $nomerSuratJalan = htmlspecialchars($_POST['nomer_surat_jalan']);
        $tanggalBaru = htmlspecialchars($_POST['tanggal']);

        // Ambil data transaksi lama
        $queryLama = "SELECT id_barang, jenis, jumlah FROM transaksi WHERE id = ?";
        $stmtLama = $conn->prepare($queryLama);
        $stmtLama->bind_param("i", $id);
        $stmtLama->execute();
        $resultLama = $stmtLama->get_result();

        if ($resultLama->num_rows === 0) {
            echo "Transaksi tidak ditemukan.";
            exit;
        }

        $transaksiLama = $resultLama->fetch_assoc();
        $idBarang = $transaksiLama['id_barang'];
        $jenisLama = $transaksiLama['jenis'];
        $jumlahLama = $transaksiLama['jumlah'];

        // Hitung perubahan stok
        $stokChange = 0;
        if ($jenisLama === 'keluar') {
            $stokChange += $jumlahLama; // Kembalikan stok yang dikurangi sebelumnya
        } else if ($jenisLama === 'masuk') {
            $stokChange -= $jumlahLama; // Kurangi stok yang ditambahkan sebelumnya
        }

        if ($jenisBaru === 'keluar') {
            $stokChange -= $jumlahBaru; // Kurangi stok sesuai jumlah baru
        } else if ($jenisBaru === 'masuk') {
            $stokChange += $jumlahBaru; // Tambahkan stok sesuai jumlah baru
        }

        // Perbarui stok barang
        $queryUpdateStok = "UPDATE barang SET stok = stok + ? WHERE id = ?";
        $stmtUpdateStok = $conn->prepare($queryUpdateStok);
        $stmtUpdateStok->bind_param("ii", $stokChange, $idBarang);
        if (!$stmtUpdateStok->execute()) {
            echo "Error saat memperbarui stok: " . $stmtUpdateStok->error;
            exit;
        }

        // Perbarui data transaksi
        $queryUpdateTransaksi = "
            UPDATE transaksi 
            SET jenis = ?, 
                jumlah = ?, 
                id_kondisi = ?,
                nomer_surat_jalan = ?,
                tanggal = ?
            WHERE id = ?
        ";
        $stmtUpdateTransaksi = $conn->prepare($queryUpdateTransaksi);
        $stmtUpdateTransaksi->bind_param("siisss", $jenisBaru, $jumlahBaru, $idKondisi, $nomerSuratJalan, $tanggalBaru, $id);

        if ($stmtUpdateTransaksi->execute()) {
            header("Location: index.php?message=Transaksi berhasil diperbarui");
        } else {
            echo "Error: " . $stmtUpdateTransaksi->error;
        }
    } else {
        echo "Data tidak lengkap.";
    }
} else {
    header("Location: index.php");
    exit;
}
?>
