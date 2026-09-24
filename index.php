<?php
// index.php - Main Gojek-Themed Village Dashboard
$pageTitle = 'GoDesa - Aplikasi Perangkat Desa Cibuntu, Kec. Cibitung';
require_once __DIR__ . '/includes/header.php';

// Fetch Statistics
$countSuratPending = $pdo->query("SELECT COUNT(*) FROM surat WHERE status IN ('diajukan', 'diverifikasi_rt', 'diverifikasi_rw', 'diverifikasi_staff')")->fetchColumn();
$countLaporanAktif = $pdo->query("SELECT COUNT(*) FROM laporan WHERE status IN ('menunggu', 'diproses')")->fetchColumn();
$countAgenda = $pdo->query("SELECT COUNT(*) FROM agenda WHERE tanggal >= DATE('now')")->fetchColumn();

// Fetch Pinned / Latest Announcements (GoWarta)
$pengumuman = $pdo->query("SELECT * FROM pengumuman ORDER BY is_pinned DESC, created_at DESC LIMIT 3")->fetchAll();

// Fetch Latest Reports (GoLapor)
$laporanTerbaru = $pdo->query("
    SELECT l.*, u.nama as nama_pelapor, u.rt, u.rw 
    FROM laporan l 
    JOIN users u ON l.user_id = u.id 
    ORDER BY l.created_at DESC 
    LIMIT 3
")->fetchAll();

// Fetch Upcoming Schedule (GoAgenda)
$agendaTerdekat = $pdo->query("
    SELECT * FROM agenda 
    WHERE tanggal >= DATE('now') 
    ORDER BY tanggal ASC, waktu_mulai ASC 
    LIMIT 2
")->fetchAll();
?>

<!-- 1. Gojek-Style Village Wallet Card (GoDesa Pay / Warga ID Card) -->
<section class="godesa-wallet-card">
  <div class="wallet-top">
    <div class="wallet-brand">
      <div class="logo-icon"><i class="fa-solid fa-tree-city"></i></div>
      <div>
        <div class="title">GoDesa Cibuntu</div>
        <div class="text-[11px] opacity-80"><?= htmlspecialchars($activeUser['nik'] ?? '3216071405950001') ?></div>
      </div>
    </div>
    <div class="wallet-user-info">
      <span class="font-bold"><?= htmlspecialchars($activeUser['nama'] ?? 'Hermawan') ?></span><br>
      <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full uppercase tracking-wider font-extrabold"><?= currentRole() ?> • <?= ($activeUser['rt'] !== '-' && !empty($activeUser['rt']) ? 'RT ' . $activeUser['rt'] . ' ' : '') ?>RW <?= $activeUser['rw'] ?></span>
    </div>
  </div>

  <div class="wallet-balance-row">
    <div>
      <div class="wallet-balance-label">Status Partisipasi Warga</div>
      <div class="wallet-balance-amount">Warga Aktif</div>
    </div>
    <div class="text-right">
      <div class="text-[10px] opacity-75">Iuran Kas RT:</div>
      <div class="text-sm font-bold text-yellow-300">Lunas September 2026</div>
    </div>
  </div>

  <!-- Wallet Quick Actions -->
  <div class="wallet-actions">
    <a href="surat.php" class="wallet-action-btn">
      <div class="wallet-action-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
      <span>Buat Surat</span>
    </a>
    <a href="lapor.php" class="wallet-action-btn">
      <div class="wallet-action-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <span>Lapor Warga</span>
    </a>
    <a href="agenda.php" class="wallet-action-btn">
      <div class="wallet-action-icon"><i class="fa-solid fa-calendar-check"></i></div>
      <span>Agenda</span>
    </a>
    <div onclick="openRoleSwitcher()" class="wallet-action-btn cursor-pointer">
      <div class="wallet-action-icon"><i class="fa-solid fa-shuffle"></i></div>
      <span>Ganti Peran</span>
    </div>
  </div>
</section>

<!-- 2. Gojek-Style Service 8-Grid (Services Squircles) -->
<section class="service-grid-section">
  <div class="section-title">
    <span>Layanan Digital Desa Cibuntu</span>
    <span class="text-xs text-gray-500 font-medium">Lengkap & Cepat</span>
  </div>

  <div class="service-grid">
    <!-- GoSurat -->
    <a href="surat.php" class="service-item">
      <div class="service-icon-box bg-surat">
        <i class="fa-solid fa-file-contract"></i>
      </div>
      <span class="service-label">GoSurat</span>
    </a>

    <!-- GoLapor -->
    <a href="lapor.php" class="service-item">
      <div class="service-icon-box bg-lapor">
        <i class="fa-solid fa-bullhorn"></i>
      </div>
      <span class="service-label">GoLapor</span>
    </a>

    <!-- GoAgenda -->
    <a href="agenda.php" class="service-item">
      <div class="service-icon-box bg-agenda">
        <i class="fa-solid fa-calendar-days"></i>
      </div>
      <span class="service-label">GoAgenda</span>
    </a>

    <!-- GoWarta -->
    <a href="pengumuman.php" class="service-item">
      <div class="service-icon-box bg-warta">
        <i class="fa-solid fa-newspaper"></i>
      </div>
      <span class="service-label">GoWarta</span>
    </a>

    <!-- GoBansos -->
    <a href="bansos.php" class="service-item">
      <div class="service-icon-box bg-bansos">
        <i class="fa-solid fa-hand-holding-heart"></i>
      </div>
      <span class="service-label">GoBansos</span>
    </a>

    <!-- GoDarurat -->
    <a href="darurat.php" class="service-item">
      <div class="service-icon-box bg-darurat">
        <i class="fa-solid fa-truck-medical"></i>
      </div>
      <span class="service-label">GoDarurat</span>
    </a>

    <!-- GoData -->
    <a href="profil.php" class="service-item">
      <div class="service-icon-box bg-data">
        <i class="fa-solid fa-chart-pie"></i>
      </div>
      <span class="service-label">GoData</span>
    </a>

    <!-- Role Switcher Shortcut -->
    <div onclick="openRoleSwitcher()" class="service-item cursor-pointer">
      <div class="service-icon-box bg-lainnya">
        <i class="fa-solid fa-users-gear"></i>
      </div>
      <span class="service-label">Pilih Role</span>
    </div>
  </div>
</section>

<!-- 3. Gojek-Style Carousel: Pengumuman & Berita Desa -->
<section class="banner-section">
  <div class="section-title">
    <span>Kabar & Pengumuman Desa</span>
    <a href="pengumuman.php" class="more-link">Lihat Semua <i class="fa-solid fa-chevron-right text-[10px]"></i></a>
  </div>

  <div class="banner-carousel">
    <?php foreach ($pengumuman as $idx => $p): ?>
    <div class="banner-card">
      <div class="banner-header" style="background: <?= $idx === 0 ? 'linear-gradient(135deg, #00880D, #00AA13)' : ($idx === 1 ? 'linear-gradient(135deg, #00AED6, #0284C7)' : 'linear-gradient(135deg, #F59E0B, #D97706)') ?>;">
        <span class="banner-tag"><?= htmlspecialchars($p['kategori']) ?></span>
        <div class="text-[11px] opacity-90"><i class="fa-regular fa-clock"></i> <?= date('d M Y', strtotime($p['created_at'])) ?></div>
      </div>
      <div class="banner-body">
        <h4 class="banner-title"><?= htmlspecialchars($p['judul']) ?></h4>
        <p class="banner-desc"><?= htmlspecialchars($p['isi']) ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- 4. Quick Notification / Executive Counter for Perangkat Desa & RT/RW -->
<?php if (hasRole(['admin', 'lurah', 'kades', 'staff', 'rw', 'rt'])): ?>
<section class="px-4 mb-4">
  <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center justify-between">
    <div>
      <div class="text-xs font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
        <i class="fa-solid fa-circle-check text-emerald-600"></i>
        <span>Panel Kerja Perangkat: <?= htmlspecialchars($roleInfo['label'] ?? ucfirst(currentRole())) ?></span>
      </div>
      <div class="text-xs text-gray-600 mt-1">
        Ada <strong><?= $countSuratPending ?> surat</strong> butuh tindakan & <strong><?= $countLaporanAktif ?> aduan</strong> aktif.
      </div>
    </div>
    <a href="surat.php" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition">
      Tinjau
    </a>
  </div>
</section>
<?php endif; ?>

<!-- 5. Upcoming Agenda (GoAgenda Preview) -->
<section class="feed-section">
  <div class="section-title">
    <span>Agenda Warga Cibuntu Mendatang</span>
    <a href="agenda.php" class="more-link">Lihat Jadwal <i class="fa-solid fa-chevron-right text-[10px]"></i></a>
  </div>

  <?php if (empty($agendaTerdekat)): ?>
    <div class="bg-white rounded-2xl p-4 text-center text-sm text-gray-500 border border-gray-100">
      Belum ada agenda kegiatan dalam waktu dekat.
    </div>
  <?php else: ?>
    <?php foreach ($agendaTerdekat as $a): ?>
      <div class="card-item flex gap-3.5 items-start">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex flex-col items-center justify-center font-bold flex-shrink-0 border border-blue-100">
          <span class="text-xs uppercase"><?= date('M', strtotime($a['tanggal'])) ?></span>
          <span class="text-base leading-none"><?= date('d', strtotime($a['tanggal'])) ?></span>
        </div>
        <div class="flex-1">
          <div class="flex justify-between items-start">
            <h4 class="text-sm font-bold text-gray-900 leading-snug"><?= htmlspecialchars($a['nama_kegiatan']) ?></h4>
            <span class="badge-status bg-blue-100 text-blue-700 text-[10px]"><?= htmlspecialchars($a['kategori']) ?></span>
          </div>
          <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
            <i class="fa-regular fa-clock text-[11px] text-gray-400"></i>
            <span><?= htmlspecialchars($a['waktu_mulai']) ?> - <?= htmlspecialchars($a['waktu_selesai'] ?? 'Selesai') ?> WIB</span>
          </div>
          <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
            <i class="fa-solid fa-location-dot text-[11px] text-red-400"></i>
            <span><?= htmlspecialchars($a['lokasi']) ?></span>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<!-- 6. Laporan Warga Terbaru (GoLapor Preview) -->
<section class="feed-section">
  <div class="section-title">
    <span>Laporan Terkini Warga Cibuntu</span>
    <a href="lapor.php" class="more-link">Semua Laporan <i class="fa-solid fa-chevron-right text-[10px]"></i></a>
  </div>

  <?php if (empty($laporanTerbaru)): ?>
    <div class="bg-white rounded-2xl p-4 text-center text-sm text-gray-500 border border-gray-100">
      Belum ada laporan dari warga.
    </div>
  <?php else: ?>
    <?php foreach ($laporanTerbaru as $lap): ?>
      <div class="card-item">
        <div class="card-header-row">
          <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-[10px] font-bold">
              <?= strtoupper(substr($lap['nama_pelapor'], 0, 1)) ?>
            </span>
            <span class="text-xs font-semibold text-gray-800"><?= htmlspecialchars($lap['nama_pelapor']) ?> (RT <?= $lap['rt'] ?>/<?= $lap['rw'] ?>)</span>
          </div>
          <span class="badge-status status-<?= $lap['status'] ?>">
            <?= $lap['status'] ?>
          </span>
        </div>

        <h4 class="text-xs font-bold text-gray-900 mb-1"><?= htmlspecialchars($lap['judul']) ?></h4>
        <p class="text-xs text-gray-500 mb-2 line-clamp-2"><?= htmlspecialchars($lap['deskripsi']) ?></p>

        <div class="flex justify-between items-center text-[11px] text-gray-400 pt-2 border-t border-gray-50">
          <span><i class="fa-solid fa-location-dot mr-1"></i><?= htmlspecialchars($lap['lokasi']) ?></span>
          <span><?= date('d/m/Y', strtotime($lap['created_at'])) ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
