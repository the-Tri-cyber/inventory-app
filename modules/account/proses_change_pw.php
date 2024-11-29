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
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

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

    // Verifikasi kata sandi lama
    if (!password_verify($oldPassword, $user['password'])) {
        // Kata sandi lama salah
        $_SESSION['error'] = "Kata sandi lama tidak benar.";
        header("Location: reset_password.php");
        exit;
    }

    // Periksa kesesuaian kata sandi baru dan konfirmasi
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Kata sandi baru dan konfirmasi tidak cocok.";
        header("Location: reset_password.php");
        exit;
    }

    // Hash kata sandi baru
    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update kata sandi di database
    $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $hashedNewPassword, $userId);

    if ($updateStmt->execute()) {
        $_SESSION['success'] = "Kata sandi berhasil diubah.";
        header("Location: reset_password.php");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat mengubah kata sandi.";
        header("Location: reset_password.php");
    }
} else {
    // Jika bukan POST request, redirect ke halaman reset password
    header("Location: reset_password.php");
    exit;
}
?>