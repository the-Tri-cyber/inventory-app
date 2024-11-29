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

// Fungsi untuk mendapatkan daftar barang dengan pagination
function getBarang($conn, $search = '', $offset = 0, $limit = 10) {
    $query = "SELECT * FROM barang WHERE 
              nama_barang LIKE ? OR 
              merk LIKE ? OR 
              asal_perolehan LIKE ? 
              ORDER BY created_at DESC LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    $searchParam = "%$search%";
    $stmt->bind_param("ssiii", $searchParam, $searchParam, $searchParam, $offset, $limit);
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

$result = getBarang($conn, $search, $offset, $limit);
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
            <td>' . htmlspecialchars($row['merk']) . '</td>
            <td>' . htmlspecialchars($row['id_kategori']) . '</td>
            <td>' . htmlspecialchars($row['id_kondisi']) . '</td>
            <td>' . htmlspecialchars($row['id_ruangan']) . '</td>
            <td>' . $row['stok'] . '</td>
            <td>' . number_format($row['harga_satuan'], 2, ',', '.') . '</td>
            <td>' . htmlspecialchars($row['asal_perolehan']) . '</td>
            <td><img src="/inventory-app/modules/uploads/' . htmlspecialchars($row['gambar']) . '" alt="Gambar" style="width: 50px; height: auto;"></td>
            <td>' . date('d-m-Y', strtotime($row['created_at'])) . '</td>
            <td>' . date('d-m-Y', strtotime($row['updated_at'])) . '</td>';

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
    <h1 class="mb-4">Daftar Barang</h1>
    <div class="d-flex justify-content-between">
    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Barang</a>
    <form method="POST" class="d-flex mb-3" role="search">
        <input type="search" name="search" class="form-control me-2" placeholder="Cari barang..." value="' . htmlspecialchars($search) . '">
        <button class="btn btn-outline-success me-2" type="submit"><i class="bi bi-search"></i></button>
        <a href="?reset=true&page=1&limit=' . $limit . '" class="btn btn-outline-warning"><i class="bi bi-arrow-clockwise"></i></a>
    </form>
';

// Menambahkan tombol Laporan hanya untuk admin 
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'user') {
    $content .= '<a target="_blank" href="../laporan/laporan_barang.php" class="btn btn-secondary mb-3 ms-2"><i class="bi bi-floppy me-2"></i>Laporan Barang</a>
    </div>';
}
 
$content .='
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>ID Kategori</th>
                    <th>ID Kondisi</th>
                    <th>ID Ruang</th>
                    <th>Stok</th>
                    <th>Harga Satuan</th>
                    <th>Asal Perolehan</th>
                    <th>Gambar</th>
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
    $content .= '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&limit=' . $limit . '&search=' . urlencode($search) . '">' . $i . '</a></ li>';
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

$title = "Daftar Barang";
// Menyertakan layout
include '../../views/layout.php';
?>