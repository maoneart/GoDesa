# GoDesa - Aplikasi Layanan Digital Perangkat Desa (SOP Standar Kemendagri & Gojek UI Style)

Aplikasi web perangkat desa mobile-first bertema Gojek UI (Super-App) yang telah disesuaikan 100% dengan **SOP Administrasi Pemerintahan Desa Resmi (Permendagri No. 47/2016)** untuk Desa Cibuntu, Kec. Cibitung, Kab. Bekasi.

## 🚀 Akses Aplikasi
- **URL Lokal**: [http://localhost:8085/GoDesa](http://localhost:8085/GoDesa)
- **Path Direktori**: `/sdcard/www/GoDesa`
- **Database**: SQLite Standalone (`/sdcard/www/GoDesa/db/desa.sqlite`)

## 🏛️ Rantai Birokrasi Berjenjang (Alur 4 Tahap SOP Desa)
Alur permohonan surat mematuhi rantai tata naskah dinas resmi:
1. **Warga Pemohon** (`warga`): Hermawan (RT 003 / RW 001 Desa Cibuntu)
   - Mengajukan surat online mandiri dengan biodata lengkap sesuai KTP & KK.
2. **Ketua RT** (`rt`): Bpk. Sutisna (Ketua RT 003 / RW 001)
   - Memvalidasi domisili dan menerbitkan **Surat Pengantar RT** (Format: `014/RT.003-RW.001/Ds.CBT/IX/2026`).
3. **Ketua RW** (`rw`): Bpk. H. Warsito (Ketua RW 001)
   - Memeriksa pengantar RT dan memberikan pengesahan **Mengetahui RW** (Format: `008/RW.001/Ds.CBT/IX/2026`).
4. **Staff Pelayanan Desa** (`staff`): Rahmat Hidayat (Kasi Pelayanan Desa Cibuntu)
   - Verifikasi kelengkapan berkas fisik/digital di loket kantor desa.
5. **Kepala Desa** (`kades` / `lurah`): H. Abdul Rohim, S.Sos (Kepala Desa Cibuntu)
   - Penandatanganan elektronik (**e-Signature**) resmi dengan QR Code verifikasi dan stempel digital desa.
6. **Administrator** (`admin`): Admin Desa Cibuntu
   - Manajemen penuh master data kependudukan dan konfigurasi sistem.

## 📇 Standar Biodata Kependudukan (SIAK Disdukcapil)
Setiap akun warga dilengkapi identitas hukum lengkap:
- **NIK** (16 digit) & **Nomor Kartu Keluarga (KK)**
- **Tempat & Tanggal Lahir (TTL)**
- **Jenis Kelamin** (Laki-laki / Perempuan dinamis)
- **Agama**, **Status Perkawinan**, dan **Pekerjaan**
- **Kewarganegaraan** (WNI) & **Golongan Darah**
- **Wilayah RT, RW, dan Alamat Lengkap**

## 📄 Format Surat & Naskah Dinas Dinamis
Format penomoran surat resmi: `[Kode Klasifikasi] / [No. Register] / Ds.CBT / [Bulan Romawi] / [Tahun]`
1. **Surat Keterangan Usaha (SKU)** [Kode 503]: mencantumkan nama usaha, bidang usaha, alamat toko, dan lama beroperasi.
2. **Surat Pengantar SKCK** [Kode 300]: memuat klausul kelakuan baik, bebas perkara pidana, dan rekomendasi ke Polsek Cikarang Barat / Polres Metro Bekasi.
3. **Surat Keterangan Domisili** [Kode 470]: mencatat lama tinggal dan status kepemilikan tempat tinggal.
4. **Surat Keterangan Tidak Mampu (SKTM)** [Kode 401]: validasi status pra-sejahtera untuk beasiswa/KIP atau keringanan rumah sakit.
5. **Surat Keterangan Belum Menikah** [Kode 472]: pernyataan status jejaka/perawan dan belum pernah terikat perkawinan yang sah.

## 🛡️ Standar UI & Alert Modal
- **MaoneArt Glassmorphism Modal System**: 100% modal interaktif tanpa `alert()` / `confirm()` native browser.
- Semua tombol aksi simetris dalam grid 2-kolom (`grid grid-cols-2 gap-3`).
