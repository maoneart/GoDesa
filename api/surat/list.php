<?php
// api/surat/list.php
require_once __DIR__ . '/../config/database.php';

$userId = intval($_GET['user_id'] ?? 0);
$role = trim($_GET['role'] ?? 'warga');
$rt = trim($_GET['rt'] ?? '');
$rw = trim($_GET['rw'] ?? '');

$sql = "SELECT s.*, u.nama as pemohon_nama, u.nik as pemohon_nik, u.no_kk, u.tempat_lahir, u.tanggal_lahir, u.jenis_kelamin, u.pekerjaan, u.rt, u.rw, u.no_hp 
        FROM surat s 
        JOIN users u ON s.user_id = u.id";
$params = [];

if (in_array($role, ['admin', 'lurah', 'kades', 'staff'])) {
    // Sees all
} elseif ($role === 'rw' && !empty($rw)) {
    $sql .= " WHERE u.rw = ?";
    $params[] = $rw;
} elseif ($role === 'rt' && !empty($rt) && !empty($rw)) {
    $sql .= " WHERE u.rt = ? AND u.rw = ?";
    $params[] = $rt;
    $params[] = $rw;
} else {
    // Warga only sees own letters
    if ($userId > 0) {
        $sql .= " WHERE s.user_id = ?";
        $params[] = $userId;
    }
}

$sql .= " ORDER BY s.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$letters = $stmt->fetchAll();

foreach ($letters as &$l) {
    $l['data_tambahan'] = json_decode($l['data_tambahan'] ?? '{}', true);
}

sendResponse(true, 'Data surat berhasil diambil', $letters);
