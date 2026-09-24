<?php
// api/surat/create.php
require_once __DIR__ . '/../config/database.php';

$data = getJsonInput();
$userId = intval($data['user_id'] ?? 4);
$jenisSurat = trim($data['jenis_surat'] ?? '');
$keperluan = trim($data['keperluan'] ?? '');

if (empty($jenisSurat) || empty($keperluan)) {
    sendResponse(false, 'Jenis surat dan keperluan wajib diisi', null, 400);
}

// Helper Angka Bulan ke Romawi
$map = [
    1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
    7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
];
$romawi = $map[intval(date('n'))] ?? 'IX';
$curYear = date('Y');

$prefixMap = [
    'Surat Keterangan Usaha (SKU)' => '503',
    'Surat Pengantar SKCK' => '300',
    'Surat Keterangan Domisili' => '470',
    'Surat Keterangan Tidak Mampu (SKTM)' => '401',
    'Surat Keterangan Belum Menikah' => '472',
    'Surat Keterangan Kematian' => '474.3'
];
$kodeKlasifikasi = $prefixMap[$jenisSurat] ?? '470';
$nomorUrut = sprintf("%03d", rand(10, 999));
$nomorSurat = "{$kodeKlasifikasi}/{$nomorUrut}/Ds.CBT/{$romawi}/{$curYear}";
$qrToken = md5($nomorSurat . time());

$extra = $data['data_tambahan'] ?? [];
if (!is_array($extra)) {
    $extra = [];
}

$stmt = $pdo->prepare("INSERT INTO surat (nomor_surat, user_id, jenis_surat, keperluan, data_tambahan, status, qr_token) VALUES (?, ?, ?, ?, ?, 'diajukan', ?)");
$stmt->execute([$nomorSurat, $userId, $jenisSurat, $keperluan, json_encode($extra), $qrToken]);
$newId = $pdo->lastInsertId();

sendResponse(true, 'Permohonan surat berhasil diajukan', [
    'id' => $newId,
    'nomor_surat' => $nomorSurat,
    'status' => 'diajukan'
]);
