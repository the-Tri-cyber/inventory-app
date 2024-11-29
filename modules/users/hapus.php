<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
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

// Ambil nama file gambar dari database sebelum menghapus
$query = $conn->prepare("SELECT gambar FROM users WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $gambarPath = '../uploads/users/' . $user['gambar']; // Path lengkap ke file gambar

    // Menangani penghapusan
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Hapus file gambar jika ada
        if ($user['gambar'] && file_exists($gambarPath)) {
            unlink($gambarPath); // Menghapus file gambar
        }

        // Hapus baris dari tabel users
        $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteQuery->bind_param("i", $id);
        
        if ($deleteQuery->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $error_message = "Error: " . $deleteQuery->error;
        }
    }
} else {
    $error_message = "Pengguna tidak ditemukan.";
}

// Set title dan content untuk layout
$title = "Hapus Pengguna";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Hapus Pengguna</h1>
        <p>Apakah Anda yakin ingin menghapus pengguna ini?</p>
        <form action="" method="POST">
            <input type="hidden" name="id" value="' . $id . '">
            <button type="submit" class="btn btn-danger">Hapus</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
';

if (isset($error_message)) {
    $content .= '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
}

include '../../views/layout.php';
?>