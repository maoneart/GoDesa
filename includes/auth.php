<?php
// includes/auth.php - Session and Role Management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Role Switcher Handler (via GET ?switch_role=admin|kades|lurah|staff|rw|rt|warga)
if (isset($_GET['switch_role'])) {
    $targetRole = trim(strtolower($_GET['switch_role']));
    if ($targetRole === 'kades') $targetRole = 'lurah'; // Kompatibilitas database
    $allowedRoles = ['admin', 'lurah', 'staff', 'rw', 'rt', 'warga'];
    if (in_array($targetRole, $allowedRoles)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = ? LIMIT 1");
        $stmt->execute([$targetRole]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_rt'] = $user['rt'];
            $_SESSION['user_rw'] = $user['rw'];
        }
    }
    // Redirect back without switch_role param
    $cleanUri = preg_replace('/(\?|&)switch_role=[^&]*/', '', $_SERVER['REQUEST_URI']);
    if (strpos($cleanUri, '?') === false && strpos($cleanUri, '&') !== false) {
        $cleanUri = preg_replace('/&/', '?', $cleanUri, 1);
    }
    header("Location: " . ($cleanUri ?: 'index.php'));
    exit;
}

// Default session: Hermawan (Warga) if not set
if (!isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'warga' LIMIT 1");
    $stmt->execute();
    $warga = $stmt->fetch();
    if ($warga) {
        $_SESSION['user_id'] = $warga['id'];
        $_SESSION['user_role'] = $warga['role'];
        $_SESSION['user_nama'] = $warga['nama'];
        $_SESSION['user_rt'] = $warga['rt'];
        $_SESSION['user_rw'] = $warga['rw'];
    }
}

// Helper to get active user details
function currentUser() {
    global $pdo;
    if (!isset($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function currentRole() {
    $role = $_SESSION['user_role'] ?? 'warga';
    return $role === 'lurah' ? 'kades' : $role;
}

function hasRole($roles) {
    if (is_string($roles)) {
        $roles = [$roles];
    }
    $cur = currentRole();
    if (($cur === 'kades' || $cur === 'lurah') && (in_array('kades', $roles) || in_array('lurah', $roles))) {
        return true;
    }
    return in_array($cur, $roles);
}

function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, info
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

$activeUser = currentUser();
