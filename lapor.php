<?php
// lapor.php - GoLapor: Layanan Pengaduan & Aspirasi Warga Desa Cibuntu
$pageTitle = 'GoLapor - Pengaduan Warga Desa Cibuntu, Kec. Cibitung';
require_once __DIR__ . '/includes/header.php';

$filter = $_GET['filter'] ?? 'all';
$query = "SELECT l.*, u.nama as nama_pelapor, u.rt, u.rw FROM laporan l JOIN users u ON l.user_id = u.id";

if ($filter !== 'all') {
    $query .= " WHERE l.status = " . $pdo->quote($filter);
}
$query .= " ORDER BY l.created_at DESC";
$laporanList = $pdo->query($query)->fetchAll();
?>

<!-- Header Top Banner for GoLapor -->
<div class="px-4 pt-4 pb-2">
  <div class="bg-gradient-to-r from-red-600 to-rose-500 rounded-2xl p-4 text-white shadow-lg flex items-center justify-between">
    <div>
      <span class="text-[10px] font-extrabold uppercase bg-white/25 px-2 py-0.5 rounded-full">GoLapor Cibuntu</span>
      <h2 class="text-lg font-extrabold mt-1">Lapor Keluhan & Aspirasi</h2>
      <p class="text-xs text-red-100 mt-0.5">Sampaikan masalah fasilitas & lingkungan langsung ke perangkat Desa Cibuntu.</p>
    </div>
    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl flex-shrink-0">
      <i class="fa-solid fa-bullhorn"></i>
    </div>
  </div>
</div>

<!-- Form Buat Laporan Baru -->
<div class="px-4 my-2">
  <button type="button" onclick="toggleFormLapor()" class="btn-primary w-full py-3">
    <i class="fa-solid fa-plus"></i>
    <span>Buat Laporan Baru</span>
  </button>
</div>

<div id="formLaporSection" class="hidden px-4 mb-4">
  <div class="form-card mx-0 my-0">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
        <i class="fa-solid fa-pen-to-square text-red-500"></i> Form Aduan Warga Cibuntu
      </h3>
      <button type="button" onclick="toggleFormLapor()" class="text-gray-400 hover:text-gray-600 text-xs">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    </div>

    <form action="actions/handler.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="submit_laporan">
      
      <div class="form-group">
        <label class="form-label">Kategori Masalah</label>
        <select name="kategori" class="form-control" required>
          <option value="Infrastruktur">Jalan / Jembatan / PJU Rusak</option>
          <option value="Kebersihan">Sampah Liar / Saluran Mampet</option>
          <option value="Keamanan">Ketertiban & Keamanan Lingkungan</option>
          <option value="Pelayanan">Pelayanan Administrasi & Bansos</option>
          <option value="Bencana">Banjir / Bencana Alam</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Judul Laporan</label>
        <input type="text" name="judul" class="form-control" placeholder="Contoh: Lampu PJU Dusun II Padam" required>
      </div>

      <div class="form-group">
        <label class="form-label">Lokasi Kejadian / Aduan</label>
        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Depan Masjid Jami RT 04 RW 02" required>
      </div>

      <div class="form-group">
        <label class="form-label">Detail Deskripsi Masalah</label>
        <textarea name="deskripsi" class="form-control" placeholder="Jelaskan secara rinci kondisi dan urgensi masalah..." required></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Foto Bukti (Opsional)</label>
        <input type="file" name="foto" accept="image/*" class="form-control text-xs">
      </div>

      <button type="submit" class="btn-primary w-full">
        <i class="fa-solid fa-paper-plane"></i> Kirim Laporan Sekarang
      </button>
    </form>
  </div>
</div>

<!-- Filter Tabs -->
<div class="px-4 mb-3">
  <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
    <a href="lapor.php?filter=all" class="px-3 py-1.5 rounded-full font-bold transition whitespace-nowrap <?= $filter === 'all' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200' ?>">
      Semua
    </a>
    <a href="lapor.php?filter=menunggu" class="px-3 py-1.5 rounded-full font-bold transition whitespace-nowrap <?= $filter === 'menunggu' ? 'bg-amber-600 text-white' : 'bg-white text-gray-600 border border-gray-200' ?>">
      Menunggu
    </a>
    <a href="lapor.php?filter=diproses" class="px-3 py-1.5 rounded-full font-bold transition whitespace-nowrap <?= $filter === 'diproses' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200' ?>">
      Diproses
    </a>
    <a href="lapor.php?filter=selesai" class="px-3 py-1.5 rounded-full font-bold transition whitespace-nowrap <?= $filter === 'selesai' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border border-gray-200' ?>">
      Selesai
    </a>
  </div>
</div>

