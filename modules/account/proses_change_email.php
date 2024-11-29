<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Ambil detail pengguna dari session
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = $_POST['new_email'];
    $password = $_POST['password'];

    // Ambil data pengguna dari database
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Pengguna tidak ditemukan
        header("Location: index.php");
        exit;
    }

    $user = $result->fetch_assoc();

    // Verifikasi kata sandi
    if (!password_verify($password, $user['password'])) {
        // Kata sandi salah
        $_SESSION['error'] = "Kata sandi tidak benar.";
        header("Location: change_email.php");
        exit;
    }

    // Update email di database
    $updateQuery = "UPDATE users SET email = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $newEmail, $userId);

    if ($updateStmt->execute()) {
        $_SESSION['success'] = "Email berhasil diubah.";
        header("Location: change_email.php");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat mengubah email.";
        header("Location: change_email.php");
    }
} else {
    // Jika bukan POST request, redirect ke halaman change email
    header("Location: change_email.php");
    exit;
}
?>