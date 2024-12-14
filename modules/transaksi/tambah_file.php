<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db.php';

// Menyiapkan variabel untuk layout
$title = "Tambah Transaksi";
$content = '
    <div class="container mt-5">
        <h1 class="mb-4">Tambah Transaksi</h1>
        <form action="proses_tambah_file.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="file_excel" class="form-label">Upload File Excel:</label>
                <input type="file" name="file_excel" id="file_excel" class="form-control" accept=".xlsx, .xls" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>

        <div class="mt-4">
            <h5>Syarat Pengisian File Excel</h5>
            <div class="card">
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>File:</strong> Microsoft Excel dengan ekstensi <code>.xlsx</code></li>
                        <li><strong>Tanggal:</strong> Kolom A harus terisi dengan format <code>(yyyy-mm-dd hh:mm:ss)</code>. Contoh: <code>2024-12-13 15:03:00</code></li>
                        <li><strong>Urutan Kolom:</strong>
                            <ul>
                                <li>Tanggal</li>
                                <li>No Surat Jalan</li>
                                <li>Id Item</li>
                                <li>Jenis</li>
                                <li>Jumlah</li>
                                <li>Id Kondisi</li>
                            </ul>
                        </li>
                        <li><strong>Contoh Pengisian:</strong>
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr class="text-center">
                                    <th>Tanggal</th>
                                    <th>No Surat Jalan</th>
                                    <th>Id Item</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Id Kondisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>2024-12-13 20:39:00</td>
                                    <td>FSP 2403434</td>
                                    <td>14</td>
                                    <td>keluar</td>
                                    <td>10</td>
                                    <td>1</td>
                                </tr>
                            </tbody>
                        </table>
                        </li>
                        <li><strong>Catatan:</strong> Jika ada satu baris yang datanya tidak lengkap atau tidak terpenuhi pada kolom, maka akan di-skip oleh program (kecuali No Surat Jalan).</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
';

// Menggunakan layout
include '../../views/layout.php';