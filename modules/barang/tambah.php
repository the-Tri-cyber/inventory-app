<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Ambil data kategori
$kategori_query = $conn->query("SELECT * FROM kategori");
$kategori_options = '';
while ($row = $kategori_query->fetch_assoc()) {
    $kategori_options .= '<option value="' . $row['id_kategori'] . '">' . htmlspecialchars($row['kategori']) . '</option>';
}

// Ambil data ruang
$ruang_query = $conn->query("SELECT * FROM ruang");
$ruang_options = '';
while ($row = $ruang_query->fetch_assoc()) {
    $ruang_options .= '<option value="' . $row['id_ruangan'] . '">' . htmlspecialchars($row['ruangan']) . '</option>';
}

// Set title dan content untuk layout
$title = "Tambah Item";
$content = '
    <h1 class="mb-4">Tambah Item</h1>
    <form action="proses_tambah.php" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Item:</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
            <div class="invalid-feedback">Nama item harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="merk" class="form-label">Merk:</label>
            <input type="text" class="form-control" id="merk" name="merk" required>
            <div class="invalid-feedback">Merk harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="id_kategori" class="form-label">ID Kategori:</label>
            <select class="form-select" id="id_kategori" name="id_kategori" required>
                <option value="">Pilih Kategori</option>
                ' . $kategori_options . '
            </select>
            <div class="invalid-feedback">ID Kategori harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="id_ruangan" class="form-label">ID Ruang:</label>
            <select class="form-select" id="id_ruangan" name="id_ruangan" required>
                <option value="">Pilih Ruang</option>
                ' . $ruang_options . '
            </select>
            <div class="invalid-feedback">ID Ruang harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="stok" class="form-label">Stok:</label>
            <input type="number" class="form-control" id="stok" name="stok" required>
            <div class="invalid-feedback">Stok harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="asal_perolehan" class="form-label">Asal Perolehan:</label>
            <input type="text" class="form-control" id="asal_perolehan" name="asal_perolehan" required>
            <div class="invalid-feedback">Asal perolehan harus diisi.</div>
        </div>

        <button type="submit" class="btn btn-primary">Tambah</button>
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
                <h5 class="modal-title" id="errorModalLabel">Kesalahan </h5>
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