<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

$id_ruangan = intval($_GET['id']);

// Cek apakah ID valid
if ($id_ruangan <= 0) {
    header("Location: index.php");
    exit;
}

// Menangani penghapusan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $conn->prepare("DELETE FROM ruang WHERE id_ruangan = ?");
    $query->bind_param("i", $id_ruangan);
    
    if ($query->execute()) {
        header("Location: index.php?success=" . urlencode("Ruangan berhasil dihapus."));
        exit;
    } else {
        $error_message = "Error: " . $query->error;
    }
}

// Set title dan content untuk layout
$title = "Hapus Ruangan";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Hapus Ruangan</h1>
        <p>Apakah Anda yakin ingin menghapus ruangan ini?</p>
        <form action="" method="POST">
            <input type="hidden" name="id_ruangan" value="' . $id_ruangan . '">
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