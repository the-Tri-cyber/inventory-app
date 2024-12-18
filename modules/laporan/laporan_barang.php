<?php
session_start();
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'user')) {
    header("Location: ../auth/login.php");
    exit;
}

// Include TCPDF library
require_once('../../vendor/autoload.php'); // Ganti dengan path yang sesuai

// Koneksi ke database
include '../../config/db.php'; // Ganti dengan path yang sesuai

// Buat instance TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Laporan Item');
$pdf->SetSubject('Laporan Item');
$pdf->SetKeywords('TCPDF, PDF, laporan, item');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Laporan Item', 'Generated by Inventory App');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();

// Ambil tanggal dari parameter GET
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : '';

// Query untuk mengambil data barang dengan filter tanggal
if ($reportType === 'created') {
    $query = "SELECT b.id, b.nama_barang, b.merk, k.kategori, r.ruangan, b.stok, b.asal_perolehan
              FROM barang b 
              JOIN kategori k ON b.id_kategori = k.id_kategori 
              JOIN ruang r ON b.id_ruangan = r.id_ruangan
              WHERE b.created_at BETWEEN ? AND ?";
} elseif ($reportType === 'updated') {
    $query = "SELECT b.id, b.nama_barang, b.merk, k.kategori, r.ruangan, b.stok, b.asal_perolehan
              FROM barang b 
              JOIN kategori k ON b.id_kategori = k.id_kategori 
              JOIN ruang r ON b.id_ruangan = r.id_ruangan
              WHERE b.updated_at BETWEEN ? AND ?";
} else {
    // Jika report_type tidak valid, redirect atau tampilkan pesan error
    echo "Invalid report type.";
    exit;
}

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

// Buat tabel untuk laporan
$html = '<h2>Laporan Item</h2>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Item</th>
                    <th>Merk</th>
                    <th>Kategori</th>
                    <th>Ruang</th>
                    <th>Stok</th>
                    <th>Asal Perolehan</th>
                </tr>
            </thead>
            <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['nama_barang'] . '</td>
                    <td>' . $row['merk'] . '</td>
                    <td>' . $row['kategori'] . '</td>
                    <td>' . $row['ruangan'] . '</td>
                    <td>' . $row['stok'] . '</td>
                    <td>' . $row['asal_perolehan'] . '</td>';


        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="10">Tidak ada data item.</td></tr>';
}

$html .= '</tbody></table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('laporan_barang.pdf', 'I'); // 'I' untuk menampilkan di browser, 'D' untuk download
?>