<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Cek peran pengguna
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: /inventory-app/public/"); // Ganti dengan URL halaman yang diinginkan
    exit();
}

include '../../config/db.php';

// Pagination settings
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Jumlah kategori per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Handle reset
if (isset($_GET['reset'])) {
    unset($_SESSION['search']); // Menghapus nilai pencarian dari session
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&limit=$limit");
    exit;
}

// Fungsi untuk mendapatkan daftar kategori dengan pagination
function getKategori($conn, $search = '', $offset = 0, $limit = 10) {
    $query = "SELECT * FROM kategori WHERE kategori LIKE ? ORDER BY id_kategori DESC LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("sii", $searchParam, $offset, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Fungsi untuk menghitung total kategori
function getTotalKategori($conn, $search = '') {
    $query = "SELECT COUNT(*) as total FROM kategori WHERE kategori LIKE ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("s", $searchParam);
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

$result = getKategori($conn, $search, $offset, $limit);
$totalKategori = getTotalKategori($conn, $search);

// Pastikan totalKategori tidak nol sebelum melakukan pembagian
$totalPages = ($totalKategori > 0) ? ceil($totalKategori / $limit) : 1; // Setidaknya ada satu halaman

// Membuat konten tabel kategori
function createTableContent($result) {
    $content = '';
    $no = 1; // Inisialisasi nomor urut

    while ($row = $result->fetch_assoc()) {
        $content .= '<tr>
            <td>' . $no++ . '</td>
            <td>' . htmlspecialchars($row['id_kategori']) . '</td>
            <td>' . htmlspecialchars($row['kategori']) . '</td>
            <td>
                <a href="edit.php?id=' . $row['id_kategori'] . '" class="btn btn-warning btn-sm">Edit</a>
                <a href="hapus.php?id=' . $row['id_kategori'] . '" class="btn btn-danger btn-sm">Hapus</a>
            </td>
        </tr>';
    }

    return $content;
}

$content = '
    <h1 class="mb-4">Daftar Kategori</h1>
    <div class="d-flex justify-content-between">
    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Kategori</a>
    <form method="POST" class="d-flex mb-3" role="search">
        <input type="search" name="search" class="form-control me-2" placeholder="Cari kategori..." value="' . htmlspecialchars($search) . '">
        <button class="btn btn-outline-success me-2" type="submit"><i class="bi bi-search"></i></button>
        <a href="?reset=true&page=1&limit=' . $limit . '" class="btn btn-outline-warning"><i class="bi bi-arrow-clockwise"></i></a>
    </form>
';

// Menambahkan tombol Laporan hanya untuk admin dan manager
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager') {
    $content .= '<a target="_blank" href="../laporan/laporan_kategori.php" class="btn btn-secondary mb-3 ms-2"><i class="bi bi-floppy me-2"></i>Laporan Kategori</a>
    </div>';
}

$content .= '
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>ID Kategori</th>
                    <th>Kategori</th>
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
        <span>Menampilkan ' . (($page - 1) * $limit + 1) . ' sampai ' . min($page * $limit, $totalKategori) . ' dari ' . $totalKategori . ' kategori</span>
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

$title = "Daftar Kategori";
// Menyertakan layout
include '../../views/layout.php';
?>