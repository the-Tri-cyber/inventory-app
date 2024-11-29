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
$title = "Account Details"; // Menggunakan variabel $title untuk layout utama
$content = '
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="../uploads/users/' . htmlspecialchars($user['gambar']) . '" class="card-img-top" alt="Profile Picture" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($user['username']) . '</h5>
                    <p class="card-text">Email: ' . htmlspecialchars($user['email']) . '</p>
                    <p class="card-text">Role: ' . htmlspecialchars($user['role']) . '</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <form action="proses_edit_account.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="' . $user['id'] . '">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="' . htmlspecialchars($user['username']) . '" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label"> Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="' . htmlspecialchars($user['email']) . '" readonly>
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="gambar" name="gambar">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                </div>
                <button type="submit" class="btn btn-primary">Update Account</button>
            </form>
        </div>
    </div>
';

// Menyertakan layout utama
include '../../views/layout.php';
?>