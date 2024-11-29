<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $merk = htmlspecialchars(trim($_POST['merk']));
    $id_kategori = htmlspecialchars(trim($_POST['id_kategori']));
    $id_kondisi = htmlspecialchars(trim($_POST['id_kondisi']));
    $id_ruangan = htmlspecialchars(trim($_POST['id_ruangan']));
    $stok = intval($_POST['stok']);
    $harga_satuan = floatval($_POST['harga_satuan']);
    $asal_perolehan = htmlspecialchars(trim($_POST['asal_perolehan']));

    // Proses upload gambar jika ada
    $gambar = null; // Inisialisasi variabel gambar
    $uploadFileDir = '../uploads/'; // Pastikan direktori ini ada dan dapat ditulis

    // Ambil gambar lama dari database
    $query = $conn->prepare("SELECT gambar FROM barang WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $barang = $result->fetch_assoc();
    $oldImagePath = $barang['gambar'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validasi ekstensi file
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Buat nama file baru dengan hash
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            // Pindahkan file ke direktori tujuan
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $gambar = $newFileName; // Simpan hanya nama file baru
            } else {
                $error_message = "Terjadi kesalahan saat mengunggah gambar.";
                header("Location: edit.php?id=$id&error=" . urlencode($error_message));
                exit;
            }
        } else {
            $error_message = "Hanya file gambar yang diperbolehkan.";
            header("Location: edit.php?id=$id&error=" . urlencode($error_message));
            exit;
        }
    }

    // Hapus gambar lama jika ada
    if ($oldImagePath && file_exists($uploadFileDir . $oldImagePath)) {
        unlink($uploadFileDir . $oldImagePath); // Menghapus file gambar lama
    }

    // Siapkan query untuk update barang
    if ($gambar) {
        // Jika gambar baru diupload, update gambar di database
        $query = $conn->prepare("UPDATE barang SET nama_barang = ?, merk = ?, id_kategori = ?, id_kondisi = ?, id_ruangan = ?, stok = ?, harga_satuan = ?, asal_perolehan = ?, gambar = ?, updated_at = NOW() WHERE id = ?");
        $query->bind_param("ssiiissssi", $nama_barang, $merk, $id_kategori, $id_kondisi, $id_ruangan, $stok, $harga_satuan, $asal_perolehan, $gambar, $id);
    } else {
        // Jika tidak ada gambar baru, update tanpa gambar
        $query = $conn->prepare("UPDATE barang SET nama_barang = ?, merk = ?, id_kategori = ?, id_kondisi = ?, id_ruangan = ?, stok = ?, harga_satuan = ?, asal_perolehan = ?, updated_at = NOW() WHERE id = ?");
        $query->bind_param("ssiiisssi", $nama_barang, $merk, $id_kategori, $id_kondisi, $id_ruangan, $stok, $harga_satuan, $asal_perolehan, $id);
    }

    // Eksekusi query
    if ($query->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error_message = "Error: " . $query->error;
        header("Location: edit.php?id=" . $id . "&error=" . urlencode($error_message));
        exit;
    }
}
?>