<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
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
$pdf->SetTitle('Laporan Kategori');
$pdf->SetSubject('Laporan Kategori');
$pdf->SetKeywords('TCPDF, PDF, laporan, kategori');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Laporan Kategori', 'Generated by Inventory App');

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

// Query untuk mengambil data kategori
$query = "SELECT id_kategori, kategori FROM kategori";
$result = $conn->query($query);

// Buat tabel untuk laporan
$html = '<h2>Laporan Kategori</h2>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>ID Kategori</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['id_kategori'] . '</td>
                    <td>' . $row['kategori'] . '</td>
                </tr>';
    }
} else {
    $html .= '<tr><td colspan="2">Tidak ada data kategori.</td></tr>';
}

$html .= '</tbody></table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('laporan_kategori.pdf', 'I'); // 'I' untuk menampilkan di browser, 'D' untuk download
?>