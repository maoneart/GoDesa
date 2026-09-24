<?php
// agenda.php - GoAgenda: Jadwal & Schedule Kegiatan Desa Cibuntu
$pageTitle = 'GoAgenda - Jadwal Kegiatan Desa Cibuntu, Kec. Cibitung';
require_once __DIR__ . '/includes/header.php';

// Fetch Upcoming & Past Agendas
$agendaUpcoming = $pdo->query("
    SELECT * FROM agenda 
    WHERE tanggal >= DATE('now') 
    ORDER BY tanggal ASC, waktu_mulai ASC
")->fetchAll();

$agendaPast = $pdo->query("
    SELECT * FROM agenda 
    WHERE tanggal < DATE('now') 
    ORDER BY tanggal DESC 
    LIMIT 5
")->fetchAll();
?>

<!-- Header Top Banner for GoAgenda -->
<div class="px-4 pt-4 pb-2">
  <div class="bg-gradient-to-r from-sky-600 to-blue-600 rounded-2xl p-4 text-white shadow-lg flex items-center justify-between">
    <div>
      <span class="text-[10px] font-extrabold uppercase bg-white/25 px-2 py-0.5 rounded-full">GoAgenda Desa</span>
      <h2 class="text-lg font-extrabold mt-1">Jadwal & Agenda Warga</h2>
      <p class="text-xs text-sky-100 mt-0.5">Pantau jadwal gotong royong, posyandu, musrenbangdes & kegiatan RT/RW.</p>
    </div>
    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl flex-shrink-0">
      <i class="fa-solid fa-calendar-days"></i>
    </div>
  </div>
</div>

<!-- Tombol Tambah Agenda (Untuk Perangkat Desa / Admin) -->
<?php if (hasRole(['admin', 'lurah', 'staff'])): ?>
<div class="px-4 my-2">
  <button type="button" onclick="toggleFormAgenda()" class="btn-primary w-full py-3" style="background: linear-gradient(135deg, #00AED6 0%, #0284C7 100%);">
    <i class="fa-solid fa-calendar-plus"></i>
    <span>Tambah Jadwal Kegiatan Desa</span>
  </button>
</div>

<!-- Form Tambah Agenda Baru -->
<div id="formAgendaSection" class="hidden px-4 mb-4">
  <div class="form-card mx-0 my-0">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
        <i class="fa-solid fa-calendar-plus text-sky-600"></i> Buat Agenda Baru
      </h3>
      <button type="button" onclick="toggleFormAgenda()" class="text-gray-400 hover:text-gray-600 text-xs">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    </div>

    <form action="actions/handler.php" method="POST">
      <input type="hidden" name="action" value="submit_agenda">
      
      <div class="form-group">
        <label class="form-label">Nama Kegiatan</label>
        <input type="text" name="nama_kegiatan" class="form-control" placeholder="Contoh: Kerja Bakti Bersih Lingkungan RW 12" required>
      </div>

      <div class="form-group">
        <label class="form-label">Kategori Kegiatan</label>
        <select name="kategori" class="form-control" required>
          <option value="Gotong Royong">Gotong Royong / Kerja Bakti</option>
          <option value="Posyandu">Posyandu & Kesehatan Warga</option>
          <option value="Musrenbangdes">Musrenbangdes / Rapat Desa</option>
          <option value="Rapat RT/RW">Pertemuan RT / RW</option>
          <option value="Keagamaan">Pengajian & Keagamaan</option>
          <option value="Olahraga">Olahraga & Senam Ceria</option>
          <option value="Lainnya">Kegiatan Lainnya</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="form-label">Tanggal Pelaksanaan</label>
          <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="form-control" required>
        </div>
        <div>
          <label class="form-label">Waktu Mulai</label>
          <input type="time" name="waktu_mulai" value="08:00" class="form-control" required>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="form-label">Waktu Selesai (Opsional)</label>
          <input type="time" name="waktu_selesai" value="11:30" class="form-control">
        </div>
        <div>
          <label class="form-label">Penanggung Jawab</label>
          <input type="text" name="penanggung_jawab" class="form-control" placeholder="Contoh: Ketua RW 12" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Lokasi / Tempat Kumpul</label>
        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Lapangan Serbaguna Balai Warga" required>
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi & Keperluan</label>
        <textarea name="deskripsi" class="form-control" placeholder="Jelaskan rincian agenda, perlengkapan yang perlu dibawa warga..." required></textarea>
      </div>

      <button type="submit" class="btn-primary w-full" style="background: linear-gradient(135deg, #00AED6 0%, #0284C7 100%);">
        <i class="fa-solid fa-check"></i> Publikasikan Jadwal
      </button>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Daftar Agenda Mendatang -->
<div class="feed-section">
  <div class="section-title">
    <span>Agenda Mendatang</span>
    <span class="text-xs text-sky-600 font-bold"><?= count($agendaUpcoming) ?> Jadwal</span>
  </div>

  <?php if (empty($agendaUpcoming)): ?>
    <div class="bg-white rounded-2xl p-8 text-center border border-gray-100">
      <div class="w-12 h-12 rounded-full bg-sky-50 text-sky-400 flex items-center justify-center mx-auto text-xl mb-2">
        <i class="fa-regular fa-calendar-check"></i>
      </div>
      <p class="text-xs text-gray-500 font-semibold">Tidak ada jadwal kegiatan mendatang saat ini.</p>
    </div>
  <?php else: ?>
    <?php foreach ($agendaUpcoming as $a): ?>
      <div class="card-item mb-3">
        <div class="flex gap-3 items-start mb-2">
          <!-- Calendar Date Block -->
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-b from-sky-500 to-blue-600 text-white flex flex-col items-center justify-center font-bold flex-shrink-0 shadow-sm">
            <span class="text-[11px] uppercase tracking-wider"><?= date('M', strtotime($a['tanggal'])) ?></span>
            <span class="text-lg leading-none mt-0.5"><?= date('d', strtotime($a['tanggal'])) ?></span>
          </div>

          <div class="flex-1">
            <div class="flex justify-between items-start gap-1">
              <h4 class="text-sm font-extrabold text-gray-900 leading-snug"><?= htmlspecialchars($a['nama_kegiatan']) ?></h4>
              <span class="badge-status bg-sky-100 text-sky-700 text-[10px] whitespace-nowrap"><?= htmlspecialchars($a['kategori']) ?></span>
            </div>
            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
              <i class="fa-regular fa-clock text-sky-500"></i>
              <span><?= htmlspecialchars($a['waktu_mulai']) ?> - <?= htmlspecialchars($a['waktu_selesai'] ?? 'Selesai') ?> WIB</span>
            </div>
            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1.5">
              <i class="fa-solid fa-location-dot text-red-500"></i>
              <span><?= htmlspecialchars($a['lokasi']) ?></span>
            </div>
          </div>
        </div>

        <p class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded-xl mb-3 leading-relaxed">
          <?= nl2br(htmlspecialchars($a['deskripsi'])) ?>
        </p>

        <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
          <div class="flex items-center gap-1.5 text-gray-500">
            <i class="fa-solid fa-users text-sky-600"></i>
            <span><strong><?= $a['peserta_count'] ?> warga</strong> konfirmasi hadir</span>
          </div>

          <div class="flex items-center gap-2">
            <a href="actions/handler.php?action=rsvp_agenda&id=<?= $a['id'] ?>" class="px-3 py-1.5 rounded-xl bg-sky-100 text-sky-700 hover:bg-sky-200 font-bold text-xs flex items-center gap-1">
              <i class="fa-solid fa-hand"></i> Saya Hadir
            </a>

            <?php if (hasRole(['admin'])): ?>
              <button type="button" onclick="confirmDelete('actions/handler.php?action=delete_item&type=agenda&id=<?= $a['id'] ?>&redirect=agenda.php', '<?= htmlspecialchars(addslashes($a['nama_kegiatan'])) ?>')" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600">
                <i class="fa-solid fa-trash"></i>
              </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- Riwayat Kegiatan Selesai -->
  <?php if (!empty($agendaPast)): ?>
  <div class="section-title mt-6">
    <span>Riwayat Kegiatan Sebelumnya</span>
  </div>
  <?php foreach ($agendaPast as $ap): ?>
    <div class="card-item mb-2 opacity-75">
      <div class="flex justify-between items-center text-xs">
        <div>
          <span class="font-bold text-gray-800"><?= htmlspecialchars($ap['nama_kegiatan']) ?></span>
          <div class="text-[11px] text-gray-500"><?= date('d M Y', strtotime($ap['tanggal'])) ?> • <?= htmlspecialchars($ap['lokasi']) ?></div>
        </div>
        <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700 text-[10px] font-bold">Selesai</span>
      </div>
    </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
function toggleFormAgenda() {
  const el = document.getElementById('formAgendaSection');
  el.classList.toggle('hidden');
  if (!el.classList.contains('hidden')) {
    el.scrollIntoView({ behavior: 'smooth' });
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
