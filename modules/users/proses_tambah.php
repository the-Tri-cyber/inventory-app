<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki hak akses admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil dan membersihkan input
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash password
    $role = htmlspecialchars(trim($_POST['role']));
    $gambar = ''; // Inisialisasi variabel gambar dengan string kosong

    // Memastikan bahwa semua kolom yang diperlukan ada
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error_message = "Semua kolom harus diisi.";
        header("Location: tambah.php?error=" . urlencode($error_message));
        exit;
    }

    // Memeriksa apakah email sudah ada
    $query = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Jika email sudah ada, alihkan dengan pesan kesalahan
        $error_message = "Email sudah terdaftar.";
        header("Location: tambah.php?error=" . urlencode($error_message));
        exit;
    }

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
            $uploadFileDir = '../uploads/users/';
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
            $error_message = "Hanya file gambar yang diperbolehkan.";
            header("Location: tambah.php?error=" . urlencode($error_message));
            exit;
        }
    }

    // Menyimpan pengguna baru ke database
    // Jika tidak ada gambar, gunakan string kosong
    $query = $conn->prepare("INSERT INTO users (username, email, password, role, gambar) VALUES (?, ?, ?, ?, ?)");
    $query->bind_param("sssss", $username, $email, $password, $role, $gambar);

    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Pengguna berhasil ditambahkan."));
        exit;
    } else {
        // Menangani kesalahan saat eksekusi query
        $error_message = "Gagal menambahkan pengguna: " . $query->error;
        header("Location: tambah.php?error=" . urlencode($error_message));
        exit;
    }
}

// Menutup koneksi
$conn->close();
?>