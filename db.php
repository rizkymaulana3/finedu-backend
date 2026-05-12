<?php
// ============================================================
//  CORS HEADERS - Wajib paling atas sebelum apapun
// ============================================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================================
//  KONFIGURASI DATABASE (AUTO-DETECT: Railway / Vercel / Lokal)
// ============================================================

// Railway menyediakan env otomatis: MYSQLHOST, MYSQLUSER, dst.
// Vercel bisa pakai env yang di-set manual di dashboard Vercel.
// Keduanya akan terbaca lewat getenv() atau $_ENV.

$db_host = getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: 'localhost';
$db_user = getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$db_name = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: 'finedu_db';
$db_port = getenv('MYSQLPORT')     ?: getenv('DB_PORT')     ?: '3306';

// ============================================================
//  KONEKSI
// ============================================================

error_reporting(0);
ini_set('display_errors', 0);
ob_start();

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, (int)$db_port);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Koneksi ke database gagal: " . $conn->connect_error
    ]);
    exit();
}

$conn->set_charset("utf8mb4");
?>
