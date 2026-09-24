<?php
// profil.php - Profil Pengguna, Biodata Kependudukan SOP & Role Switcher
$pageTitle = 'Profil & Biodata Kependudukan - GoDesa';
require_once __DIR__ . '/includes/header.php';

$curUser = currentUser();
$role = currentRole();

// Statistics
$totalWarga = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'warga'")->fetchColumn();
$totalSurat = $pdo->query("SELECT COUNT(*) FROM surat")->fetchColumn();
$totalLaporan = $pdo->query("SELECT COUNT(*) FROM laporan")->fetchColumn();
$totalAgenda = $pdo->query("SELECT COUNT(*) FROM agenda")->fetchColumn();
?>

<!-- Header Top Banner for Profil -->
<div class="px-4 pt-4 pb-2">
  <div class="bg-gradient-to-r from-gray-900 to-slate-800 rounded-2xl p-4 text-white shadow-lg flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-14 h-14 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl font-bold border border-white/20 flex-shrink-0">
        <i class="fa-solid fa-id-card"></i>
      </div>
      <div>
        <h2 class="text-base font-extrabold leading-tight"><?= htmlspecialchars($curUser['nama'] ?? 'Pengguna') ?></h2>
        <div class="text-xs text-gray-300 mt-0.5"><?= htmlspecialchars($curUser['email'] ?? '-') ?></div>
        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full bg-green-500/30 text-green-300 font-extrabold text-[10px] uppercase tracking-wider">
          Peran: <?= strtoupper($role) ?> • RT <?= htmlspecialchars($curUser['rt']) ?> / RW <?= htmlspecialchars($curUser['rw']) ?>
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Informasi Kependudukan Standar Kemendagri / Disdukcapil -->
<div class="px-4 my-3">
  <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-xs font-extrabold text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-address-card text-emerald-600"></i> Biodata Kependudukan (SOP SIAK)
      </h3>
      <button type="button" onclick="toggleEditProfil()" class="text-[11px] text-emerald-600 font-bold bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
        <i class="fa-solid fa-pen-to-square"></i> Ubah Biodata
      </button>
    </div>

    <div class="space-y-2 text-xs">
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Nomor Induk Kependudukan (NIK)</span>
        <span class="font-mono font-bold text-gray-800"><?= htmlspecialchars($curUser['nik'] ?? '-') ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Nomor Kartu Keluarga (No. KK)</span>
        <span class="font-mono font-bold text-gray-800"><?= htmlspecialchars($curUser['no_kk'] ?? '3216071405950002') ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Tempat, Tanggal Lahir</span>
        <span class="font-semibold text-gray-800"><?= htmlspecialchars($curUser['tempat_lahir'] ?? 'Bekasi') ?>, <?= !empty($curUser['tanggal_lahir']) ? date('d-m-Y', strtotime($curUser['tanggal_lahir'])) : '14-05-1995' ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Jenis Kelamin</span>
        <span class="font-semibold text-gray-800"><?= htmlspecialchars($curUser['jenis_kelamin'] ?? 'Laki-laki') ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Agama / Kewarganegaraan</span>
        <span class="font-semibold text-gray-800"><?= htmlspecialchars($curUser['agama'] ?? 'Islam') ?> / <?= htmlspecialchars($curUser['kewarganegaraan'] ?? 'WNI') ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Pekerjaan</span>
        <span class="font-semibold text-gray-800"><?= htmlspecialchars($curUser['pekerjaan'] ?? 'Wiraswasta') ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Status Perkawinan</span>
        <span class="font-semibold text-gray-800"><?= htmlspecialchars($curUser['status_perkawinan'] ?? 'Kawin') ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Golongan Darah</span>
        <span class="font-bold text-emerald-700 font-mono"><?= htmlspecialchars($curUser['golongan_darah'] ?? 'O') ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Wilayah Rukun Tetangga / RW</span>
        <span class="font-bold text-gray-800">RT <?= htmlspecialchars($curUser['rt'] ?? '003') ?> / RW <?= htmlspecialchars($curUser['rw'] ?? '001') ?></span>
      </div>
      <div class="flex justify-between py-1 border-b border-gray-50">
        <span class="text-gray-500">Nomor HP / WhatsApp</span>
        <span class="font-bold text-gray-800"><?= htmlspecialchars($curUser['no_hp'] ?? '-') ?></span>
      </div>
      <div class="flex justify-between py-1">
        <span class="text-gray-500">Alamat Tempat Tinggal</span>
        <span class="font-medium text-gray-800 text-right max-w-[210px]"><?= htmlspecialchars($curUser['alamat'] ?? 'Desa Cibuntu') ?></span>
      </div>
    </div>
  </div>