<!-- List Laporan -->
<div class="feed-section">
  <?php if (empty($laporanList)): ?>
    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100">
      <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-xl mb-2">
        <i class="fa-solid fa-inbox"></i>
      </div>
      <p class="text-xs text-gray-500 font-semibold">Belum ada data laporan dengan filter ini.</p>
    </div>
  <?php else: ?>
    <?php foreach ($laporanList as $l): ?>
      <div class="card-item mb-3">
        <div class="card-header-row">
          <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-xs font-extrabold">
              <?= strtoupper(substr($l['nama_pelapor'], 0, 1)) ?>
            </span>
            <div>
              <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($l['nama_pelapor']) ?></div>
              <div class="text-[10px] text-gray-400">RT <?= $l['rt'] ?>/RW <?= $l['rw'] ?> • <?= date('d M Y H:i', strtotime($l['created_at'])) ?></div>
            </div>
          </div>
          <span class="badge-status status-<?= $l['status'] ?>"><?= $l['status'] ?></span>
        </div>

        <div class="mb-2">
          <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-[10px] font-bold text-gray-600 mb-1">
            <i class="fa-solid fa-tag text-[9px] mr-1"></i><?= htmlspecialchars($l['kategori']) ?>
          </span>
          <h4 class="text-sm font-extrabold text-gray-900"><?= htmlspecialchars($l['judul']) ?></h4>
          <p class="text-xs text-gray-600 mt-1 leading-relaxed"><?= nl2br(htmlspecialchars($l['deskripsi'])) ?></p>
        </div>

        <?php if (!empty($l['foto'])): ?>
          <div class="mb-3 rounded-xl overflow-hidden border border-gray-100">
            <img src="<?= htmlspecialchars($l['foto']) ?>" alt="Foto Bukti" class="w-full h-44 object-cover">
          </div>
        <?php endif; ?>

        <div class="flex items-center gap-1 text-[11px] text-gray-500 mb-2">
          <i class="fa-solid fa-location-dot text-red-500"></i>
          <span><?= htmlspecialchars($l['lokasi']) ?></span>
        </div>

        <!-- Tanggapan Petugas Jika Ada -->
        <?php if (!empty($l['tanggapan'])): ?>
          <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mt-2 text-xs">
            <div class="font-extrabold text-blue-900 flex items-center gap-1.5 mb-1">
              <i class="fa-solid fa-reply"></i>
              <span>Respon Petugas: <?= htmlspecialchars($l['petugas_nama'] ?? 'Perangkat Desa Cibuntu') ?></span>
            </div>
            <p class="text-blue-800 leading-normal"><?= nl2br(htmlspecialchars($l['tanggapan'])) ?></p>
          </div>
        <?php endif; ?>

        <!-- Action Buttons for Staff/Lurah/Admin/RW/RT -->
        <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between gap-2">
          <?php if (hasRole(['admin', 'lurah', 'staff', 'rw', 'rt'])): ?>
            <button type="button" onclick="openResponseModal(<?= $l['id'] ?>, '<?= htmlspecialchars(addslashes($l['judul'])) ?>', '<?= $l['status'] ?>', '<?= htmlspecialchars(addslashes($l['tanggapan'] ?? '')) ?>')" class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm">
              <i class="fa-solid fa-reply-all"></i> Tanggapi / Update Status
            </button>
          <?php else: ?>
            <div class="text-[11px] text-gray-400">ID Tiket: #LP-<?= sprintf("%04d", $l['id']) ?></div>
          <?php endif; ?>

          <?php if (hasRole(['admin'])): ?>
            <button type="button" onclick="confirmDelete('actions/handler.php?action=delete_item&type=laporan&id=<?= $l['id'] ?>&redirect=lapor.php', '<?= htmlspecialchars(addslashes($l['judul'])) ?>')" class="py-2 px-3 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 font-bold text-xs" title="Hapus Laporan">
              <i class="fa-solid fa-trash"></i>
            </button>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Modal Tanggapi Petugas (MaoneArt Style) -->
<div id="responseModal" class="maoneart-modal-backdrop">
  <div class="maoneart-modal-card text-left">
    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
      <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">
        <i class="fa-solid fa-user-shield"></i>
      </div>
      <div>
        <h4 class="text-sm font-extrabold text-gray-900">Tindak Lanjut Laporan</h4>
        <div id="modalLaporJudul" class="text-xs text-gray-500 truncate max-w-[240px]"></div>
      </div>
    </div>

    <form action="actions/handler.php" method="POST">
      <input type="hidden" name="action" value="update_laporan">
      <input type="hidden" name="id" id="modalLaporId">

      <div class="form-group mb-3">
        <label class="form-label">Status Penanganan</label>
        <select name="status" id="modalLaporStatus" class="form-control" required>
          <option value="menunggu">Menunggu Tindakan</option>
          <option value="diproses">Sedang Diproses Satgas / Teknisi Desa</option>
          <option value="selesai">Selesai Ditangani</option>
          <option value="ditolak">Ditolak / Di luar Kewenangan Desa</option>
        </select>
      </div>

      <div class="form-group mb-4">
        <label class="form-label">Tanggapan & Penjelasan Petugas</label>
        <textarea name="tanggapan" id="modalLaporTanggapan" class="form-control" placeholder="Tuliskan perkembangan perbaikan, teknisi yang dikirim, atau catatan untuk pelapor..." required></textarea>
      </div>

      <!-- 100% Symmetrical 2-Column Action Buttons Grid -->
      <div class="maoneart-modal-actions">
        <button type="button" onclick="closeResponseModal()" class="maoneart-modal-btn cancel">
          Batal
        </button>
        <button type="submit" class="maoneart-modal-btn primary">
          Simpan Status
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleFormLapor() {
  const formSection = document.getElementById('formLaporSection');
  formSection.classList.toggle('hidden');
  if (!formSection.classList.contains('hidden')) {
    formSection.scrollIntoView({ behavior: 'smooth' });
  }
}

function openResponseModal(id, judul, status, tanggapan) {
  document.getElementById('modalLaporId').value = id;
  document.getElementById('modalLaporJudul').innerText = judul;
  document.getElementById('modalLaporStatus').value = status;
  document.getElementById('modalLaporTanggapan').value = tanggapan;
  
  const modal = document.getElementById('responseModal');
  modal.classList.add('active');
}

function closeResponseModal() {
  document.getElementById('responseModal').classList.remove('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
