<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

$id = intval($_GET['id']);
$query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Set title dan content untuk layout
$title = "Edit Pengguna";
$content = '
    <h1 class="mb-4">Edit Pengguna</h1>
    <form action="proses_edit.php" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="id" value="' . $user['id'] . '"> 
        
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="' . htmlspecialchars($user['username']) . '" required>
            <div class="invalid-feedback">Username harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="' . htmlspecialchars($user['email']) . '" required>
            <div class="invalid-feedback">Email harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <select class="form-select" id="role" name="role" required>
                <option value="admin" ' . ($user['role'] === 'admin' ? 'selected' : '') . '>Admin</option>
                <option value="manager" ' . ($user['role'] === 'manager' ? 'selected' : '') . '>Manager</option>
                <option value="user" ' . ($user['role'] === 'user' ? 'selected' : '') . '>User  </option>
            </select>
            <div class="invalid-feedback">Role harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="gambar" class="form-label">Foto Profil: </label>
            <div>';
            if (!empty($user['gambar'])) {
                $content .= '<img src="../uploads/users/' . htmlspecialchars($user['gambar']) . '" alt="Gambar" style="width: 100px; height: auto; display: block; margin-bottom: 10px;">';
            } else {
                $content .= 'Tidak ada foto profil saat ini.';
            }
            $content .= '</div>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            <div class="invalid-feedback">Silakan pilih gambar jika ingin mengunggahnya.</div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
';

// Menangkap pesan kesalahan dari proses_tambah.php jika ada
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
} else {
    $error_message = '';
}

include '../../views/layout.php';
?>

<!-- Modal untuk menampilkan pesan kesalahan -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Kesalahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo $error_message; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript untuk validasi form
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')

        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })

        // Menampilkan modal jika ada pesan kesalahan
        <?php if (!empty($error_message)): ?>
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        <?php endif; ?>
    })()
</script>