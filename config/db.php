<?php
// session_start([
//     'cookie_secure' => true,   // Cookie hanya dikirim melalui HTTPS
//     'cookie_httponly' => true, // Cookie tidak bisa diakses oleh JavaScript
//     'cookie_samesite' => 'Strict' // Mencegah cookie dikirim lintas situs
// ]);

// if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//     header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//     exit;
// }

$host = 'localhost';
$user = 'root';
$password = '';
$db_name = 'inventory';

$conn = new mysqli($host, $user, $password, $db_name);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
