<?php
// pengumuman.php - GoWarta: Warta & Pengumuman Resmi Desa Cibuntu
$pageTitle = 'GoWarta - Informasi & Berita Desa Cibuntu, Kec. Cibitung';
require_once __DIR__ . '/includes/header.php';

$pengumumanList = $pdo->query("
    SELECT p.*, u.nama as nama_penulis, u.role as penulis_role 
    FROM pengumuman p 
    LEFT JOIN users u ON p.penulis_id = u.id 
    ORDER BY p.is_pinned DESC, p.created_at DESC
")->fetchAll();
?>

<!-- Header Top Banner for GoWarta -->
<div class="px-4 pt-4 pb-2">
  <div class="bg-gradient-to-r from-purple-700 to-indigo-600 rounded-2xl p-4 text-white shadow-lg flex items-center justify-between">
    <div>
      <span class="text-[10px] font-extrabold uppercase bg-white/25 px-2 py-0.5 rounded-full">GoWarta Desa</span>
      <h2 class="text-lg font-extrabold mt-1">Pengumuman & Warta</h2>
      <p class="text-xs text-purple-100 mt-0.5">Informasi resmi, program bansos & pengumuman Kepala Desa.</p>
    </div>
    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl flex-shrink-0">
      <i class="fa-solid fa-newspaper"></i>
    </div>
  </div>
</div>

<!-- Tombol Tambah Pengumuman (Admin/Lurah/Staff) -->
<?php if (hasRole(['admin', 'lurah', 'staff'])): ?>
<div class="px-4 my-2">
  <button type="button" onclick="toggleFormPengumuman()" class="btn-primary w-full py-3" style="background: linear-gradient(135deg, #7E3AF2 0%, #6366F1 100%);">
    <i class="fa-solid fa-bullhorn"></i>
    <span>Siarkan Pengumuman Baru</span>
  </button>
</div>

<!-- Form Siarkan Pengumuman Baru -->
<div id="formPengumumanSection" class="hidden px-4 mb-4">
  <div class="form-card mx-0 my-0">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
        <i class="fa-solid fa-bullhorn text-purple-600"></i> Buat Pengumuman Desa
      </h3>
      <button type="button" onclick="toggleFormPengumuman()" class="text-gray-400 hover:text-gray-600 text-xs">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    </div>

    <form action="actions/handler.php" method="POST">
      <input type="hidden" name="action" value="submit_pengumuman">
      
      <div class="form-group">
        <label class="form-label">Judul Pengumuman</label>
        <input type="text" name="judul" class="form-control" placeholder="Contoh: Penyaluran Beras Bantuan Pangan Tahap II" required>
      </div>

      <div class="form-group">
        <label class="form-label">Kategori Informasi</label>
        <select name="kategori" class="form-control" required>
          <option value="Pelayanan">Pelayanan Kependudukan</option>
          <option value="Bansos">Bantuan Sosial (Bansos / BLT)</option>
          <option value="Himbauan">Himbauan & Ketertiban</option>
          <option value="Penting">Pengumuman Sangat Penting</option>
          <option value="Info">Informasi Umum</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Isi Lengkap Pengumuman</label>
        <textarea name="isi" class="form-control" rows="4" placeholder="Tuliskan isi pengumuman secara rinci, tanggal, jam, syarat yang harus dibawa warga..." required></textarea>
      </div>

      <div class="mb-4 flex items-center gap-2">
        <input type="checkbox" name="is_pinned" id="is_pinned" value="1" class="w-4 h-4 text-purple-600 rounded">
        <label for="is_pinned" class="text-xs font-bold text-gray-700 cursor-pointer">Sematkan di Paling Atas (Pinned Banner)</label>
      </div>

      <button type="submit" class="btn-primary w-full" style="background: linear-gradient(135deg, #7E3AF2 0%, #6366F1 100%);">
        <i class="fa-solid fa-paper-plane"></i> Publikasikan Pengumuman
      </button>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Daftar Pengumuman -->
<div class="feed-section">
  <div class="section-title">
    <span>Daftar Warta Desa</span>
    <span class="text-xs text-purple-700 font-bold"><?= count($pengumumanList) ?> Info</span>
  </div>

  <?php if (empty($pengumumanList)): ?>
    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100">
      <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-400 flex items-center justify-center mx-auto text-xl mb-2">
        <i class="fa-regular fa-newspaper"></i>
      </div>
      <p class="text-xs text-gray-500 font-semibold">Belum ada pengumuman yang disiarkan.</p>
    </div>
  <?php else: ?>
    <?php foreach ($pengumumanList as $p): ?>
      <div class="card-item mb-3">
        <div class="flex justify-between items-start mb-2">
          <div class="flex items-center gap-1.5">
            <?php if ($p['is_pinned']): ?>
              <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-extrabold flex items-center gap-1">
                <i class="fa-solid fa-thumbtack"></i> Pinned
              </span>
            <?php endif; ?>
            <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-[10px] font-bold">
              <?= htmlspecialchars($p['kategori']) ?>
            </span>
          </div>
          <span class="text-[11px] text-gray-400"><i class="fa-regular fa-clock mr-1"></i><?= date('d M Y', strtotime($p['created_at'])) ?></span>
        </div>

        <h3 class="text-sm font-extrabold text-gray-900 mb-2"><?= htmlspecialchars($p['judul']) ?></h3>
        <p class="text-xs text-gray-600 leading-relaxed bg-gray-50 p-3 rounded-xl mb-2">
          <?= nl2br(htmlspecialchars($p['isi'])) ?>
        </p>

        <div class="flex items-center justify-between text-[11px] text-gray-400 pt-2 border-t border-gray-100">
          <span>Oleh: <strong><?= htmlspecialchars($p['nama_penulis'] ?? 'Pemerintah Desa') ?></strong></span>
          <?php if (hasRole(['admin'])): ?>
            <button type="button" onclick="confirmDelete('actions/handler.php?action=delete_item&type=pengumuman&id=<?= $p['id'] ?>&redirect=pengumuman.php', '<?= htmlspecialchars(addslashes($p['judul'])) ?>')" class="text-red-500 hover:text-red-700 font-bold">
              <i class="fa-solid fa-trash mr-1"></i>Hapus
            </button>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
function toggleFormPengumuman() {
  const el = document.getElementById('formPengumumanSection');
  el.classList.toggle('hidden');
  if (!el.classList.contains('hidden')) {
    el.scrollIntoView({ behavior: 'smooth' });
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
