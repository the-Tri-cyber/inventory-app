<?php
session_start();

// Periksa autentikasi dan otorisasi
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: /inventory-app/modules/transaksi/user_transaksi.php");
    exit();
}

include '../../config/db.php';

// Pagination settings
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Handle reset
if (isset($_GET['reset'])) {
    unset($_SESSION['search']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&limit=$limit");
    exit;
}

// Fungsi untuk mendapatkan daftar transaksi dengan pagination
function getTransaksi($conn, $search = '', $offset = 0, $limit = 10) {
    $query = "
        SELECT t.id, b.nama_barang, t.jenis, t.jumlah, k.kondisi, t.nomer_surat_jalan, t.tanggal
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        JOIN kondisi k ON t.id_kondisi = k.id_kondisi
        WHERE b.nama_barang LIKE ? OR t.jenis LIKE ?
        ORDER BY t.tanggal DESC
        LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("ssii", $searchParam, $searchParam, $offset, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Fungsi untuk menghitung total transaksi
function getTotalTransaksi($conn, $search = '') {
    $query = "
        SELECT COUNT(*) as total
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        JOIN kondisi k ON t.id_kondisi = k.id_kondisi
        WHERE b.nama_barang LIKE ? OR t.jenis LIKE ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total'];
}

// Handle search
$search = isset($_POST['search']) ? $_POST['search'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['search'] = $search;
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&limit=$limit");
    exit;
} else {
    $search = isset($_SESSION['search']) ? $_SESSION['search'] : '';
}

// Ambil data transaksi dan totalnya
$result = getTransaksi($conn, $search, $offset, $limit);
$totalTransaksi = getTotalTransaksi($conn, $search);

// Hitung total halaman
$totalPages = ($totalTransaksi > 0) ? ceil($totalTransaksi / $limit) : 1;

// Menyiapkan variabel untuk layout
$title = "Daftar Transaksi";
$content = '
    <h1 class="mb-4">Daftar Transaksi</h1>
    <div class="d-flex justify-content-between">
        <div class="mb-3">
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-floppy me-2"></i>Tambah Transaksi
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="tambah.php">Tambah Manual</a></li>
                <li><a class="dropdown-item" href="tambah_file.php">Tambah Melalui File</a></li>
            </ul>
        </div>
        </div>
        <form method="POST" class="d-flex mb-3" role="search">
            <input type="search" name="search" class="form-control me-2" placeholder="Cari transaksi..." value="' . htmlspecialchars($search) . '">
            <button class="btn btn-outline-success me-2" type="submit"><i class="bi bi-search"></i></button>
            <a href="?reset=true&page=1&limit=' . $limit . '" class="btn btn-outline-warning"><i class="bi bi-arrow-clockwise"></i></a>
        </form>';

        // Menambahkan tombol Laporan
        if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'user') {
            $content .= '<div class="mb-3">
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#dateModal">
                <i class="bi bi-floppy me-2"></i>Laporan Transaksi
            </button>
        </div>
        </div>';
        }

$content .= '<form id="multiDeleteForm" method="POST" action="hapus.php">';

$content .= '
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>No</th>
                    <th>ID</th>
                    <th>Nama Item</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>No Surat Jalan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>';

$no = $offset + 1;
while ($row = $result->fetch_assoc()) {
    $content .= '
        <tr>
            <td><input type="checkbox" name="selected_ids[]" value="' . $row['id'] . '"></td>
            <td>' . $no++ . '</td>
            <td>' . $row['id'] . '</td>
            <td>' . htmlspecialchars($row['nama_barang']) . '</td>
            <td>' . htmlspecialchars($row['jenis']) . '</td>
            <td>' . $row['jumlah'] . '</td>
            <td>' . htmlspecialchars($row['kondisi']) . '</td>
            <td>' . htmlspecialchars($row['nomer_surat_jalan']) . '</td>
            <td>' . date('d-m-Y H:i:s', strtotime($row['tanggal'])) . '</td>';

            // Tampilkan kolom aksi hanya untuk Admin dan Manajer 
            if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager') {
                $content .= '<td>
                <a href="edit.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal" data-id="' . $row['id'] . '" onclick="setHapusId(this)">Hapus</button>
                </td>';
            } else {
                $content .= '<td></td>'; // Kosongkan kolom aksi untuk pengguna lain
            }

                    $content .= '</tr>';
            }

$content .= '
            </tbody>
        </table>
    </div>';

$content .= '<button type="button" class="btn btn-danger d-none" id="deleteSelected" data-bs-toggle="modal" data-bs-target="#hapusModal">
    Hapus Terpilih
</button>';

$content .= '</form>';

// Pagination controls
$content .= '<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span>Menampilkan ' . (($page - 1) * $limit + 1) . ' sampai ' . min($page * $limit, $totalTransaksi) . ' dari ' . $totalTransaksi . ' transaksi</span>
    </div>
    <div>
        <label for="limit">Records per page:</label>
        <select id="limit" onchange="changeLimit(this.value)">
            <option value="5" ' . ($limit == 5 ? 'selected' : '') . '>5</option>
            <option value="10" ' . ($limit == 10 ? 'selected' : '') . '>10</option>
            <option value="20" ' . ($limit == 20 ? 'selected' : '') . '>20</option>
            <option value="50" ' . ($limit == 50 ? 'selected' : '') . '>50</option>
        </select>
    </div>
</div>';

$content .= '<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">';
if ($page > 1) {
    $content .= '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '&limit=' . $limit . '&search=' . urlencode($search) . '">Previous</a></li>';
}
for ($i = 1; $i <= $totalPages; $i++) {
    $active = ($i == $page) ? 'active' : '';
    $content .= '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&limit=' . $limit . '&search=' . urlencode($search) . '">' . $i . '</a></li>';
}
if ($page < $totalPages) {
    $content .= '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '&limit=' . $limit . '&search=' . urlencode($search) . '">Next</a></li>';
}
$content .= '</ul>
</nav>';

// Modal untuk memilih tanggal dibuat
$content .= '
<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dateModalLabel">Pilih Tanggal untuk Laporan Berdasarkan Tanggal Dibuat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="../laporan/laporan_transaksi.php" target="_blank">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Dari Tanggal:</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Sampai Tanggal:</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <input type="hidden" name="search" value="' . htmlspecialchars($search) . '">
                    <input type="hidden" name="report_type" value="created">
                    <button type="submit" class="btn btn-primary">Buat Laporan</button>
                </form>
            </div>
        </div>
    </div>
</div>';

// Modal konfirmasi hapus
$content .= '
<div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hapusModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus transaksi ini?</p>
                <p id="hapusCount"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="hapus.php">
                    <input type="hidden" name="hapus_ids" id="hapus_ids">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>';

// Modal untuk menampilkan pesan
$content .= '
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>';

// Tambahkan script untuk menampilkan modal pesan
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    echo "<script>console.log('Message: $message');</script>"; // Debugging
    $content .= "
    <script>
        document.getElementById('messageContent').innerText = '$message';
        var messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
        messageModal.show();
    </script>";
}

