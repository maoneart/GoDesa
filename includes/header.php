<?php
// includes/header.php
require_once __DIR__ . '/auth.php';

$curRole = currentRole();
$roleLabels = [
    'admin' => ['label' => 'Admin Desa', 'class' => 'bg-slate-100 text-slate-700', 'icon' => 'fa-shield-halved'],
    'lurah' => ['label' => 'Kepala Desa', 'class' => 'bg-green-100 text-green-800', 'icon' => 'fa-user-tie'],
    'kades' => ['label' => 'Kepala Desa', 'class' => 'bg-green-100 text-green-800', 'icon' => 'fa-user-tie'],
    'staff' => ['label' => 'Staff Pelayanan', 'class' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-id-badge'],
    'rw'    => ['label' => 'Ketua RW 001', 'class' => 'bg-indigo-100 text-indigo-800', 'icon' => 'fa-sitemap'],
    'rt'    => ['label' => 'Ketua RT 003', 'class' => 'bg-teal-100 text-teal-800', 'icon' => 'fa-people-roof'],
    'warga' => ['label' => 'Warga (RT 003)', 'class' => 'bg-amber-100 text-amber-800', 'icon' => 'fa-user']
];
$roleInfo = $roleLabels[$curRole] ?? $roleLabels['warga'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?= $pageTitle ?? 'GoDesa - Layanan Digital Desa Cibuntu, Kec. Cibitung' ?></title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2300AA13'><circle cx='12' cy='12' r='10'/></svg>">
  <meta name="theme-color" content="#00AA13">

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">

  <!-- FontAwesome 6 Free Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Tailwind CSS CDN for swift utility layout -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            gojek: {
              green: '#00AA13',
              darkgreen: '#00880D',
              lightgreen: '#E8F8EE',
              dark: '#1C1C1C',
              gray: '#4A4A4A',
              lightgray: '#717171',
              red: '#EE2737',
              blue: '#00AED6'
            }
          }
        }
      }
    }
  </script>

  <!-- GoDesa Custom Style -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-shell">
  <!-- Top Gojek Header -->
  <header class="top-header">
    <div class="location-role-bar">
      <div class="location-badge">
        <i class="fa-solid fa-location-dot"></i>
        <span>Desa Cibuntu</span>
        <span class="text-xs text-gray-400 font-normal">Kec. Cibitung</span>
      </div>
      <button type="button" onclick="openRoleSwitcher()" class="role-badge-btn" title="Klik untuk berganti peran">
        <i class="fa-solid <?= $roleInfo['icon'] ?>"></i>
        <span><?= $roleInfo['label'] ?></span>
        <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
      </button>
    </div>

    <!-- Gojek Search Bar -->
    <div class="search-pill-container">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="globalAppSearch" placeholder="Cari layanan surat, laporan, agenda...">
    </div>
  </header>
