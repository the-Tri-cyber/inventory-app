<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki hak akses admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $merk = htmlspecialchars(trim($_POST['merk']));
    $id_kategori = htmlspecialchars(trim($_POST['id_kategori']));
    $id_kondisi = htmlspecialchars(trim($_POST['id_kondisi']));
    $id_ruangan = htmlspecialchars(trim($_POST['id_ruangan']));
    $stok = intval($_POST['stok']);
    $harga_satuan = floatval($_POST['harga_satuan']);
    $asal_perolehan = htmlspecialchars(trim($_POST['asal_perolehan']));

    // Menangani pengunggahan gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileSize = $_FILES['gambar']['size'];
        $fileType = $_FILES['gambar']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validasi ekstensi file
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Buat nama file baru dengan hash
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '../uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            // Pindahkan file ke direktori tujuan
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $gambar = $newFileName; // Simpan path gambar
            } else {
                $error_message = "Terjadi kesalahan saat mengunggah gambar.";
                header("Location: tambah.php?error=" . urlencode($error_message));
                exit;
            }
        } else {
            $error_message = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        }
    } else {
        $error_message = "Terjadi kesalahan saat mengunggah file.";
    }

    // Jika ada kesalahan, redirect ke halaman tambah.php dengan pesan kesalahan
    if (!empty($error_message)) {
        header("Location: tambah.php?error=" . urlencode($error_message));
        exit;
    }

    // Query untuk menyimpan data barang
    $query = $conn->prepare("INSERT INTO barang (nama_barang, merk, id_kategori, id_kondisi, id_ruangan, stok, harga_satuan, asal_perolehan, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssiiissss", $nama_barang, $merk, $id_kategori, $id_kondisi, $id_ruangan, $stok, $harga_satuan, $asal_perolehan, $gambar);

    if ($query->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error_message = "Error: " . $query->error;
        header("Location: tambah.php?error=" . urlencode($error_message));
        exit;
    }
}
?>