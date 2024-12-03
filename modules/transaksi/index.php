<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'user') {
    header("Location: /inventory-app/public/"); // Ganti dengan URL halaman yang diinginkan
    exit();
}

include '../../config/db.php';

// Pagination settings
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Jumlah transaksi per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Handle reset
if (isset($_GET['reset'])) {
    unset($_SESSION['search']); // Menghapus nilai pencarian dari session
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&limit=$limit");
    exit;
}

// Fungsi untuk mendapatkan daftar transaksi dengan pagination
function getTransaksi($conn, $search = '', $offset = 0, $limit = 10) {
    $query = "
        SELECT t.id, b.nama_barang, t.jenis, t.jumlah, t.tanggal
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        WHERE b.nama_barang LIKE ? OR t.jenis LIKE ?
        ORDER BY t.tanggal DESC LIMIT ?, ?
    ";
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
        WHERE b.nama_barang LIKE ? OR t.jenis LIKE ?
    ";
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
    $_SESSION['search'] = $search; // Simpan pencarian dalam session
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&limit=$limit");
    exit;
} else {
    // Ambil pencarian dari session jika ada
    $search = isset($_SESSION['search']) ? $_SESSION['search'] : '';
}

$result = getTransaksi($conn, $search, $offset, $limit);
$totalTransaksi = getTotalTransaksi($conn, $search);

// Pastikan totalTransaksi tidak nol sebelum melakukan pembagian
$totalPages = ($totalTransaksi > 0) ? ceil($totalTransaksi / $limit) : 1; // Setidaknya ada satu halaman

// Menyiapkan variabel untuk layout
$title = "Daftar Transaksi";
$content = '
    <h1 class="mb-4">Daftar Transaksi</h1>
    <div class="d-flex justify-content-between">
    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Transaksi</a>
    <form method="POST" class="d-flex mb-3" role="search">
        <input type="search" name="search" class="form-control me-2" placeholder="Cari transaksi..." value="' . htmlspecialchars($search) . '">
        <button class="btn btn-outline-success me-2" type="submit"><i class="bi bi-search"></i></button>
        <a href="?reset=true&page=1&limit=' . $limit . '" class="btn btn-outline-warning"><i class="bi bi-arrow-clockwise"></i></a>
    </form>
';

// Menambahkan tombol Laporan hanya untuk admin 
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'user') {
    $content .= '<div class="mb-3">
    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#dateModal">
        <i class="bi bi-floppy me-2"></i>Laporan Transaksi
    </button>
</div>
</div>';
}

// Modal untuk memilih tanggal
$content .= '
    <div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateModalLabel">Pilih Tanggal untuk Laporan</h5>
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
                        <button type="submit" class="btn btn-primary">Buat Laporan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>';

$content .= '
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No </th>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>';

            $no = 1; // Inisialisasi nomor urut
            while ($row = $result->fetch_assoc()) {
                $content .= '
                <tr>
                    <td>' . $no++ . '</td>
                    <td>' . $row['id'] . '</td>
                    <td>' . htmlspecialchars($row['nama_barang']) . '</td>
                    <td>' . htmlspecialchars($row['jenis']) . '</td>
                    <td>' . $row['jumlah'] . '</td>
                    <td>' . date('d-m-Y H:i:s', strtotime($row['tanggal'])) . '</td>';
                    
                // Tampilkan kolom aksi hanya untuk Admin dan Manajer
                if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager') {
                    $content .= '<td>
                        <a href="edit.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm">Hapus</a>
                    </td>';
                } else {
                    $content .= '<td></td>'; // Kosongkan kolom aksi untuk pengguna lain
                }

                $content .= '</tr>';
            }

$content .= '
            </tbody>
        </table>
    </div>
';

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

// JavaScript untuk mengubah limit
$content .= '
<script>
function changeLimit(value) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set("limit", value);
    urlParams.set("page", 1); // Reset to first page
    window.location.search = urlParams.toString();
}
</script>';

// Menggunakan layout
include '../../views/layout.php';
?>