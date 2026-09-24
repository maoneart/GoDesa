<?php
// api/auth/profile.php
require_once __DIR__ . '/../config/database.php';

$userId = intval($_GET['id'] ?? $_POST['id'] ?? 4);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    sendResponse(false, 'Data pengguna tidak ditemukan', null, 404);
}

unset($user['password']);
sendResponse(true, 'Profil pengguna berhasil diambil', $user);
