<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Set title dan content untuk layout
$title = "Tambah Kondisi";
$content = '
    <h1 class="mb-4">Tambah Kondisi</h1>
    <form action="proses_tambah.php" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="kondisi" class="form-label">Kondisi:</label>
            <input type="text" class="form-control" id="kondisi" name="kondisi" required>
            <div class="invalid-feedback">Kondisi harus diisi.</div>
        </div>

        <button type="submit" class="btn btn-primary">Tambah</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
';

include '../../views/layout.php';
?>

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
    })()
</script>