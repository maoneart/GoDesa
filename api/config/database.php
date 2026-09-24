<?php
// api/config/database.php - Hybrid Database Driver (MySQL for hosting & SQLite fallback)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

date_default_timezone_set('Asia/Jakarta');

// Konfigurasi MySQL Hosting maoneart.my.id
$mysqlHost = 'localhost';
$mysqlDb   = 'u9585642_godesa';
$mysqlUser = 'u9585642_godesa';
$mysqlPass = 'Godesa@2026!'; // Sesuaikan dengan kredensial cPanel hosting

// Fallback SQLite Lokal
$sqlitePath = __DIR__ . '/../../db/desa.sqlite';

$pdo = null;

// Coba koneksi MySQL hosting terlebih dahulu jika bukan lokal termux
$isHosting = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'maoneart.my.id') !== false);

if ($isHosting) {
    try {
        $dsn = "mysql:host={$mysqlHost};dbname={$mysqlDb};charset=utf8mb4";
        $pdo = new PDO($dsn, $mysqlUser, $mysqlPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $e) {
        // Fallback to SQLite if MySQL fails
    }
}

if (!$pdo) {
    try {
        $pdo = new PDO("sqlite:" . $sqlitePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Koneksi database gagal: " . $e->getMessage()
        ]);
        exit;
    }
}

function sendResponse($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? $_POST;
}
