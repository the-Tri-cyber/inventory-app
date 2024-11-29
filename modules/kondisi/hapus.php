<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

$id_kondisi = intval($_GET['id']);

// Cek apakah ID valid
if ($id_kondisi <= 0) {
    header("Location: index.php");
    exit;
}

// Menangani penghapusan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $conn->prepare("DELETE FROM kondisi WHERE id_kondisi = ?");
    $query->bind_param("i", $id_kondisi);
    
    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Kondisi berhasil dihapus."));
        exit;
    } else {
        $error_message = "Error: " . $query->error;
    }
}

// Set title dan content untuk layout
$title = "Hapus Kondisi";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Hapus Kondisi</h1>
        <p>Apakah Anda yakin ingin menghapus kondisi ini?</p>
        <form action="" method="POST">
            <input type="hidden" name="id_kondisi" value="' . $id_kondisi . '">
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