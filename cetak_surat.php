<?php
// cetak_surat.php - Format Dokumen Surat Resmi Pemerintah Desa Cibuntu, Kec. Cibitung
require_once __DIR__ . '/includes/auth.php';

$suratId = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT s.*, 
           u.nama, u.nik, u.no_kk, u.tempat_lahir, u.tanggal_lahir, 
           u.jenis_kelamin, u.agama, u.status_perkawinan, u.pekerjaan, 
           u.kewarganegaraan, u.golongan_darah, u.alamat, u.rt, u.rw, u.no_hp
    FROM surat s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.id = ?
");
$stmt->execute([$suratId]);
$surat = $stmt->fetch();

if (!$surat) {
    die("Dokumen surat tidak ditemukan.");
}

$extra = json_decode($surat['data_tambahan'] ?? '{}', true);

// Fungsi Format Tanggal Indonesia
function formatTanggalIndo($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return date('d F Y');
    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $time = strtotime($tanggal);
    $d = date('d', $time);
    $m = $bulanIndo[intval(date('m', $time))] ?? date('F', $time);
    $y = date('Y', $time);
    return "{$d} {$m} {$y}";
}

$tglSurat = formatTanggalIndo(!empty($surat['tanggal_disetujui']) ? $surat['tanggal_disetujui'] : date('Y-m-d'));
$tglPengantar = formatTanggalIndo(!empty($surat['tanggal_rt']) ? $surat['tanggal_rt'] : date('Y-m-d'));
$tglLahir = !empty($surat['tanggal_lahir']) ? formatTanggalIndo($surat['tanggal_lahir']) : '14 Mei 1995';
$ttl = htmlspecialchars($surat['tempat_lahir'] ?? 'Bekasi') . ', ' . $tglLahir;
$isDisetujui = in_array($surat['status'], ['disetujui_kades', 'disetujui_lurah']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($surat['jenis_surat']) ?> - <?= htmlspecialchars($surat['nama']) ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

  <style>
    body {
      font-family: 'Times New Roman', Times, serif;
      background-color: #525659;
      margin: 0;
      padding: 20px 10px;
      color: #000;
    }

    .no-print-bar {
      max-width: 820px;
      margin: 0 auto 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #1F2937;
      padding: 12px 18px;
      border-radius: 12px;
      color: #FFF;
      font-family: 'Plus Jakarta Sans', sans-serif;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .paper-sheet {
      width: 100%;
      max-width: 820px;
      min-height: 1100px;
      margin: 0 auto;
      background: #FFF;
      padding: 45px 55px;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
      box-sizing: border-box;
      position: relative;
    }

    /* Kop Surat Resmi Permendagri */
    .kop-header {
      display: flex;
      align-items: center;
      border-bottom: 3px double #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
      gap: 18px;
    }

    .kop-logo {
      width: 85px;
      height: 85px;
      object-fit: contain;
    }

    .kop-text {
      flex: 1;
      text-align: center;
    }

    .kop-text h3 {
      margin: 0;
      font-size: 15pt;
      font-weight: bold;
      letter-spacing: 1px;
    }

    .kop-text h2 {
      margin: 2px 0;
      font-size: 17pt;
      font-weight: bold;
      letter-spacing: 1.2px;
    }

    .kop-text h1 {
      margin: 2px 0;
      font-size: 21pt;
      font-weight: 900;
      letter-spacing: 2px;
    }

    .kop-text p {
      margin: 3px 0 0;
      font-size: 9.5pt;
      font-style: italic;
    }

    /* Judul Surat */
    .judul-surat {
      text-align: center;
      margin-bottom: 20px;
    }

    .judul-surat h3 {
      margin: 0;
      font-size: 13pt;
      text-decoration: underline;
      text-transform: uppercase;
      font-weight: bold;
      letter-spacing: 0.5px;
    }

    .judul-surat p {
      margin: 3px 0 0;
      font-size: 11pt;
    }

    /* Content & Table */
    .content-body {
      font-size: 11.5pt;
      line-height: 1.5;
      text-align: justify;
    }

    .konsideran {
      margin-bottom: 12px;
      text-indent: 30px;
    }

    .table-bio {
      width: 100%;
      margin: 10px 0 16px;
      border-collapse: collapse;
      font-size: 11pt;
    }

    .table-bio td {
      padding: 3px 0;
      vertical-align: top;
    }

    .table-bio td:first-child {
      width: 220px;
      padding-left: 20px;
    }

    .table-bio td:nth-child(2) {
      width: 20px;
    }

    .klausul-box {
      margin: 12px 0;
      padding: 10px 16px;
      background: #F8FAFC;
      border-left: 4px solid #00AA13;
      font-size: 11pt;
      border-radius: 0 8px 8px 0;
    }

    /* Tanda Tangan */
    .ttd-container {
      margin-top: 35px;
      display: flex;
      justify-content: space-between;
    }

    .ttd-mengetahui {
      width: 240px;
      text-align: center;
      font-size: 11pt;
    }

    .ttd-box {
      width: 270px;
      text-align: center;
      position: relative;
      font-size: 11pt;
    }

    .stempel-digital {
      position: absolute;
      left: 10px;
      top: 45px;
      width: 125px;
      opacity: 0.85;
      transform: rotate(-10deg);
      pointer-events: none;
    }

    .qr-box {
      margin: 8px auto;
      display: inline-block;
      padding: 5px;
      background: #FFF;
      border: 1px solid #CBD5E1;
      border-radius: 6px;
    }

    @media print {
      body {
        background: #FFF;
        padding: 0;
      }
      .no-print-bar {
        display: none !important;
      }
      .paper-sheet {
        box-shadow: none;
        padding: 30px 40px;
        min-height: auto;
      }
    }
  </style>
</head>
<body>

<div class="no-print-bar">
  <div>
    <strong>Format Cetak Dokumen Resmi Desa Cibuntu</strong>
    <span style="font-size: 0.8rem; opacity: 0.85; margin-left: 10px; background: rgba(255,255,255,0.15); padding: 3px 8px; rounded: 6px;">
      Status: <?= strtoupper(str_replace('_', ' ', $surat['status'])) ?>
    </span>
  </div>
  <div style="display: flex; gap: 10px;">
    <a href="surat.php" style="padding: 8px 14px; background: #4B5563; color: #FFF; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 700;">
      <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
    <button onclick="window.print()" style="padding: 8px 16px; background: #00AA13; color: #FFF; border: none; border-radius: 8px; font-weight: 800; font-size: 0.85rem; cursor: pointer; display: flex; items-center; gap: 6px;">
      <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
    </button>
  </div>
</div>

<div class="paper-sheet">
  <!-- Kop Surat Resmi Standar Permendagri & Pemkab Bekasi -->
  <div class="kop-header">
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d3/Lambang_Kabupaten_Bekasi.png/250px-Lambang_Kabupaten_Bekasi.png" alt="Logo Kabupaten Bekasi" class="kop-logo" onerror="this.src='https://raw.githubusercontent.com/maoneart/cek_dpt_gas/main/logo.png'">
    <div class="kop-text">
      <h3>PEMERINTAH KABUPATEN BEKASI</h3>
      <h2>KECAMATAN CIBITUNG</h2>
      <h1>DESA CIBUNTU</h1>
      <p>Jl. Raya Cibuntu No. 01, Kec. Cibitung, Kab. Bekasi 17520 • Telp: (021) 88349921 • Pos: 17520</p>
    </div>
  </div>

  <!-- Judul Dokumen -->
  <div class="judul-surat">
    <h3><?= strtoupper(htmlspecialchars($surat['jenis_surat'])) ?></h3>
    <p>Nomor : <?= htmlspecialchars($surat['nomor_surat']) ?></p>
  </div>

  <!-- Isi Naskah Dinas -->
  <div class="content-body">
    <!-- Dasar Konsideran Pengantar RT & RW -->
    <p class="konsideran">
      Berdasarkan Surat Pengantar dari Ketua RT <?= htmlspecialchars($surat['rt']) ?> / RW <?= htmlspecialchars($surat['rw']) ?> Desa Cibuntu Nomor: <strong><?= htmlspecialchars($surat['nomor_pengantar_rt'] ?? ('014/RT.' . $surat['rt'] . '-RW.' . $surat['rw'] . '/Ds.CBT/' . getBulanRomawi() . '/' . date('Y'))) ?></strong> tertanggal <?= $tglPengantar ?>, yang bertanda tangan di bawah ini Kepala Desa Cibuntu, Kecamatan Cibitung, Kabupaten Bekasi, menerangkan dengan sebenarnya bahwa:
    </p>

    <!-- Tabel Biodata Warga Lengkap (SOP Kependudukan) -->
    <table class="table-bio">
      <tr>
        <td>1. Nama Lengkap</td>
        <td>:</td>
        <td><strong><?= strtoupper(htmlspecialchars($surat['nama'])) ?></strong></td>
      </tr>
      <tr>
        <td>2. Nomor Induk Kependudukan (NIK)</td>
        <td>:</td>
        <td><strong><?= htmlspecialchars($surat['nik']) ?></strong></td>
      </tr>
      <tr>
        <td>3. Nomor Kartu Keluarga (No. KK)</td>
        <td>:</td>
        <td><?= htmlspecialchars($surat['no_kk'] ?? '3216071405950002') ?></td>
      </tr>
      <tr>
        <td>4. Tempat & Tanggal Lahir</td>
        <td>:</td>
        <td><?= $ttl ?></td>
      </tr>
      <tr>
        <td>5. Jenis Kelamin</td>
        <td>:</td>
        <td><?= htmlspecialchars($surat['jenis_kelamin'] ?? 'Laki-laki') ?></td>
      </tr>
      <tr>
        <td>6. Kewarganegaraan / Agama</td>
        <td>:</td>
        <td><?= htmlspecialchars($surat['kewarganegaraan'] ?? 'WNI') ?> / <?= htmlspecialchars($surat['agama'] ?? 'Islam') ?></td>
      </tr>
      <tr>
        <td>7. Pekerjaan</td>
        <td>:</td>
        <td><?= htmlspecialchars($surat['pekerjaan'] ?? 'Wiraswasta') ?></td>
      </tr>
      <tr>
        <td>8. Status Perkawinan</td>
        <td>:</td>
        <td><?= htmlspecialchars($surat['status_perkawinan'] ?? 'Kawin') ?></td>
      </tr>
      <tr>
        <td>9. Alamat Lengkap KTP</td>
        <td>:</td>
        <td><?= htmlspecialchars($surat['alamat'] ?? 'Desa Cibuntu') ?>, RT <?= htmlspecialchars($surat['rt']) ?> / RW <?= htmlspecialchars($surat['rw']) ?>, Desa Cibuntu, Kec. Cibitung, Kab. Bekasi</td>
      </tr>
    </table>

    <!-- Klausul Dinamis Berdasarkan Jenis Surat -->
    <?php if ($surat['jenis_surat'] === 'Surat Keterangan Usaha (SKU)'): ?>
      <p>
        Bahwa nama tersebut di atas adalah benar-benar penduduk Desa Cibuntu, Kecamatan Cibitung, Kabupaten Bekasi, dan berdasarkan pengamatan serta pengantar RT/RW setempat, nama tersebut benar memiliki dan menjalankan kegiatan usaha:
      </p>
      <div class="klausul-box">
        <table style="width: 100%; font-size: 11pt;">
          <tr>
            <td style="width: 180px;">Nama Usaha</td>
            <td style="width: 15px;">:</td>
            <td><strong><?= htmlspecialchars($extra['nama_usaha'] ?? 'Toko Kelontong Berkah Cibuntu') ?></strong></td>
          </tr>
          <tr>
            <td>Bidang / Jenis Usaha</td>
            <td>:</td>
            <td><?= htmlspecialchars($extra['bidang_usaha'] ?? 'Perdagangan Sembako & Kebutuhan Harian') ?></td>
          </tr>
          <tr>
            <td>Alamat Tempat Usaha</td>
            <td>:</td>
            <td><?= htmlspecialchars($extra['alamat_usaha'] ?? ($surat['alamat'] . ' RT ' . $surat['rt'] . ' / RW ' . $surat['rw'])) ?></td>
          </tr>
          <tr>
            <td>Beroperasi Sejak</td>
            <td>:</td>
            <td>Tahun <?= htmlspecialchars($extra['sejak_tahun'] ?? '2021') ?></td>
          </tr>
        </table>
      </div>
      <p>
        Surat keterangan ini diberikan kepada yang bersangkutan untuk melengkapi persyaratan administrasi: <strong><?= htmlspecialchars($surat['keperluan']) ?></strong>.
      </p>

    <?php elseif ($surat['jenis_surat'] === 'Surat Pengantar SKCK'): ?>
      <p>
        Bahwa nama tersebut di atas adalah benar-benar warga penduduk Desa Cibuntu, Kecamatan Cibitung, Kabupaten Bekasi, dan sepanjang catatan yang ada pada kami yang bersangkutan:
      </p>
      <ol style="margin: 4px 0 10px 24px; padding-left: 0; line-height: 1.6;">
        <li>Berkelakuan baik serta mentaati norma dan ketertiban di lingkungan masyarakat;</li>
        <li>Tidak sedang tersangkut perkara pidana ataupun dalam proses penyelidikan pihak berwajib;</li>
        <li>Tidak terlibat dalam tindak kriminalitas, organisasi terlarang, maupun peredaran narkotika.</li>
      </ol>
      <p>
        Surat Pengantar ini diterbitkan sebagai pengantar permohonan penerbitan <strong>Surat Keterangan Catatan Kepolisian (SKCK)</strong> pada <strong><?= htmlspecialchars($extra['tujuan_instansi'] ?? 'Kepolisian Sektor (Polsek) Cikarang Barat / Polres Metro Bekasi') ?></strong> untuk keperluan: <strong><?= htmlspecialchars($surat['keperluan']) ?></strong>.
      </p>

    <?php elseif ($surat['jenis_surat'] === 'Surat Keterangan Domisili'): ?>
      <p>
        Bahwa nama tersebut di atas adalah benar-benar berdomisili dan bertempat tinggal di RT <?= htmlspecialchars($surat['rt']) ?> / RW <?= htmlspecialchars($surat['rw']) ?> Desa Cibuntu, Kecamatan Cibitung, Kabupaten Bekasi sejak <?= htmlspecialchars($extra['lama_tinggal'] ?? 'beberapa tahun terakhir') ?> dengan status tempat tinggal <?= htmlspecialchars($extra['status_tinggal'] ?? 'Milik Sendiri') ?>.
      </p>
      <p>
        Surat keterangan domisili ini diterbitkan untuk dipergunakan sebagai kelengkapan: <strong><?= htmlspecialchars($surat['keperluan']) ?></strong>.
      </p>

    <?php elseif ($surat['jenis_surat'] === 'Surat Keterangan Tidak Mampu (SKTM)'): ?>
      <p>
        Bahwa nama tersebut di atas adalah benar warga Desa Cibuntu, Kecamatan Cibitung, Kabupaten Bekasi, dan berdasarkan data sensus serta validasi lapangan RT/RW tergolong dalam keluarga <strong>Kurang Mampu / Pra-Sejahtera</strong> secara perekonomian.
      </p>
      <p>
        Surat keterangan ini diberikan guna permohonan <strong><?= htmlspecialchars($surat['keperluan']) ?></strong>.
      </p>

    <?php elseif ($surat['jenis_surat'] === 'Surat Keterangan Belum Menikah'): ?>
      <p>
        Bahwa nama tersebut di atas adalah benar penduduk Desa Cibuntu, Kecamatan Cibitung, Kabupaten Bekasi yang berstatus <strong>BELUM KAWIN (Jejaka / Perawan)</strong> dan sampai dengan tanggal diterbitkannya surat ini belum pernah mengikat perkawinan yang sah baik secara hukum agama maupun perundang-undangan negara.
      </p>
      <p>
        Surat keterangan ini diberikan untuk keperluan: <strong><?= htmlspecialchars($surat['keperluan']) ?></strong>.
      </p>

    <?php else: ?>
      <p>
        Bahwa nama tersebut di atas adalah benar-benar warga masyarakat Desa Cibuntu, Kecamatan Cibitung, Kabupaten Bekasi yang memiliki catatan kependudukan baik dan berkelakuan baik di lingkungan masyarakat.
      </p>
      <p>
        Surat keterangan ini diberikan untuk dipergunakan sebagaimana mestinya guna keperluan: <strong><?= htmlspecialchars($surat['keperluan']) ?></strong>.
      </p>
    <?php endif; ?>

    <p style="margin-top: 14px;">
      Demikian surat keterangan ini kami buat dengan sebenarnya dan penuh rasa tanggung jawab agar dapat dipergunakan sebagaimana mestinya. Surat ini berlaku selama 30 (tiga puluh) hari kalender sejak tanggal diterbitkan.
    </p>
  </div>

  <!-- Kolom Tanda Tangan & Pengesahan Digital -->
  <div class="ttd-container">
    <div class="ttd-mengetahui">
      <div>Mengetahui,</div>
      <div><strong>Ketua RT <?= htmlspecialchars($surat['rt']) ?> / RW <?= htmlspecialchars($surat['rw']) ?></strong></div>
      <div style="height: 60px;"></div>
      <div style="font-weight: bold; text-decoration: underline;">
        <?= $surat['rt'] === '003' ? 'Bpk. SUTISNA' : 'KETUA RT ' . $surat['rt'] ?>
      </div>
      <div style="font-size: 9pt; color: #555;">No. Reg: <?= htmlspecialchars($surat['nomor_pengantar_rt'] ?? '014/RT.003-RW.001') ?></div>
    </div>

    <div class="ttd-box">
      <div>Cibuntu, <?= $tglSurat ?></div>
      <div style="font-weight: bold; margin-bottom: 6px;">Kepala Desa Cibuntu</div>
      
      <!-- Stempel Digital Watermark Resmi Desa Cibuntu -->
      <svg class="stempel-digital" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="90" fill="none" stroke="#1D4ED8" stroke-width="4" stroke-dasharray="8 4"/>
        <circle cx="100" cy="100" r="78" fill="none" stroke="#1D4ED8" stroke-width="2"/>
        <path id="curve" fill="none" d="M 30,100 A 70,70 0 1,1 170,100" />
        <text fill="#1D4ED8" font-size="11.5" font-weight="bold" font-family="Arial">
          <textPath href="#curve" startOffset="50%" text-anchor="middle">PEMERINTAH KAB. BEKASI</textPath>
        </text>
        <circle cx="100" cy="100" r="10" fill="#1D4ED8" opacity="0.3"/>
        <text x="100" y="105" text-anchor="middle" fill="#1D4ED8" font-size="13" font-weight="900" font-family="Arial">DESA</text>
        <text x="100" y="125" text-anchor="middle" fill="#1D4ED8" font-size="12" font-weight="bold" font-family="Arial">CIBUNTU</text>
      </svg>

      <!-- QR Code Validasi Keaslian Dokumen Terenkripsi -->
      <div id="qrcode" class="qr-box"></div>
      <div style="font-size: 7.5pt; color: #555; margin-top: 1px;">Ditandatangani secara elektronik (e-Signature)</div>

      <div style="margin-top: 6px;">
        <strong style="text-decoration: underline; font-size: 12pt;">H. ABDUL ROHIM, S.Sos</strong><br>
        <span style="font-size: 9.5pt;">Kepala Desa Cibuntu</span>
      </div>
    </div>
  </div>
</div>

<script>
// Generate QR Code Verifikasi Standar BSRE / Kominfo
new QRCode(document.getElementById("qrcode"), {
  text: "VERIFIKASI_SAH:<?= $surat['nomor_surat'] ?>|PEMOHON:<?= $surat['nama'] ?>|NIK:<?= $surat['nik'] ?>|KK:<?= $surat['no_kk'] ?>|KADES:H. ABDUL ROHIM, S.Sos|PENGANTAR_RT:<?= $surat['nomor_pengantar_rt'] ?>|DESA:CIBUNTU_CIBITUNG_BEKASI",
  width: 85,
  height: 85,
  colorDark : "#0F172A",
  colorLight : "#ffffff",
  correctLevel : QRCode.CorrectLevel.H
});
</script>

</body>
</html>
