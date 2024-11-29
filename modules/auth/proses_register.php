<?php
session_start();
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = 'user'; // Role ditetapkan sebagai 'user'

    // Cek apakah username atau email sudah ada
    $query = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $query->bind_param("ss", $username, $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Username atau email sudah terdaftar
        $_SESSION['error'] = "Username atau email sudah terdaftar.";
        header("Location: register.php");
        exit;
    } else {
        // Insert ke database
        $query = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $username, $email, $password, $role);

        if ($query->execute()) {
            $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
            header("Location: login.php");
        } else {
            $_SESSION['error'] = "Terjadi kesalahan. Silakan coba lagi.";
            header("Location: register.php");
        }
    }
}
?>