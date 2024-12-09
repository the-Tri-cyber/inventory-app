<?php
// Mulai sesi untuk autentikasi
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: modules/auth/login.php");
    exit();
}

include 'config/db.php';

// Query untuk menghitung total barang
$totalBarangQuery = "SELECT COUNT(*) as total_barang FROM barang";
$totalBarangResult = $conn->query($totalBarangQuery);
$totalBarangRow = $totalBarangResult->fetch_assoc();
$totalBarang = $totalBarangRow['total_barang'];

// Query untuk menghitung barang yang tersedia (stok > 0)
$barangTersediaQuery = "SELECT COUNT(*) as barang_tersedia FROM barang WHERE stok > 0";
$barangTersediaResult = $conn->query($barangTersediaQuery);
$barangTersediaRow = $barangTersediaResult->fetch_assoc();
$barangTersedia = $barangTersediaRow['barang_tersedia'];

// Query untuk menghitung total transaksi harian
$totalTransaksiHarianQuery = "
    SELECT COUNT(*) as total_transaksi_harian 
    FROM transaksi 
    WHERE DATE(tanggal) = CURDATE()
";
$totalTransaksiHarianResult = $conn->query($totalTransaksiHarianQuery);
$totalTransaksiHarianRow = $totalTransaksiHarianResult->fetch_assoc();
$totalTransaksiHarian = $totalTransaksiHarianRow['total_transaksi_harian'];

// Query untuk menghitung total transaksi bulanan
$totalTransaksiBulananQuery = "
    SELECT COUNT(*) as total_transaksi_bulanan 
    FROM transaksi 
    WHERE MONTH(tanggal) = MONTH(CURDATE()) 
      AND YEAR(tanggal) = YEAR(CURDATE())
";
$totalTransaksiBulananResult = $conn->query($totalTransaksiBulananQuery);
$totalTransaksiBulananRow = $totalTransaksiBulananResult->fetch_assoc();
$totalTransaksiBulanan = $totalTransaksiBulananRow['total_transaksi_bulanan'];

// Tentukan konten utama (dynamic content)
ob_start(); // Mulai output buffering
?>
<div class="container">
    <h1 class="mb-4">Dashboard</h1>
    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>! Anda dapat mengelola item, transaksi, dan laporan dari sini.</p>
    
    <div class="row">
        <div class="col-md-4">
            <a href="/inventory-app/modules/barang" class="card text-white bg-primary mb-4">
                <div class="card-header">Total Item</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $barangTersedia . " / " . $totalBarang; ?></h5>
                    <p class="card-text">Jumlah barang tersedia dibandingkan dengan total barang.</p>
                </div>
            </a>
        </div>
        
        <div class="col-md-4">
            <a href="/inventory-app/modules/transaksi" class="card text-white bg-success mb-4">
                <div class="card-header">Total Transaksi Harian</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $totalTransaksiHarian; ?></h5>
                    <p class="card-text">Jumlah total transaksi harian yang telah dilakukan.</p>
                </div>
            </a>
        </div>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="col-md-4">
            <a href="/inventory-app/modules/transaksi" class="card text-white bg-warning mb-4">
                <div class="card-header">Total Transaksi Bulanan</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $totalTransaksiBulanan; ?></h5>
                    <p class="card-text">Jumlah total transaksi bulanan yang telah dilakukan.</p>
                </div>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <h3 class="mt-5">Transaksi Terbaru</h3>
    <table class="table table-striped table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>ID Transaksi</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <th>Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mengambil semua transaksi
            $query = "
                SELECT t.id, b.nama_barang, t.jenis, t.jumlah, t.tanggal
                FROM transaksi t
                JOIN barang b ON t.id_barang = b.id
                ORDER BY t.tanggal DESC
                LIMIT 10
            ";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                $no = 1; // Inisialisasi nomor urut
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['id']}</td>
                        <td>{$row['nama_barang']}</td>
                        <td>{$row['jenis']}</td>
                        <td>{$row['jumlah']}</td>
                        <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                    if ($_SESSION['role'] === 'admin') {
                        echo "<td>
                            <a href='/inventory-app/modules/transaksi/edit.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='/inventory-app/modules/transaksi/hapus.php?id={$row['id']}' class='btn btn-danger btn-sm'>Hapus</a>
                        </td>";
                    }
                    echo "</tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>Tidak ada transaksi terbaru.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean(); // Simpan konten dynamic ke variabel
$title = "Dashboard"; // Atur judul halaman

// Gunakan layout utama
include 'views/layout.php';
?>
