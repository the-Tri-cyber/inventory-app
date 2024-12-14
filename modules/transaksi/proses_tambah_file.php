<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $data = $spreadsheet->getActiveSheet()->toArray();

        // Mulai transaksi
        $conn->begin_transaction();
        foreach ($data as $index => $row) {
            if ($index === 0) continue; // Lewati header

            // Validasi data
            $tanggal = trim($row[0]);
            $nomer_surat_jalan = isset($row[1]) ? htmlspecialchars(trim($row[1])) : '';
            $id_barang = isset($row[2]) && is_numeric($row[2]) ? intval($row[2]) : null;
            $jenis = isset($row[3]) ? htmlspecialchars(trim($row[3])) : null;
            $jumlah = isset($row[4]) && is_numeric($row[4]) ? intval($row[4]) : null;
            $id_kondisi = isset($row[5]) && is_numeric($row[5]) ? intval($row[5]) : null;

            // Lewati baris jika kolom penting kosong
            if (!$tanggal || !$id_barang || !$jenis || !$jumlah || !$id_kondisi) {
                continue; // Baris tidak valid, lewati
            }

            // Konversi tanggal
            if (ExcelDate::isDateTimeFormatCode($tanggal)) {
                $tanggal_obj = ExcelDate::excelToDateTimeObject(floatval($tanggal));
                $tanggal = $tanggal_obj->format('Y-m-d H:i:s');
            }

            // Validasi stok jika barang ada
            $query_stok = $conn->prepare("SELECT stok FROM barang WHERE id = ?");
            $query_stok->bind_param("i", $id_barang);
            $query_stok->execute();
            $result_stok = $query_stok->get_result();
            $barang = $result_stok->fetch_assoc();

            if (!$barang) {
                throw new Exception("Barang dengan ID $id_barang tidak ditemukan di baris " . ($index + 1));
            }

            $stok_sekarang = $barang['stok'];
            if ($jenis === 'keluar' && $jumlah > $stok_sekarang) {
                throw new Exception("Stok tidak mencukupi untuk transaksi keluar di baris " . ($index + 1));
            }

            $stok_baru = ($jenis === 'masuk') ? $stok_sekarang + $jumlah : $stok_sekarang - $jumlah;

            // Update stok
            $query_update_stok = $conn->prepare("UPDATE barang SET stok = ? WHERE id = ?");
            $query_update_stok->bind_param("ii", $stok_baru, $id_barang);
            $query_update_stok->execute();

            // Simpan transaksi
            $query_tambah_transaksi = $conn->prepare("
                INSERT INTO transaksi (id_barang, jenis, jumlah, id_kondisi, nomer_surat_jalan, tanggal) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $query_tambah_transaksi->bind_param("isiiss", $id_barang, $jenis, $jumlah, $id_kondisi, $nomer_surat_jalan, $tanggal);
            $query_tambah_transaksi->execute();
        }

        // Commit transaksi
        $conn->commit();
        header("Location: index.php");
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
