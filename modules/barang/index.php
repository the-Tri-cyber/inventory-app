<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Cek peran pengguna
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'user') {
    header("Location: /inventory-app/public/");
    exit();
}

include '../../config/db.php';

// Pagination settings
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Jumlah barang per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Handle reset
if (isset($_GET['reset'])) {
    unset($_SESSION['search']); // Menghapus nilai pencarian dari session
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&limit=$limit");
    exit;
}

$filterKategori = '';

// Modifikasi fungsi getBarang untuk menyertakan filter kategori
function getBarang($conn, $search = '', $offset = 0, $limit = 10, $filterKategori = '') {
    $query = "SELECT b.*, k.kategori, r.ruangan 
              FROM barang b
              LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
              LEFT JOIN ruang r ON b.id_ruangan = r.id_ruangan
              WHERE (b.nama_barang LIKE ? OR b.merk LIKE ? OR b.asal_perolehan LIKE ?)";
    if (!empty($filterKategori)) {
        $query .= " AND b.id_kategori = ?";
    }
    $query .= " ORDER BY b.created_at DESC LIMIT ?, ?";
    $stmt = $conn->prepare($query);

    $searchParam = "%$search%";
    if (!empty($filterKategori)) {
        $stmt->bind_param("sssiii", $searchParam, $searchParam, $searchParam, $filterKategori, $offset, $limit);
    } else {
        $stmt->bind_param("ssiii", $searchParam, $searchParam, $searchParam, $offset, $limit);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Fungsi untuk menghitung total barang
function getTotalBarang($conn, $search = '') {
    $query = "SELECT COUNT(*) as total FROM barang WHERE 
              nama_barang LIKE ? OR 
              merk LIKE ? OR 
              asal_perolehan LIKE ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total'];
}

// Ambil daftar kategori untuk dropdown
function getKategori($conn) {
    $query = "SELECT id_kategori, kategori FROM kategori";
    $result = $conn->query($query);
    $kategoriList = [];
    while ($row = $result->fetch_assoc()) {
        $kategoriList[] = $row;
    }
    return $kategoriList;
}

$kategoriList = getKategori($conn);

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

// Tangani filter kategori
$filterKategori = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : '';
if (!empty($filterKategori)) {
    $_SESSION['filter_kategori'] = $filterKategori; // Simpan filter kategori di session
} elseif (isset($_GET['reset_filter'])) {
    unset($_SESSION['filter_kategori']); // Hapus filter kategori
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&limit=$limit");
    exit;
} else {
    $filterKategori = isset($_SESSION['filter_kategori']) ? $_SESSION['filter_kategori'] : '';
}

// Panggil fungsi getBarang dengan filter kategori
$result = getBarang($conn, $search, $offset, $limit, $filterKategori);
$totalBarang = getTotalBarang($conn, $search);

// Pastikan totalBarang tidak nol sebelum melakukan pembagian
$totalPages = ($totalBarang > 0) ? ceil($totalBarang / $limit) : 1; // Setidaknya ada satu halaman

// Membuat konten tabel barang
function createTableContent($result) {
    $content = '';
    $no = 1; // Inisialisasi nomor urut

    while ($row = $result->fetch_assoc()) {
        $content .= '<tr> 
            <td>' . $no++ . '</td>
            <td>' . $row['id'] . '</td>
            <td>' . htmlspecialchars($row['nama_barang']) . '</td>
            <td>' . htmlspecialchars($row ['merk']) . '</td>
            <td>' . htmlspecialchars($row['kategori']) . '</td>
            <td>' . htmlspecialchars($row['ruangan']) . '</td>
            <td>' . $row['stok'] . '</td>
            <td>' . htmlspecialchars($row['asal_perolehan']) . '</td>
            <td>' . date('d-m-Y H:i:s', strtotime($row['created_at'])) . '</td>
            <td>' . date('d-m-Y H:i:s', strtotime($row['updated_at'])) . '</td>';

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

    return $content;
}

$content = '
    <h1 class="mb-4">Daftar Item</h1>
    <div class="d-flex justify-content-between">
    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Item</a>
    <form method="POST" class="d-flex mb-3" role="search">
        <input type="search" name="search" class="form-control me-2" placeholder="Cari Item..." value="' . htmlspecialchars($search) . '">
        <button class="btn btn-outline-success me-2" type="submit"><i class="bi bi-search"></i></button>
        <a href="?reset=true&page=1&limit=' . $limit . '" class="btn btn-outline-warning"><i class="bi bi-arrow-clockwise"></i></a>
    </form>
';

// Menambahkan dropdown laporan
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'user') {
    $content .= '
    <div class="mb-3">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-floppy me-2"></i>Laporan Item
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createdDateModal">Tanggal Dibuat</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updatedDateModal">Tanggal Diupdate</a></li>
            </ul>
        </div>
    </div>
    </div>';
}

// Tambahkan form filter di tampilan
$content .= '
    <div class="mb-3">
        <form method="GET" class="d-flex align-items-center" role="filter">
            <label for="filter_kategori" class="form-label me-2">Filter Kategori:</label>
            <select name="filter_kategori" id="filter_kategori" class="form-select me-2">
                <option value="">Semua Kategori</option>';
foreach ($kategoriList as $kategori) {
    $selected = ($filterKategori == $kategori['id_kategori']) ? 'selected' : '';
    $content .= '<option value="' . $kategori['id_kategori'] . '" ' . $selected . '>' . htmlspecialchars($kategori['kategori']) . '</option>';
}
$content .= '</select>
            <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
            <a href="?reset_filter=true&page=1&limit=' . $limit . '" class="btn btn-outline-warning">Reset</a>
        </form>
    </div>';

// Modal untuk memilih tanggal dibuat
$content .= '
<div class="modal fade" id="createdDateModal" tabindex="-1" aria-labelledby="createdDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createdDateModalLabel">Pilih Tanggal untuk Laporan Berdasarkan Tanggal Dibuat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="../laporan/laporan_barang.php" target="_blank">
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

// Modal untuk memilih tanggal diupdate
$content .= '
<div class="modal fade" id="updatedDateModal" tabindex="-1" aria-labelledby="updatedDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatedDateModalLabel">Pilih Tanggal untuk Laporan Berdasarkan Tanggal Diupdate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="../laporan/laporan_barang.php" target="_blank">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Dari Tanggal:</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Sampai Tanggal:</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <input type="hidden" name="search" value="' . htmlspecialchars($search) . '">
                    <input type="hidden" name="report_type" value="updated">
                    <button type="submit" class="btn btn-primary">Buat Laporan</button>
                </form>
            </div>
        </div>
    </div>
</div>';

$content .= '
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Nama Item</th>
                    <th>Merk</th>
                    <th>Kategori</th>
                    <th>Ruang</th>
                    <th>Stok</th>
                    <th>Asal Perolehan</th>
                    <th>Tanggal Dibuat</th>
                    <th>Tanggal Diupdate</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>' . createTableContent($result) . '</tbody>
        </table>
    </div>
';

// Pagination controls
$content .= '<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span>Menampilkan ' . (($page - 1) * $limit + 1) . ' sampai ' . min($page * $limit, $totalBarang) . ' dari ' . $totalBarang . ' barang</span>
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

$title = "Daftar Item";
// Menyertakan layout
include '../../views/layout.php';
?>