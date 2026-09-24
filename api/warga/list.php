<?php
// api/warga/list.php
require_once __DIR__ . '/../config/database.php';

$rw = trim($_GET['rw'] ?? '');
$rt = trim($_GET['rt'] ?? '');
$search = trim($_GET['q'] ?? '');

$sql = "SELECT id, nik, no_kk, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pekerjaan, kewarganegaraan, golongan_darah, email, role, no_hp, alamat, rt, rw FROM users WHERE role = 'warga'";
$params = [];

if (!empty($rw)) {
    $sql .= " AND rw = ?";
    $params[] = $rw;
}

if (!empty($rt)) {
    $sql .= " AND rt = ?";
    $params[] = $rt;
}

if (!empty($search)) {
    $sql .= " AND (nama LIKE ? OR nik LIKE ? OR no_kk LIKE ? OR pekerjaan LIKE ?)";
    $term = "%{$search}%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

$sql .= " ORDER BY rw ASC, rt ASC, no_kk ASC, nama ASC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$citizens = $stmt->fetchAll();

sendResponse(true, 'Daftar data warga berhasil diambil', [
    'total' => count($citizens),
    'warga' => $citizens
]);
