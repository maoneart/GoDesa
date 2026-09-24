<?php
// darurat.php - GoDarurat: Layanan Tanggap Cepat & Nomor Darurat Desa Cibuntu, Kec. Cibitung
$pageTitle = 'GoDarurat - Nomor Darurat Desa Cibuntu, Kec. Cibitung';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Header Top Banner for GoDarurat -->
<div class="px-4 pt-4 pb-2">
  <div class="bg-gradient-to-r from-rose-600 to-red-700 rounded-2xl p-4 text-white shadow-lg flex items-center justify-between">
    <div>
      <span class="text-[10px] font-extrabold uppercase bg-white/25 px-2 py-0.5 rounded-full">GoDarurat 24 Jam</span>
      <h2 class="text-lg font-extrabold mt-1">Layanan Tanggap Cepat</h2>
      <p class="text-xs text-rose-100 mt-0.5">Kontak darurat ambulans, satgas desa, Babinsa & Bhabinkamtibmas Cibitung.</p>
    </div>
    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl flex-shrink-0">
      <i class="fa-solid fa-truck-medical"></i>
    </div>
  </div>
</div>

<!-- Quick SOS Emergency Button -->
<div class="px-4 my-3">
  <button type="button" onclick="triggerSosModal()" class="w-full py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-black text-sm shadow-xl flex items-center justify-center gap-2 border-2 border-red-400 animate-pulse">
    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
    <span>KIRIM SINYAL DARURAT (SOS DESA CIBUNTU)</span>
  </button>
</div>

<!-- List Emergency Contacts -->
<div class="feed-section">
  <div class="section-title">
    <span>Daftar Kontak Siaga Desa Cibuntu</span>
  </div>

  <!-- Ambulans Desa -->
  <div class="card-item mb-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl flex-shrink-0 border border-red-100">
        <i class="fa-solid fa-ambulance"></i>
      </div>
      <div>
        <h4 class="text-sm font-extrabold text-gray-900">Ambulans Siaga Desa 24 Jam</h4>
        <div class="text-xs text-gray-500">Driver: Bpk. Maman (Standby Balai Desa)</div>
        <div class="text-xs font-bold text-red-600 font-mono mt-0.5">0812-8877-6655</div>
      </div>
    </div>
    <a href="tel:081288776655" class="w-10 h-10 rounded-xl bg-red-600 hover:bg-red-700 text-white flex items-center justify-center shadow-md">
      <i class="fa-solid fa-phone"></i>
    </a>
  </div>

  <!-- Bhabinkamtibmas (Polsek Cikarang Barat / Cibitung) -->
  <div class="card-item mb-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0 border border-blue-100">
        <i class="fa-solid fa-shield-halved"></i>
      </div>
      <div>
        <h4 class="text-sm font-extrabold text-gray-900">Bhabinkamtibmas Polsek Cikarang Barat</h4>
        <div class="text-xs text-gray-500">Aiptu Suryadi (Pos Polisi Cibuntu)</div>
        <div class="text-xs font-bold text-blue-600 font-mono mt-0.5">0813-1122-3344</div>
      </div>
    </div>
    <a href="tel:081311223344" class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-md">
      <i class="fa-solid fa-phone"></i>
    </a>
  </div>

  <!-- Babinsa (TNI Koramil 05 / Cibitung) -->
  <div class="card-item mb-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl flex-shrink-0 border border-emerald-100">
        <i class="fa-solid fa-person-military-rifle"></i>
      </div>
      <div>
        <h4 class="text-sm font-extrabold text-gray-900">Babinsa Koramil 05 / Cibitung</h4>
        <div class="text-xs text-gray-500">Pelda Sugiarto (Kodim 0509/Kab. Bekasi)</div>
        <div class="text-xs font-bold text-emerald-700 font-mono mt-0.5">0852-9988-7711</div>
      </div>
    </div>
    <a href="tel:085299887711" class="w-10 h-10 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shadow-md">
      <i class="fa-solid fa-phone"></i>
    </a>
  </div>

  <!-- Puskesmas Cibuntu (UGD 24 Jam) -->
  <div class="card-item mb-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl flex-shrink-0 border border-teal-100">
        <i class="fa-solid fa-hospital"></i>
      </div>
      <div>
        <h4 class="text-sm font-extrabold text-gray-900">Puskesmas Cibuntu (UGD 24 Jam)</h4>
        <div class="text-xs text-gray-500">Jl. Raya Cibuntu No. 04, Kec. Cibitung</div>
        <div class="text-xs font-bold text-teal-600 font-mono mt-0.5">(021) 8834-9922</div>
      </div>
    </div>
    <a href="tel:02188349922" class="w-10 h-10 rounded-xl bg-teal-600 hover:bg-teal-700 text-white flex items-center justify-center shadow-md">
      <i class="fa-solid fa-phone"></i>
    </a>
  </div>

  <!-- RSUD Kabupaten Bekasi (Cibitung) -->
  <div class="card-item mb-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0 border border-indigo-100">
        <i class="fa-solid fa-house-chimney-medical"></i>
      </div>
      <div>
        <h4 class="text-sm font-extrabold text-gray-900">RSUD Kabupaten Bekasi (Cibitung)</h4>
        <div class="text-xs text-gray-500">Jl. Raya Teuku Umar No. 202, Cibitung</div>
        <div class="text-xs font-bold text-indigo-600 font-mono mt-0.5">(021) 8832-6300</div>
      </div>
    </div>
    <a href="tel:02188326300" class="w-10 h-10 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center shadow-md">
      <i class="fa-solid fa-phone"></i>
    </a>
  </div>

  <!-- Posko Damkar Sektor Cibitung -->
  <div class="card-item mb-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl flex-shrink-0 border border-orange-100">
        <i class="fa-solid fa-fire-extinguisher"></i>
      </div>
      <div>
        <h4 class="text-sm font-extrabold text-gray-900">Posko Damkar Sektor Cibitung</h4>
        <div class="text-xs text-gray-500">Penanganan Kebakaran & Evakuasi Darurat</div>
        <div class="text-xs font-bold text-orange-600 font-mono mt-0.5">113 / (021) 8835-0011</div>
      </div>
    </div>
    <a href="tel:113" class="w-10 h-10 rounded-xl bg-orange-600 hover:bg-orange-700 text-white flex items-center justify-center shadow-md">
      <i class="fa-solid fa-phone"></i>
    </a>
  </div>
</div>

<script>
function triggerSosModal() {
  showConfirmModal({
    title: 'KIRIM ALARM DARURAT?',
    message: 'Apakah Anda benar-benar sedang menghadapi kondisi darurat medis, bencana, atau bahaya keamanan yang membutuhkan bantuan Satgas Desa Cibuntu sekarang?',
    confirmText: 'Ya, Kirim Alarm',
    cancelText: 'Batal',
    isDanger: true,
    icon: 'fa-triangle-exclamation',
    onConfirm: () => {
      showAlertModal({
        title: 'Sinyal SOS Terkirim!',
        message: 'Posko Satgas Desa Cibuntu, Babinsa, dan Ambulans telah menerima koordinat darurat Anda dan sedang segera meluncur ke lokasi.',
        type: 'success',
        icon: 'fa-circle-check'
      });
    }
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
