<?php
// api/dashboard/stats.php
require_once __DIR__ . '/../config/database.php';

$totalWarga = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'warga'")->fetchColumn();
$totalSurat = $pdo->query("SELECT COUNT(*) FROM surat")->fetchColumn();
$totalLaporan = $pdo->query("SELECT COUNT(*) FROM laporan")->fetchColumn();
$totalAgenda = $pdo->query("SELECT COUNT(*) FROM agenda WHERE tanggal >= DATE('now')")->fetchColumn();

$pengumuman = $pdo->query("SELECT * FROM pengumuman ORDER BY is_pinned DESC, created_at DESC LIMIT 5")->fetchAll();
$agendaTerdekat = $pdo->query("SELECT * FROM agenda WHERE tanggal >= DATE('now') ORDER BY tanggal ASC, waktu_mulai ASC LIMIT 3")->fetchAll();

sendResponse(true, 'Statistik dashboard berhasil diambil', [
    'stats' => [
        'total_warga' => intval($totalWarga),
        'total_surat' => intval($totalSurat),
        'total_laporan' => intval($totalLaporan),
        'total_agenda' => intval($totalAgenda)
    ],
    'pengumuman' => $pengumuman,
    'agenda_terdekat' => $agendaTerdekat
]);
