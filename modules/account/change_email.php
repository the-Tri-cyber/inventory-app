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
$title = "Change Email";
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
    <form action="proses_change_email.php" method="POST">
        <input type="hidden" name="id" value="' . $user['id'] . '">
        <div class="mb-3">
            <label for="new_email" class="form-label">New Email</label>
            <input type="email" class="form-control" id="new_email" name="new_email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-warning">Change Email</button>
    </form>
';

// Menyertakan layout
include '../../views/layout.php';

// Modal untuk menampilkan pesan kesalahan
if (isset($_SESSION['error'])) {
    echo '
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ' . $_SESSION['error'] . '
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    ';
    unset($_SESSION['error']);
}

// Modal untuk menampilkan pesan sukses
if (isset($_SESSION['success'])) {
    echo '
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ' . $_SESSION['success'] . '
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    ';
    unset($_SESSION['success']);
}

?>

<script>
    // Menampilkan modal jika ada pesan kesalahan atau sukses
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    <?php if (isset($_SESSION['error'])): ?>
        errorModal.show();
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        successModal.show();
    <?php endif; ?>
</script>