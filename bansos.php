<?php
// bansos.php - GoBansos: Layanan Informasi & Cek Bantuan Sosial Desa Cibuntu
$pageTitle = 'GoBansos - Cek Bantuan Sosial Desa Cibuntu, Kec. Cibitung';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Header Top Banner for GoBansos -->
<div class="px-4 pt-4 pb-2">
  <div class="bg-gradient-to-r from-amber-500 to-yellow-500 rounded-2xl p-4 text-white shadow-lg flex items-center justify-between">
    <div>
      <span class="text-[10px] font-extrabold uppercase bg-white/25 px-2 py-0.5 rounded-full">GoBansos Peduli</span>
      <h2 class="text-lg font-extrabold mt-1">Cek Bantuan Sosial</h2>
      <p class="text-xs text-amber-100 mt-0.5">Transparansi penyaluran BLT, Beras Pangan, PKH & BPNT.</p>
    </div>
    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl flex-shrink-0">
      <i class="fa-solid fa-hand-holding-heart"></i>
    </div>
  </div>
</div>

<!-- Cek NIK Box -->
<div class="px-4 my-3">
  <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
    <h3 class="text-xs font-extrabold text-gray-800 mb-2 flex items-center gap-1.5">
      <i class="fa-solid fa-id-card text-amber-500"></i> Cek Status Penerima Bantuan (KPM)
    </h3>
    <div class="flex gap-2">
      <input type="number" id="inputNikBansos" placeholder="Masukkan 16 Digit NIK KTP..." value="<?= htmlspecialchars($activeUser['nik'] ?? '') ?>" class="form-control text-xs flex-1">
      <button type="button" onclick="cekNikBansos()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-sm">
        Cek NIK
      </button>
    </div>
  </div>
</div>

<!-- Daftar Program Bantuan Desa -->
<div class="feed-section">
  <div class="section-title">
    <span>Program Bantuan Aktif</span>
  </div>

  <!-- Card 1: Cadangan Pangan Beras -->
  <div class="card-item mb-3">
    <div class="flex items-center justify-between mb-2">
      <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold">Bantuan Pangan</span>
      <span class="badge-status status-diproses">Tahap II Sedang Berjalan</span>
    </div>
    <h4 class="text-sm font-extrabold text-gray-900">Beras Cadangan Pangan Pemerintah (10 Kg)</h4>
    <p class="text-xs text-gray-600 mt-1">Penyaluran beras kualitas medium gratis untuk 450 KPM Desa Cibuntu.</p>
    <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between text-xs text-gray-500">
      <span><i class="fa-solid fa-calendar mr-1 text-amber-500"></i>Tgl 25 - 28 Bulan Ini</span>
      <span><i class="fa-solid fa-location-dot mr-1 text-red-500"></i>Aula Balai Desa</span>
    </div>
  </div>

  <!-- Card 2: BLT Dana Desa -->
  <div class="card-item mb-3">
    <div class="flex items-center justify-between mb-2">
      <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">Dana Desa</span>
      <span class="badge-status status-selesai">Rp 300.000 / Bulan</span>
    </div>
    <h4 class="text-sm font-extrabold text-gray-900">Bantuan Langsung Tunai (BLT-DD) 2026</h4>
    <p class="text-xs text-gray-600 mt-1">Diberikan untuk warga lansia tunggal, penyandang disabilitas dan keluarga rentan ekonomi.</p>
    <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between text-xs text-gray-500">
      <span><i class="fa-solid fa-user-check mr-1 text-emerald-500"></i>Hasil Musdessus BPD</span>
      <span><i class="fa-solid fa-wallet mr-1 text-emerald-500"></i>Tunai di Kantor Desa</span>
    </div>
  </div>

  <!-- Card 3: PKH Kemensos -->
  <div class="card-item mb-3">
    <div class="flex items-center justify-between mb-2">
      <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-extrabold">Kemensos RI</span>
      <span class="badge-status status-diproses">Triwulan III</span>
    </div>
    <h4 class="text-sm font-extrabold text-gray-900">Program Keluarga Harapan (PKH) & BPNT</h4>
    <p class="text-xs text-gray-600 mt-1">Bantuan pendidikan anak sekolah dan pemenuhan gizi balita melalui rekening KKS Bank Himbara.</p>
  </div>
</div>

<script>
function cekNikBansos() {
  const nik = document.getElementById('inputNikBansos').value.trim();
  if (!nik || nik.length < 10) {
    showAlertModal({
      title: 'NIK Tidak Lengkap',
      message: 'Silakan masukkan minimal 16 digit NIK KTP Anda untuk mengecek status bansos.',
      type: 'danger',
      icon: 'fa-triangle-exclamation'
    });
    return;
  }

  showAlertModal({
    title: 'Hasil Pengecekan KPM',
    message: `NIK <strong>${nik}</strong> terdaftar sebagai <strong>Penerima Manfaat Bantuan Cadangan Pangan Beras 10 Kg</strong> Tahap II Desa Cibuntu. Silakan hadir di Aula Balai Desa membawa KTP & KK asli.`,
    type: 'success',
    icon: 'fa-circle-check',
    buttonText: 'Siap, Mengerti'
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
