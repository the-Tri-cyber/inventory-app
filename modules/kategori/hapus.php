<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

$id_kategori = intval($_GET['id']);

// Cek apakah ID valid
if ($id_kategori <= 0) {
    header("Location: index.php");
    exit;
}

// Menangani penghapusan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
    $query->bind_param("i", $id_kategori);
    
    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Kategori berhasil dihapus."));
        exit;
    } else {
        $error_message = "Error: " . $query->error;
    }
}

// Set title dan content untuk layout
$title = "Hapus Kategori";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Hapus kategori</h1>
        <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
        <form action="" method="POST">
            <input type="hidden" name="id_kategori$id_kategori" value="' . $id_kategori . '">
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