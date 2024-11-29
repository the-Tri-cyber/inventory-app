<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Ambil detail pengguna dari database
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$user = $result->fetch_assoc();

// Menyiapkan variabel untuk layout
$title = "Reset Password";
$content = '';

// Menampilkan pesan kesalahan atau sukses
if (isset($_SESSION['error'])) {
    $content .= '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']); // Hapus pesan setelah ditampilkan
}

if (isset($_SESSION['success'])) {
    $content .= '<div class="alert alert-success" role="alert">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']); // Hapus pesan setelah ditampilkan
}

$content .= '
    <form action="proses_change_pw.php" method="POST">
        <input type="hidden" name="id" value="' . $user['id'] . '">
        <div class="mb-3">
            <label for="old_password" class="form-label">Old Password</label>
            <input type="password" class="form-control" id="old_password" name="old_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new _password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-danger">Reset Password</button>
    </form>
';

// Menyertakan layout
include '../../views/layout.php';
?>