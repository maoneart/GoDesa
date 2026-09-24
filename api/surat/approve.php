<?php
// api/surat/approve.php
require_once __DIR__ . '/../config/database.php';

$data = getJsonInput();
$suratId = intval($data['id'] ?? 0);
$action = trim($data['action'] ?? '');
$userId = intval($data['user_id'] ?? 0);
$catatan = trim($data['catatan'] ?? '');

if ($suratId <= 0 || empty($action)) {
    sendResponse(false, 'Parameter id dan action wajib dikirim', null, 400);
}

$map = [
    1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
    7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
];
$romawi = $map[intval(date('n'))] ?? 'IX';
$curYear = date('Y');

switch ($action) {
    case 'verifikasi_rt':
        $rtNum = trim($data['rt'] ?? '003');
        $rwNum = trim($data['rw'] ?? '001');
        $noUrutRt = sprintf("%03d", rand(10, 99));
        $nomorPengantarRt = "{$noUrutRt}/RT.{$rtNum}-RW.{$rwNum}/Ds.CBT/{$romawi}/{$curYear}";
        $catatanRt = !empty($catatan) ? $catatan : "Pengantar telah disahkan oleh Ketua RT {$rtNum} / RW {$rwNum}.";

        $stmt = $pdo->prepare("UPDATE surat SET status = 'diverifikasi_rt', nomor_pengantar_rt = ?, catatan_rt = ?, rt_id = ?, tanggal_rt = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$nomorPengantarRt, $catatanRt, $userId, $suratId]);
        sendResponse(true, 'Pengantar RT berhasil disetujui', ['status' => 'diverifikasi_rt', 'nomor_pengantar_rt' => $nomorPengantarRt]);
        break;

    case 'verifikasi_rw':
        $rwNum = trim($data['rw'] ?? '001');
        $noUrutRw = sprintf("%03d", rand(10, 99));
        $nomorPengantarRw = "{$noUrutRw}/RW.{$rwNum}/Ds.CBT/{$romawi}/{$curYear}";
        $catatanRw = !empty($catatan) ? $catatan : "Mengetahui dan menyetujui pengantar RT setempat.";

        $stmt = $pdo->prepare("UPDATE surat SET status = 'diverifikasi_rw', nomor_pengantar_rw = ?, catatan_rw = ?, rw_id = ?, tanggal_rw = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$nomorPengantarRw, $catatanRw, $userId, $suratId]);
        sendResponse(true, 'Pengesahan Ketua RW berhasil', ['status' => 'diverifikasi_rw', 'nomor_pengantar_rw' => $nomorPengantarRw]);
        break;

    case 'verifikasi_staff':
        $catatanStaff = !empty($catatan) ? $catatan : "Berkas diverifikasi oleh Kasi Pelayanan Desa Cibuntu.";
        $stmt = $pdo->prepare("UPDATE surat SET status = 'diverifikasi_staff', catatan_staff = ?, catatan = ?, staff_id = ? WHERE id = ?");
        $stmt->execute([$catatanStaff, $catatanStaff, $userId, $suratId]);
        sendResponse(true, 'Berkas berhasil diverifikasi oleh staff pelayanan', ['status' => 'diverifikasi_staff']);
        break;

    case 'approve_kades':
        $catatanKades = !empty($catatan) ? $catatan : "Disetujui dan ditandatangani secara elektronik (e-Signature QR) oleh Kepala Desa Cibuntu.";
        $stmt = $pdo->prepare("UPDATE surat SET status = 'disetujui_kades', catatan = ?, lurah_id = ?, kades_id = ?, tanggal_disetujui = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$catatanKades, $userId, $userId, $suratId]);
        sendResponse(true, 'Surat resmi telah disahkan & ditandatangani oleh Kepala Desa', ['status' => 'disetujui_kades']);
        break;

    case 'reject':
        $alasan = !empty($catatan) ? $catatan : 'Berkas persyaratan belum lengkap atau tidak valid.';
        $stmt = $pdo->prepare("UPDATE surat SET status = 'ditolak', catatan = ? WHERE id = ?");
        $stmt->execute([$alasan, $suratId]);
        sendResponse(true, 'Permohonan surat ditolak', ['status' => 'ditolak', 'alasan' => $alasan]);
        break;

    default:
        sendResponse(false, 'Aksi tidak dikenal', null, 400);
}
