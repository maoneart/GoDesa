<?php
// surat.php - GoSurat: Pelayanan Administrasi & Pembuatan Dokumen Desa Cibuntu
$pageTitle = 'GoSurat - Layanan Dokumen Desa Cibuntu, Kec. Cibitung';
require_once __DIR__ . '/includes/header.php';

$curUser = currentUser();
$curRole = currentRole();

// Filter surat berdasarkan tingkatan role (RBAC Berjenjang RT/RW/Desa):
if (hasRole(['admin', 'lurah', 'kades', 'staff'])) {
    $suratList = $pdo->query("
        SELECT s.*, u.nama as pemohon_nama, u.nik as pemohon_nik, u.no_kk, u.tempat_lahir, u.tanggal_lahir, u.jenis_kelamin, u.agama, u.status_perkawinan, u.pekerjaan, u.rt, u.rw, u.no_hp
        FROM surat s 
        JOIN users u ON s.user_id = u.id 
        ORDER BY s.created_at DESC
    ")->fetchAll();
    $scopeTitle = 'Daftar Pengajuan Surat Seluruh Desa Cibuntu';
} elseif (hasRole('rw')) {
    $stmt = $pdo->prepare("
        SELECT s.*, u.nama as pemohon_nama, u.nik as pemohon_nik, u.no_kk, u.tempat_lahir, u.tanggal_lahir, u.jenis_kelamin, u.agama, u.status_perkawinan, u.pekerjaan, u.rt, u.rw, u.no_hp
        FROM surat s 
        JOIN users u ON s.user_id = u.id 
        WHERE u.rw = ? 
        ORDER BY s.created_at DESC
    ");
    $stmt->execute([$curUser['rw']]);
    $suratList = $stmt->fetchAll();
    $scopeTitle = 'Pengajuan Surat Lingkungan RW ' . htmlspecialchars($curUser['rw']);
} elseif (hasRole('rt')) {
    $stmt = $pdo->prepare("
        SELECT s.*, u.nama as pemohon_nama, u.nik as pemohon_nik, u.no_kk, u.tempat_lahir, u.tanggal_lahir, u.jenis_kelamin, u.agama, u.status_perkawinan, u.pekerjaan, u.rt, u.rw, u.no_hp
        FROM surat s 
        JOIN users u ON s.user_id = u.id 
        WHERE u.rt = ? AND u.rw = ? 
        ORDER BY s.created_at DESC
    ");
    $stmt->execute([$curUser['rt'], $curUser['rw']]);
    $suratList = $stmt->fetchAll();
    $scopeTitle = 'Pengajuan Surat Warga RT ' . htmlspecialchars($curUser['rt']) . ' / RW ' . htmlspecialchars($curUser['rw']);
} else {
    $stmt = $pdo->prepare("
        SELECT s.*, u.nama as pemohon_nama, u.nik as pemohon_nik, u.no_kk, u.tempat_lahir, u.tanggal_lahir, u.jenis_kelamin, u.agama, u.status_perkawinan, u.pekerjaan, u.rt, u.rw, u.no_hp
        FROM surat s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.user_id = ? 
        ORDER BY s.created_at DESC
    ");
    $stmt->execute([$curUser['id']]);
    $suratList = $stmt->fetchAll();
    $scopeTitle = 'Riwayat Dokumen Surat Saya';
}
?>

<!-- Header Top Banner for GoSurat -->
<div class="px-4 pt-4 pb-2">
  <div class="bg-gradient-to-r from-emerald-600 to-green-600 rounded-2xl p-4 text-white shadow-lg flex items-center justify-between">
    <div>
      <span class="text-[10px] font-extrabold uppercase bg-white/25 px-2 py-0.5 rounded-full">GoSurat Cibuntu</span>
      <h2 class="text-lg font-extrabold mt-1">Layanan Administrasi Desa</h2>
      <p class="text-xs text-green-100 mt-0.5">SOP Berjenjang: Pengantar RT ➔ RW ➔ Pelayanan Desa ➔ TTD Kades.</p>
    </div>
    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl flex-shrink-0">
      <i class="fa-solid fa-file-signature"></i>
    </div>
  </div>
</div>

<!-- SOP Flow Banner Information -->
<div class="px-4 my-2">
  <div class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm text-xs">
    <div class="text-[11px] font-bold text-gray-500 mb-2 flex items-center gap-1.5">
      <i class="fa-solid fa-route text-emerald-600"></i> Alur Administrasi SOP Permendagri:
    </div>
    <div class="grid grid-cols-4 gap-1 text-center font-bold text-[10px]">
      <div class="p-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
        <i class="fa-solid fa-people-roof block text-xs mb-0.5"></i>
        1. Pengantar RT
      </div>
      <div class="p-1.5 rounded-lg bg-indigo-50 text-indigo-800 border border-indigo-200">
        <i class="fa-solid fa-sitemap block text-xs mb-0.5"></i>
        2. Ketahui RW
      </div>
      <div class="p-1.5 rounded-lg bg-blue-50 text-blue-800 border border-blue-200">
        <i class="fa-solid fa-id-badge block text-xs mb-0.5"></i>
        3. Loket Desa
      </div>
      <div class="p-1.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200">
        <i class="fa-solid fa-signature block text-xs mb-0.5"></i>
        4. TTD Kades
      </div>
    </div>
  </div>
</div>

<!-- Tombol Buat Permohonan Baru -->
<div class="px-4 my-2">
  <button type="button" onclick="toggleFormSurat()" class="btn-primary w-full py-3">
    <i class="fa-solid fa-file-circle-plus"></i>
    <span>Ajukan Permohonan Dokumen Baru</span>
  </button>
</div>

<!-- Form Pengajuan Surat Baru -->
<div id="formSuratSection" class="hidden px-4 mb-4">
  <div class="form-card mx-0 my-0">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
        <i class="fa-solid fa-file-pen text-green-600"></i> Form Permohonan Surat Resmi
      </h3>
      <button type="button" onclick="toggleFormSurat()" class="text-gray-400 hover:text-gray-600 text-xs">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    </div>

    <!-- Data Pemohon Ringkas -->
    <div class="p-3 bg-gray-50 rounded-xl mb-3 border border-gray-200 text-xs">
      <div class="text-[11px] font-bold text-gray-500 mb-1">Identitas Pemohon (Data Kependudukan):</div>
      <div class="font-bold text-gray-900"><?= htmlspecialchars($curUser['nama']) ?> (<?= htmlspecialchars($curUser['nik']) ?>)</div>
      <div class="text-gray-600 mt-0.5">No. KK: <span class="font-mono"><?= htmlspecialchars($curUser['no_kk'] ?? '3216071405950002') ?></span> • RT <?= htmlspecialchars($curUser['rt']) ?> / RW <?= htmlspecialchars($curUser['rw']) ?></div>
    </div>

    <form action="actions/handler.php" method="POST">
      <input type="hidden" name="action" value="submit_surat">
      
      <div class="form-group">
        <label class="form-label">Jenis Dokumen Surat</label>
        <select name="jenis_surat" id="selectJenisSurat" class="form-control" onchange="onJenisSuratChange(this.value)" required>
          <option value="">-- Pilih Jenis Dokumen --</option>
          <option value="Surat Keterangan Usaha (SKU)">Surat Keterangan Usaha (SKU) [Kode 503]</option>
          <option value="Surat Pengantar SKCK">Surat Pengantar SKCK Polsek [Kode 300]</option>
          <option value="Surat Keterangan Domisili">Surat Keterangan Domisili [Kode 470]</option>
          <option value="Surat Keterangan Tidak Mampu (SKTM)">Surat Keterangan Tidak Mampu (SKTM) [Kode 401]</option>
          <option value="Surat Keterangan Belum Menikah">Surat Keterangan Belum Menikah [Kode 472]</option>
          <option value="Surat Keterangan Kematian">Surat Keterangan Kematian [Kode 474.3]</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Keperluan Pengajuan Surat</label>
        <input type="text" name="keperluan" class="form-control" placeholder="Contoh: Pengajuan KUR Bank Mandiri / Melamar Kerja di MM2100" required>
      </div>

      <!-- Field Dinamis SKU -->
      <div id="fieldSku" class="hidden space-y-3 p-3 bg-emerald-50 rounded-xl mb-3 border border-emerald-200">
        <div class="text-xs font-bold text-emerald-900"><i class="fa-solid fa-store mr-1"></i> Data Usaha Pemohon (SKU):</div>
        <div>
          <label class="form-label text-emerald-900">Nama Usaha / Toko</label>
          <input type="text" name="nama_usaha" class="form-control" placeholder="Contoh: Toko Berkah Cibuntu">
        </div>
        <div>
          <label class="form-label text-emerald-900">Bidang / Jenis Usaha</label>
          <input type="text" name="bidang_usaha" class="form-control" placeholder="Contoh: Perdagangan Sembako / Bengkel Motor">
        </div>
        <div>
          <label class="form-label text-emerald-900">Alamat Tempat Usaha</label>
          <input type="text" name="alamat_usaha" class="form-control" placeholder="Contoh: Kp. Cibuntu RT 003 / RW 001">
        </div>
        <div>
          <label class="form-label text-emerald-900">Beroperasi Sejak Tahun</label>
          <input type="number" name="sejak_tahun" class="form-control" placeholder="Contoh: 2021" min="1990" max="2026">
        </div>
      </div>

      <!-- Field Dinamis SKCK -->
      <div id="fieldSkck" class="hidden space-y-3 p-3 bg-blue-50 rounded-xl mb-3 border border-blue-200">
        <div class="text-xs font-bold text-blue-900"><i class="fa-solid fa-shield-halved mr-1"></i> Data Pengantar SKCK:</div>
        <div>
          <label class="form-label text-blue-900">Tujuan Kantor Kepolisian</label>
          <input type="text" name="tujuan_instansi" class="form-control" value="Polsek Cikarang Barat / Polres Metro Bekasi">
        </div>
      </div>

      <!-- Field Dinamis Domisili -->
      <div id="fieldDomisili" class="hidden space-y-3 p-3 bg-teal-50 rounded-xl mb-3 border border-teal-200">
        <div class="text-xs font-bold text-teal-900"><i class="fa-solid fa-house-user mr-1"></i> Data Domisili Tempat Tinggal:</div>
        <div>
          <label class="form-label text-teal-900">Lama Tinggal di Desa Cibuntu</label>
          <input type="text" name="lama_tinggal" class="form-control" placeholder="Contoh: 5 Tahun / Sejak Lahir">
        </div>
        <div>
          <label class="form-label text-teal-900">Status Tempat Tinggal</label>
          <select name="status_tinggal" class="form-control">
            <option value="Rumah Sendiri">Rumah Milik Sendiri</option>
            <option value="Sewa / Kontrak">Sewa / Kontrak</option>
            <option value="Menumpang Keluarga">Menumpang Keluarga</option>
          </select>
        </div>
      </div>

      <!-- Field Dinamis SKTM -->
      <div id="fieldSktm" class="hidden space-y-3 p-3 bg-amber-50 rounded-xl mb-3 border border-amber-200">
        <div class="text-xs font-bold text-amber-900"><i class="fa-solid fa-hand-holding-heart mr-1"></i> Keterangan Tidak Mampu (SKTM):</div>
        <div>
          <label class="form-label text-amber-900">Tujuan Pengajuan SKTM</label>
          <select name="keterangan_tujuan" class="form-control">
            <option value="KIP Kuliah / Beasiswa Pendidikan">KIP Kuliah / Beasiswa Pendidikan</option>
            <option value="Keringanan Biaya Rumah Sakit">Keringanan Biaya Rumah Sakit</option>
            <option value="Pengajuan BPJS PBI Gratis">Pengajuan BPJS PBI Gratis</option>
            <option value="Bantuan Hukum / Prodeo">Bantuan Hukum / Pengadilan (Prodeo)</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Catatan Tambahan (Opsional)</label>
        <textarea name="keterangan_lain" class="form-control" placeholder="Keterangan lain yang diperlukan..."></textarea>
      </div>

      <button type="submit" class="btn-primary w-full py-3">
        <i class="fa-solid fa-paper-plane"></i> Kirim Permohonan ke Ketua RT
      </button>
    </form>
  </div>
</div>

<!-- List Riwayat Surat -->
<div class="feed-section">
  <div class="section-title">
    <span><?= $scopeTitle ?></span>
    <span class="text-xs text-gray-500 font-normal"><?= count($suratList) ?> Dokumen</span>
  </div>

  <?php if (empty($suratList)): ?>
    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100">
      <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-xl mb-2">
        <i class="fa-solid fa-folder-open"></i>
      </div>
      <p class="text-xs text-gray-500 font-semibold">Belum ada dokumen surat pada lingkup ini.</p>
    </div>
  <?php else: ?>
    <?php foreach ($suratList as $s): ?>
      <?php 
        $isDisetujui = in_array($s['status'], ['disetujui_kades', 'disetujui_lurah']);
      ?>
      <div class="card-item mb-3">
        <div class="card-header-row">
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-sm font-extrabold flex-shrink-0">
              <i class="fa-solid fa-file-lines"></i>
            </span>
            <div>
              <h4 class="text-xs font-bold text-gray-900"><?= htmlspecialchars($s['jenis_surat']) ?></h4>
              <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($s['nomor_surat']) ?></div>
            </div>
          </div>
          <span class="badge-status status-<?= $s['status'] ?>">
            <?php 
              if ($s['status'] === 'diajukan') echo '1/4 Menunggu RT';
              elseif ($s['status'] === 'diverifikasi_rt') echo '2/4 Menunggu RW';
              elseif ($s['status'] === 'diverifikasi_rw') echo '3/4 Loket Desa';
              elseif ($s['status'] === 'diverifikasi_staff') echo '4/4 Menunggu Kades';
              elseif ($isDisetujui) echo 'Disetujui Kades (Sah)';
              else echo 'Ditolak';
            ?>
          </span>
        </div>

        <!-- Rincian Pemohon & Rantai Nomor SOP -->
        <div class="my-2 bg-gray-50 p-2.5 rounded-xl text-xs space-y-1.5">
          <div class="flex justify-between">
            <span class="text-gray-500">Pemohon:</span>
            <span class="font-bold text-gray-800"><?= htmlspecialchars($s['pemohon_nama']) ?> (RT <?= $s['rt'] ?>/RW <?= $s['rw'] ?>)</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">NIK / KK:</span>
            <span class="font-mono text-gray-700"><?= htmlspecialchars($s['pemohon_nik']) ?> / <?= htmlspecialchars($s['no_kk'] ?? '-') ?></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500">Keperluan:</span>
            <span class="font-semibold text-gray-800"><?= htmlspecialchars($s['keperluan']) ?></span>
          </div>

          <!-- Rantai Pengantar RT & RW -->
          <?php if (!empty($s['nomor_pengantar_rt'])): ?>
          <div class="pt-1 border-t border-gray-200 text-teal-800 flex justify-between items-center text-[11px]">
            <span><i class="fa-solid fa-stamp mr-1"></i> Pengantar RT:</span>
            <span class="font-mono font-bold"><?= htmlspecialchars($s['nomor_pengantar_rt']) ?></span>
          </div>
          <?php endif; ?>

          <?php if (!empty($s['nomor_pengantar_rw'])): ?>
          <div class="text-indigo-800 flex justify-between items-center text-[11px]">
            <span><i class="fa-solid fa-sitemap mr-1"></i> Pengantar RW:</span>
            <span class="font-mono font-bold"><?= htmlspecialchars($s['nomor_pengantar_rw']) ?></span>
          </div>
          <?php endif; ?>

          <?php if (!empty($s['catatan_rt'])): ?>
          <div class="text-[11px] text-teal-700 bg-teal-50/70 p-1.5 rounded-lg">
            <strong>Catatan RT:</strong> <?= htmlspecialchars($s['catatan_rt']) ?>
          </div>
          <?php endif; ?>

          <?php if (!empty($s['catatan_rw'])): ?>
          <div class="text-[11px] text-indigo-700 bg-indigo-50/70 p-1.5 rounded-lg">
            <strong>Catatan RW:</strong> <?= htmlspecialchars($s['catatan_rw']) ?>
          </div>
          <?php endif; ?>

          <?php if (!empty($s['catatan'])): ?>
          <div class="text-[11px] text-emerald-700 bg-emerald-50/70 p-1.5 rounded-lg">
            <strong>Catatan Desa:</strong> <?= htmlspecialchars($s['catatan']) ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Tombol Aksi Berdasarkan Alur SOP Desa -->
        <div class="mt-3 pt-2 border-t border-gray-100 flex flex-wrap gap-2 items-center justify-between">
          <!-- 1. Jika Disetujui Kades -> Buka Format Cetak Resmi -->
          <?php if ($isDisetujui): ?>
            <a href="cetak_surat.php?id=<?= $s['id'] ?>" target="_blank" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
              <i class="fa-solid fa-print"></i> Cetak Dokumen Sah (PDF)
            </a>
          <?php endif; ?>

          <!-- 2. Tindakan Ketua RT: Verifikasi & Sahkan Pengantar RT -->
          <?php if (hasRole(['rt', 'admin']) && $s['status'] === 'diajukan'): ?>
            <form action="actions/handler.php" method="POST" class="flex-1">
              <input type="hidden" name="action" value="verifikasi_rt">
              <input type="hidden" name="id" value="<?= $s['id'] ?>">
              <button type="submit" class="w-full py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-stamp"></i> Setujui Pengantar RT <?= $s['rt'] ?>
              </button>
            </form>
          <?php endif; ?>

          <!-- 3. Tindakan Ketua RW: Sahkan & Mengetahui Pengantar RW -->
          <?php if (hasRole(['rw', 'admin']) && $s['status'] === 'diverifikasi_rt'): ?>
            <form action="actions/handler.php" method="POST" class="flex-1">
              <input type="hidden" name="action" value="verifikasi_rw">
              <input type="hidden" name="id" value="<?= $s['id'] ?>">
              <button type="submit" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-sitemap"></i> Sahkan Mengetahui RW <?= $s['rw'] ?>
              </button>
            </form>
          <?php endif; ?>

          <!-- 4. Tindakan Staff Kantor Desa: Verifikasi Berkas Kelurahan -->
          <?php if (hasRole(['staff', 'admin']) && in_array($s['status'], ['diverifikasi_rw', 'diverifikasi_rt'])): ?>
            <form action="actions/handler.php" method="POST" class="flex-1">
              <input type="hidden" name="action" value="verifikasi_surat">
              <input type="hidden" name="id" value="<?= $s['id'] ?>">
              <button type="submit" class="w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-clipboard-check"></i> Verifikasi Berkas Desa
              </button>
            </form>
          <?php endif; ?>

          <!-- 5. Tindakan Kepala Desa: Persetujuan & Tanda Tangan Digital (e-Signature QR) -->
          <?php if (hasRole(['lurah', 'kades', 'admin']) && in_array($s['status'], ['diverifikasi_staff', 'diverifikasi_rw'])): ?>
            <button type="button" onclick="confirmApproval(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['pemohon_nama'])) ?>', '<?= htmlspecialchars(addslashes($s['jenis_surat'])) ?>')" class="flex-1 py-2 px-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
              <i class="fa-solid fa-signature"></i> Tanda Tangan & Setujui Kades
            </button>
          <?php endif; ?>

          <!-- 6. Tolak Surat (RT, RW, Staff, Kades, Admin) -->
          <?php if (hasRole(['admin', 'lurah', 'kades', 'staff', 'rw', 'rt']) && in_array($s['status'], ['diajukan', 'diverifikasi_rt', 'diverifikasi_rw', 'diverifikasi_staff'])): ?>
            <button type="button" onclick="openRejectModal(<?= $s['id'] ?>)" class="py-2 px-3 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 font-bold text-xs">
              <i class="fa-solid fa-ban"></i> Tolak
            </button>
          <?php endif; ?>

          <!-- 7. Hapus data (Admin only) -->
          <?php if (hasRole(['admin'])): ?>
            <button type="button" onclick="confirmDelete('actions/handler.php?action=delete_item&type=surat&id=<?= $s['id'] ?>&redirect=surat.php', '<?= htmlspecialchars(addslashes($s['jenis_surat'])) ?> - <?= htmlspecialchars(addslashes($s['pemohon_nama'])) ?>')" class="py-2 px-3 rounded-xl bg-gray-100 text-gray-500 hover:text-red-600 font-bold text-xs">
              <i class="fa-solid fa-trash"></i>
            </button>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Modal Tolak Surat (MaoneArt Style) -->
<div id="rejectModal" class="maoneart-modal-backdrop">
  <div class="maoneart-modal-card text-left">
    <h4 class="text-sm font-extrabold text-gray-900 mb-2">Tolak Permohonan Dokumen</h4>
    <form action="actions/handler.php" method="POST">
      <input type="hidden" name="action" value="reject_surat">
      <input type="hidden" name="id" id="rejectSuratId">
      
      <div class="form-group mb-4">
        <label class="form-label">Alasan Penolakan SOP</label>
        <textarea name="alasan" class="form-control" placeholder="Contoh: Alamat tempat usaha belum terverifikasi oleh pengantar RT atau data KK tidak cocok..." required></textarea>
      </div>

      <!-- 100% Symmetrical 2-Column Action Buttons Grid -->
      <div class="maoneart-modal-actions">
        <button type="button" onclick="closeRejectModal()" class="maoneart-modal-btn cancel">
          Batal
        </button>
        <button type="submit" class="maoneart-modal-btn danger">
          Tolak Permohonan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleFormSurat() {
  const formSection = document.getElementById('formSuratSection');
  formSection.classList.toggle('hidden');
  if (!formSection.classList.contains('hidden')) {
    formSection.scrollIntoView({ behavior: 'smooth' });
  }
}

function onJenisSuratChange(val) {
  document.getElementById('fieldSku').classList.add('hidden');
  document.getElementById('fieldSkck').classList.add('hidden');
  document.getElementById('fieldDomisili').classList.add('hidden');
  document.getElementById('fieldSktm').classList.add('hidden');

  if (val === 'Surat Keterangan Usaha (SKU)') {
    document.getElementById('fieldSku').classList.remove('hidden');
  } else if (val === 'Surat Pengantar SKCK') {
    document.getElementById('fieldSkck').classList.remove('hidden');
  } else if (val === 'Surat Keterangan Domisili') {
    document.getElementById('fieldDomisili').classList.remove('hidden');
  } else if (val === 'Surat Keterangan Tidak Mampu (SKTM)') {
    document.getElementById('fieldSktm').classList.remove('hidden');
  }
}

function confirmApproval(suratId, namaPemohon, jenisSurat) {
  showConfirmModal({
    title: 'Pengesahan Kepala Desa',
    message: `Anda akan memberikan persetujuan resmi dan tanda tangan digital Kepala Desa Cibuntu untuk <strong>${jenisSurat}</strong> atas nama pemohon <strong>${namaPemohon}</strong>. Dokumen siap dicetak secara sah.`,
    confirmText: 'Sahkan & TTD',
    cancelText: 'Batal',
    isDanger: false,
    icon: 'fa-file-signature',
    onConfirm: () => {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'actions/handler.php';
      form.innerHTML = `
        <input type="hidden" name="action" value="approve_surat">
        <input type="hidden" name="id" value="${suratId}">
      `;
      document.body.appendChild(form);
      form.submit();
    }
  });
}

function openRejectModal(id) {
  document.getElementById('rejectSuratId').value = id;
  document.getElementById('rejectModal').classList.add('active');
}

function closeRejectModal() {
  document.getElementById('rejectModal').classList.remove('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