</div>

<!-- Form Edit Biodata Kependudukan (Hidden by default) -->
<div id="formEditProfil" class="hidden px-4 mb-4">
  <div class="form-card mx-0 my-0">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2">
        <i class="fa-solid fa-user-pen text-emerald-600"></i> Perbarui Biodata Kependudukan
      </h3>
      <button type="button" onclick="toggleEditProfil()" class="text-gray-400 hover:text-gray-600 text-xs">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    </div>

    <form action="actions/handler.php" method="POST" class="space-y-3">
      <input type="hidden" name="action" value="update_profil">
      <input type="hidden" name="user_id" value="<?= $curUser['id'] ?>">

      <div class="form-group">
        <label class="form-label">Nama Lengkap (Sesuai KTP)</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($curUser['nama']) ?>" class="form-control" required>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div class="form-group">
          <label class="form-label">Nomor Kartu Keluarga (KK)</label>
          <input type="number" name="no_kk" value="<?= htmlspecialchars($curUser['no_kk'] ?? '3216071405950002') ?>" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Nomor HP / WhatsApp</label>
          <input type="text" name="no_hp" value="<?= htmlspecialchars($curUser['no_hp'] ?? '') ?>" class="form-control">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div class="form-group">
          <label class="form-label">Tempat Lahir</label>
          <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($curUser['tempat_lahir'] ?? 'Bekasi') ?>" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($curUser['tanggal_lahir'] ?? '1995-05-14') ?>" class="form-control" required>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div class="form-group">
          <label class="form-label">Jenis Kelamin</label>
          <select name="jenis_kelamin" class="form-control">
            <option value="Laki-laki" <?= ($curUser['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="Perempuan" <?= ($curUser['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Agama</label>
          <select name="agama" class="form-control">
            <option value="Islam" <?= ($curUser['agama'] ?? '') === 'Islam' ? 'selected' : '' ?>>Islam</option>
            <option value="Kristen" <?= ($curUser['agama'] ?? '') === 'Kristen' ? 'selected' : '' ?>>Kristen</option>
            <option value="Katolik" <?= ($curUser['agama'] ?? '') === 'Katolik' ? 'selected' : '' ?>>Katolik</option>
            <option value="Hindu" <?= ($curUser['agama'] ?? '') === 'Hindu' ? 'selected' : '' ?>>Hindu</option>
            <option value="Buddha" <?= ($curUser['agama'] ?? '') === 'Buddha' ? 'selected' : '' ?>>Buddha</option>
            <option value="Konghucu" <?= ($curUser['agama'] ?? '') === 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div class="form-group">
          <label class="form-label">Status Perkawinan</label>
          <select name="status_perkawinan" class="form-control">
            <option value="Belum Kawin" <?= ($curUser['status_perkawinan'] ?? '') === 'Belum Kawin' ? 'selected' : '' ?>>Belum Kawin</option>
            <option value="Kawin" <?= ($curUser['status_perkawinan'] ?? '') === 'Kawin' ? 'selected' : '' ?>>Kawin</option>
            <option value="Cerai Hidup" <?= ($curUser['status_perkawinan'] ?? '') === 'Cerai Hidup' ? 'selected' : '' ?>>Cerai Hidup</option>
            <option value="Cerai Mati" <?= ($curUser['status_perkawinan'] ?? '') === 'Cerai Mati' ? 'selected' : '' ?>>Cerai Mati</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Golongan Darah</label>
          <select name="golongan_darah" class="form-control">
            <option value="-" <?= ($curUser['golongan_darah'] ?? '') === '-' ? 'selected' : '' ?>>-</option>
            <option value="A" <?= ($curUser['golongan_darah'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
            <option value="B" <?= ($curUser['golongan_darah'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
            <option value="AB" <?= ($curUser['golongan_darah'] ?? '') === 'AB' ? 'selected' : '' ?>>AB</option>
            <option value="O" <?= ($curUser['golongan_darah'] ?? '') === 'O' ? 'selected' : '' ?>>O</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Pekerjaan</label>
        <input type="text" name="pekerjaan" value="<?= htmlspecialchars($curUser['pekerjaan'] ?? 'Wiraswasta') ?>" class="form-control" required>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div class="form-group">
          <label class="form-label">Wilayah RT</label>
          <input type="text" name="rt" value="<?= htmlspecialchars($curUser['rt'] ?? '003') ?>" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Wilayah RW</label>
          <input type="text" name="rw" value="<?= htmlspecialchars($curUser['rw'] ?? '001') ?>" class="form-control" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Alamat Lengkap</label>
        <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($curUser['alamat'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn-primary w-full py-3">
        <i class="fa-solid fa-save"></i> Simpan Data Kependudukan
      </button>
    </form>
  </div>
</div>

<!-- Fast Role Switcher Box (Intended for Demo & Testing) -->
<div class="px-4 my-3">
  <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-xs font-extrabold text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-users-gear text-blue-600"></i> Beralih Peran (Role Switcher)
      </h3>
      <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-full">Simulasi SOP Desa</span>
    </div>
    <p class="text-xs text-gray-500 mb-3 leading-relaxed">
      Uji alur birokrasi berjenjang dari Warga (RT 003/001), Ketua RT 003, Ketua RW 001, Staff Desa, hingga Kepala Desa:
    </p>

    <div class="grid grid-cols-2 gap-2 text-xs">
      <a href="?switch_role=warga" class="p-3 rounded-xl border <?= $role === 'warga' ? 'border-amber-500 bg-amber-50 text-amber-900 font-bold ring-2 ring-amber-400' : 'border-gray-200 hover:bg-gray-50 text-gray-700' ?> flex flex-col items-center text-center transition">
        <i class="fa-solid fa-user text-lg text-amber-600 mb-1"></i>
        <span class="font-bold">Hermawan (Warga)</span>
        <span class="text-[10px] text-gray-500">RT 003 / RW 001</span>
      </a>

      <a href="?switch_role=rt" class="p-3 rounded-xl border <?= $role === 'rt' ? 'border-teal-500 bg-teal-50 text-teal-900 font-bold ring-2 ring-teal-400' : 'border-gray-200 hover:bg-gray-50 text-gray-700' ?> flex flex-col items-center text-center transition">
        <i class="fa-solid fa-people-roof text-lg text-teal-600 mb-1"></i>
        <span class="font-bold">Bpk. Sutisna (RT)</span>
        <span class="text-[10px] text-gray-500">Ketua RT 003</span>
      </a>

      <a href="?switch_role=rw" class="p-3 rounded-xl border <?= $role === 'rw' ? 'border-indigo-500 bg-indigo-50 text-indigo-900 font-bold ring-2 ring-indigo-400' : 'border-gray-200 hover:bg-gray-50 text-gray-700' ?> flex flex-col items-center text-center transition">
        <i class="fa-solid fa-sitemap text-lg text-indigo-600 mb-1"></i>
        <span class="font-bold">Bpk. H. Warsito (RW)</span>
        <span class="text-[10px] text-gray-500">Ketua RW 001</span>
      </a>

      <a href="?switch_role=staff" class="p-3 rounded-xl border <?= $role === 'staff' ? 'border-blue-500 bg-blue-50 text-blue-900 font-bold ring-2 ring-blue-400' : 'border-gray-200 hover:bg-gray-50 text-gray-700' ?> flex flex-col items-center text-center transition">
        <i class="fa-solid fa-id-badge text-lg text-blue-600 mb-1"></i>
        <span class="font-bold">Rahmat H. (Staff)</span>
        <span class="text-[10px] text-gray-500">Kasi Pelayanan Desa</span>
      </a>

      <a href="?switch_role=kades" class="p-3 rounded-xl border <?= in_array($role, ['kades', 'lurah']) ? 'border-green-500 bg-green-50 text-green-900 font-bold ring-2 ring-green-400' : 'border-gray-200 hover:bg-gray-50 text-gray-700' ?> flex flex-col items-center text-center transition">
        <i class="fa-solid fa-user-tie text-lg text-green-600 mb-1"></i>
        <span class="font-bold">H. Abdul Rohim, S.Sos</span>
        <span class="text-[10px] text-gray-500">Kepala Desa Cibuntu</span>
      </a>

      <a href="?switch_role=admin" class="p-3 rounded-xl border <?= $role === 'admin' ? 'border-slate-800 bg-slate-100 text-slate-900 font-bold ring-2 ring-slate-700' : 'border-gray-200 hover:bg-gray-50 text-gray-700' ?> flex flex-col items-center text-center transition">
        <i class="fa-solid fa-shield-halved text-lg text-slate-700 mb-1"></i>
        <span class="font-bold">Administrator</span>
        <span class="text-[10px] text-gray-500">Akses penuh desa</span>
      </a>
    </div>
  </div>
</div>

<!-- Statistik Data Desa Cibuntu -->
<div class="px-4 my-3">
  <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
    <h3 class="text-xs font-extrabold text-gray-800 mb-3 flex items-center gap-2">
      <i class="fa-solid fa-chart-simple text-purple-600"></i> Statistik Pelayanan Desa Cibuntu
    </h3>
    <div class="grid grid-cols-2 gap-3">
      <div class="p-3 bg-purple-50 rounded-xl">
        <div class="text-[11px] text-purple-800 font-semibold">Total Surat Diterbitkan</div>
        <div class="text-xl font-extrabold text-purple-900 mt-1"><?= $totalSurat ?></div>
      </div>
      <div class="p-3 bg-red-50 rounded-xl">
        <div class="text-[11px] text-red-800 font-semibold">Aduan Warga Ditangani</div>
        <div class="text-xl font-extrabold text-red-900 mt-1"><?= $totalLaporan ?></div>
      </div>
      <div class="p-3 bg-blue-50 rounded-xl">
        <div class="text-[11px] text-blue-800 font-semibold">Agenda Warga Terjadwal</div>
        <div class="text-xl font-extrabold text-blue-900 mt-1"><?= $totalAgenda ?></div>
      </div>
      <div class="p-3 bg-green-50 rounded-xl">
        <div class="text-[11px] text-green-800 font-semibold">Warga Terdaftar Digital</div>
        <div class="text-xl font-extrabold text-green-900 mt-1"><?= $totalWarga ?> Akun</div>
      </div>
    </div>
  </div>
</div>

<!-- Hak Cipta & Info Pembuat -->
<div class="px-4 pb-4 text-center">
  <div class="text-xs text-gray-400">
    <strong>GoDesa</strong> • Powered by MaoneArt Glassmorphism System<br>
    Pemerintah Desa Cibuntu, Kec. Cibitung, Kab. Bekasi
  </div>
</div>

<script>
function toggleEditProfil() {
  const form = document.getElementById('formEditProfil');
  form.classList.toggle('hidden');
  if (!form.classList.contains('hidden')) {
    form.scrollIntoView({ behavior: 'smooth' });
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
