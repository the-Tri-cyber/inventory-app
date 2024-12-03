<?php
session_start();
include '../../config/db.php';

// Set waktu kedaluwarsa sesi (1 hari dalam detik)
$session_duration = 86400; // 24 jam * 60 menit * 60 detik

// Cek apakah sesi sudah ada dan apakah sesi telah kedaluwarsa
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_duration) {
    // Jika sesi sudah kedaluwarsa, hapus sesi
    session_unset(); // Menghapus semua variabel sesi
    session_destroy(); // Menghancurkan sesi
    header("Location: login.php?message=session_expired"); // Redirect ke halaman login dengan pesan
    exit;
}

// Update waktu terakhir aktivitas sesi
$_SESSION['last_activity'] = time();

// Validasi token CSRF
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
        header("Location: login.php");
        exit;
    }

    // Mengambil data email dan password dari form
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (!empty($email) && !empty($password)) {
        // Menggunakan email untuk proses login
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Login berhasil
                $_SESSION['username'] = $user['username']; // Menyimpan username
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['gambar'] = $user['gambar'];

                // Redirect berdasarkan peran pengguna
                if ($user['role'] === 'admin' || $user['role'] === 'manager') {
                    header("Location: ../../index.php"); // Halaman untuk admin dan manager
                } else {
                    header("Location: ../../modules/dashboard.php"); // Halaman untuk pengguna biasa
                }
                exit;
            } else {
                $_SESSION['error_message'] = "Password salah!";
            }
        } else {
            $_SESSION['error_message'] = "Email tidak ditemukan!";
        }
    } else {
        $_SESSION['error_message'] = "Email dan password tidak boleh kosong!";
    }

    header("Location: login.php");
    exit;
}
?>