$content .= '
<script>
document.getElementById("selectAll").addEventListener("click", function(e) {
    var checkboxes = document.querySelectorAll("input[type=\'checkbox\']");
    checkboxes.forEach(cb => cb.checked = e.target.checked);
    toggleDeleteButton();
});

document.querySelectorAll("input[name=\'selected_ids[]\']").forEach(cb => {
    cb.addEventListener("change", toggleDeleteButton);
});

document.getElementById("deleteSelected").addEventListener("click", function() {
    var checkboxes = document.querySelectorAll("input[name=\'selected_ids[]\']:checked");
    var selectedCount = checkboxes.length;
    if (selectedCount > 0) {
        document.getElementById("hapusCount").innerText = "Jumlah yang dipilih: " + selectedCount;
        var ids = Array.from(checkboxes).map(cb => cb.value).join(",");
        document.getElementById("hapus_ids").value = ids;
        $("#hapusModal").modal("show");
    } else {
        alert("Pilih setidaknya satu transaksi untuk dihapus.");
    }
});

function setHapusId(button) {
    var id = button.getAttribute("data-id"); // Ambil ID dari atribut data-id
    document.getElementById("hapus_ids").value = id; // Set ID ke input tersembunyi
    document.getElementById("hapusCount").innerText = "Anda akan menghapus transaksi dengan ID: " + id; // Tampilkan ID di modal
}

function changeLimit(value) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set("limit", value);
    urlParams.set("page", 1); // Reset to first page
    window.location.search = urlParams.toString();
    window.location = "?page=1&limit=" + value;
}

// Fungsi untuk menampilkan tombol Hapus Terpilih jika ada yang tercentang
function toggleDeleteButton() {
    var checkboxes = document.querySelectorAll("input[name=\'selected_ids[]\']:checked");
    var deleteButton = document.getElementById("deleteSelected");
    if (checkboxes.length > 0) {
        deleteButton.classList.remove("d-none");
    } else {
        deleteButton.classList.add("d-none");
    }
}

// Script untuk menampilkan modal pesan
if (window.location.search.includes("message=")) {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get("message");
    document.getElementById("messageContent").innerText = message;
    var messageModal = new bootstrap.Modal(document.getElementById("messageModal"));
    messageModal.show();
}
</script>';

// Menggunakan layout
include '../../views/layout.php';
?>
