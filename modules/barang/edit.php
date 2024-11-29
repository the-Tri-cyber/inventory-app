<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

$id = intval($_GET['id']);
$query = $conn->prepare("SELECT * FROM barang WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$barang = $result->fetch_assoc();

// Ambil data kategori
$kategori_query = $conn->query("SELECT * FROM kategori");
$kategori_options = '';
while ($row = $kategori_query->fetch_assoc()) {
    // Tandai opsi yang sesuai dengan id_kategori dari barang
    $selected = ($row['id_kategori'] == $barang['id_kategori']) ? 'selected' : '';
    $kategori_options .= '<option value="' . $row['id_kategori'] . '" ' . $selected . '>' . htmlspecialchars($row['kategori']) . '</option>';
}

// Ambil data kondisi
$kondisi_query = $conn->query("SELECT * FROM kondisi");
$kondisi_options = '';
while ($row = $kondisi_query->fetch_assoc()) {
    // Tandai opsi yang sesuai dengan id_kondisi dari barang
    $selected = ($row['id_kondisi'] == $barang['id_kondisi']) ? 'selected' : '';
    $kondisi_options .= '<option value="' . $row['id_kondisi'] . '" ' . $selected . '>' . htmlspecialchars($row['kondisi']) . '</option>';
}

// Ambil data ruang
$ruang_query = $conn->query("SELECT * FROM ruang");
$ruang_options = '';
while ($row = $ruang_query->fetch_assoc()) {
    // Tandai opsi yang sesuai dengan id_ruangan dari barang
    $selected = ($row['id_ruangan'] == $barang['id_ruangan']) ? 'selected' : '';
    $ruang_options .= '<option value="' . $row['id_ruangan'] . '" ' . $selected . '>' . htmlspecialchars($row['ruangan']) . '</option>';
}

// Set title dan content untuk layout
$title = "Edit Barang";
$content = '
    <h1 class="mb-4">Edit Barang</h1>
    <form action="proses_edit.php" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="id" value="' . $barang['id'] . '">
        
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang:</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="' . htmlspecialchars($barang['nama_barang']) . '" required>
            <div class="invalid-feedback">Nama barang harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="merk" class="form-label">Merk:</label>
            <input type="text" class="form-control" id="merk" name="merk" value="' . htmlspecialchars($barang['merk']) . '" required>
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
            <label for="id_kondisi" class="form-label">ID Kondisi:</label>
            <select class="form-select" id="id_kondisi" name="id_kondisi" required>
                <option value="">Pilih Kondisi</option>
                ' . $kondisi_options . '
            </select>
            <div class="invalid-feedback">ID Kondisi harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="id_ruangan" class="form-label">ID Ruangan:</label>
            <select class ="form-select" id="id_ruangan" name="id_ruangan" required>
                <option value="">Pilih Ruang</option>
                ' . $ruang_options . '
            </select>
            <div class="invalid-feedback">ID Ruang harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="stok" class="form-label">Stok:</label>
            <input type="number" class="form-control" id="stok" name="stok" value="' . $barang['stok'] . '" required>
            <div class="invalid-feedback">Stok harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="harga_satuan" class="form-label">Harga Satuan:</label>
            <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" step="0.01" value="' . $barang['harga_satuan'] . '" required>
            <div class="invalid-feedback">Harga satuan harus diisi.</div>
        </div>
        
        <div class="mb-3">
            <label for="asal_perolehan" class="form-label">Asal Perolehan:</label>
            <input type="text" class="form-control" id="asal_perolehan" name="asal_perolehan" value="' . htmlspecialchars($barang['asal_perolehan']) . '" required>
            <div class="invalid-feedback">Asal perolehan harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar: </label>
            <div>';
            if (!empty($barang['gambar'])) {
                $content .= '<img src="../uploads/' . htmlspecialchars($barang['gambar']) . '" alt="Gambar" style="width: 100px; height: auto; display: block; margin-bottom: 10px;">';
            } else {
                $content .= 'Tidak ada gambar saat ini.';
            }
            $content .= '</div>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            <div class="invalid-feedback">Silakan pilih gambar jika ingin mengunggahnya.</div>
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
';

// Menangkap pesan kesalahan dari proses_edit.php jika ada
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/inventory-app/bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
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