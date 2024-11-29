<?php
// Mulai sesi untuk autentikasi
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

include '../config/db.php';

// Query untuk menghitung total barang dan total transaksi
$totalQuery = "
    SELECT 
        (SELECT COUNT(*) FROM barang) as total_barang, 
        (SELECT COUNT(*) FROM transaksi) as total_transaksi
";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalBarang = $totalRow['total_barang'];
$totalTransaksi = $totalRow['total_transaksi'];

// Tentukan konten utama (dynamic content)
ob_start(); // Mulai output buffering
?>
<div class="container">
    <h1 class="mb-4">Dashboard</h1>
    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>! Anda dapat melihat barang dan transaksi dari sini.</p>
    
    <div class="row">
        <div class="col-md-6">
            <a href="/inventory-app/modules/barang" class="card text-white bg-primary mb-4">
                <div class="card-header">Total Barang</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $totalBarang; ?></h5>
                    <p class="card-text">Jumlah total barang yang tersedia.</p>
                </div>
            </a>
        </div>
        
        <div class="col-md-6">
            <a href="/inventory-app/modules/transaksi" class="card text-white bg-success mb-4">
                <div class="card-header">Total Transaksi</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $totalTransaksi; ?></h5>
                    <p class="card-text">Jumlah total transaksi yang telah dilakukan.</p>
                </div>
            </a>
        </div>
    </div>

    <h3 class="mt-5">Transaksi</h3>
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
                    <th>Aksi</th> <!-- Tambahkan kolom aksi untuk admin -->
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
                        <td>{$no}</td> <!-- Tampilkan nomor urut -->
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
                    $no++; // Increment nomor urut
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>Tidak ada transaksi.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean(); // Simpan konten dynamic ke variabel
$title = "Dashboard Pengguna"; // Atur judul halaman

// Gunakan layout utama
include '../views/layout.php';
?>