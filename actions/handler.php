<?php
// actions/handler.php - Backend Controller for GoDesa (Desa Cibuntu, Kec. Cibitung)
require_once __DIR__ . '/../includes/auth.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Helper for file upload
function handleUpload($fileInputName) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file = $_FILES[$fileInputName];
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return null;
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid('godesa_') . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/' . $filename;
    }
    return null;
}

$romawi = getBulanRomawi();
$curYear = date('Y');

switch ($action) {
    // 1. Submit Laporan Warga (GoLapor)
    case 'submit_laporan':
        $userId = $_SESSION['user_id'] ?? 4;
        $kategori = trim($_POST['kategori'] ?? 'Lainnya');
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        $fotoPath = handleUpload('foto');

        if (empty($judul) || empty($deskripsi) || empty($lokasi)) {
            setFlash('danger', 'Mohon lengkapi judul, deskripsi, dan lokasi laporan.');
            header("Location: ../lapor.php");
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO laporan (user_id, kategori, judul, deskripsi, lokasi, foto, status) VALUES (?, ?, ?, ?, ?, ?, 'menunggu')");
        $stmt->execute([$userId, $kategori, $judul, $deskripsi, $lokasi, $fotoPath]);

        setFlash('success', 'Laporan Anda berhasil dikirim dan akan segera ditindaklanjuti oleh perangkat Desa Cibuntu!');
        header("Location: ../lapor.php");
        exit;

    // 2. Update Status Laporan (Staff/Kades/Admin/RW/RT)
    case 'update_laporan':
        if (!hasRole(['admin', 'lurah', 'kades', 'staff', 'rw', 'rt'])) {
            setFlash('danger', 'Anda tidak memiliki hak akses untuk menanggapi laporan.');
            header("Location: ../lapor.php");
            exit;
        }

        $laporanId = intval($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? 'menunggu');
        $tanggapan = trim($_POST['tanggapan'] ?? '');
        $petugasNama = $_SESSION['user_nama'] ?? 'Petugas Desa Cibuntu';

        $stmt = $pdo->prepare("UPDATE laporan SET status = ?, tanggapan = ?, petugas_nama = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$status, $tanggapan, $petugasNama, $laporanId]);

        setFlash('success', 'Status laporan #' . $laporanId . ' berhasil diperbarui.');
        header("Location: ../lapor.php");
        exit;

    // 3. Submit Permohonan Surat Resmi (GoSurat SOP Standar Kemendagri)
    case 'submit_surat':
        $userId = $_SESSION['user_id'] ?? 4;
        $jenisSurat = trim($_POST['jenis_surat'] ?? '');
        $keperluan = trim($_POST['keperluan'] ?? '');
        
        if (empty($jenisSurat) || empty($keperluan)) {
            setFlash('danger', 'Mohon pilih jenis surat dan isi keperluan pengajuan.');
            header("Location: ../surat.php");
            exit;
        }

        $prefixMap = [
            'Surat Keterangan Usaha (SKU)' => '503',
            'Surat Pengantar SKCK' => '300',
            'Surat Keterangan Domisili' => '470',
            'Surat Keterangan Tidak Mampu (SKTM)' => '401',
            'Surat Keterangan Belum Menikah' => '472',
            'Surat Keterangan Kematian' => '474.3'
        ];
        $kodeKlasifikasi = $prefixMap[$jenisSurat] ?? '470';
        $nomorUrut = sprintf("%03d", rand(10, 999));
        $nomorSurat = "{$kodeKlasifikasi}/{$nomorUrut}/Ds.CBT/{$romawi}/{$curYear}";
        $qrToken = md5($nomorSurat . time());

        // Extra JSON data dinamis sesuai jenis surat
        $dataTambahan = [];
        if (!empty($_POST['nama_usaha'])) $dataTambahan['nama_usaha'] = trim($_POST['nama_usaha']);
        if (!empty($_POST['bidang_usaha'])) $dataTambahan['bidang_usaha'] = trim($_POST['bidang_usaha']);
        if (!empty($_POST['alamat_usaha'])) $dataTambahan['alamat_usaha'] = trim($_POST['alamat_usaha']);
        if (!empty($_POST['sejak_tahun'])) $dataTambahan['sejak_tahun'] = trim($_POST['sejak_tahun']);
        if (!empty($_POST['lama_tinggal'])) $dataTambahan['lama_tinggal'] = trim($_POST['lama_tinggal']);
        if (!empty($_POST['status_tinggal'])) $dataTambahan['status_tinggal'] = trim($_POST['status_tinggal']);
        if (!empty($_POST['tujuan_instansi'])) $dataTambahan['tujuan_instansi'] = trim($_POST['tujuan_instansi']);
        if (!empty($_POST['nama_almarhum'])) $dataTambahan['nama_almarhum'] = trim($_POST['nama_almarhum']);
        if (!empty($_POST['tgl_meninggal'])) $dataTambahan['tgl_meninggal'] = trim($_POST['tgl_meninggal']);
        if (!empty($_POST['tempat_meninggal'])) $dataTambahan['tempat_meninggal'] = trim($_POST['tempat_meninggal']);
        if (!empty($_POST['keterangan_lain'])) $dataTambahan['keterangan_lain'] = trim($_POST['keterangan_lain']);

        $stmt = $pdo->prepare("INSERT INTO surat (nomor_surat, user_id, jenis_surat, keperluan, data_tambahan, status, qr_token) VALUES (?, ?, ?, ?, ?, 'diajukan', ?)");
        $stmt->execute([$nomorSurat, $userId, $jenisSurat, $keperluan, json_encode($dataTambahan), $qrToken]);

        setFlash('success', 'Permohonan dokumen berhasil diajukan! No. Registrasi: ' . $nomorSurat . '. Menunggu persetujuan pengantar Ketua RT.');
        header("Location: ../surat.php");
        exit;

    // 4. Verifikasi Pengantar oleh Ketua RT (Tahap 1)
    case 'verifikasi_rt':
        if (!hasRole(['admin', 'rt'])) {
            setFlash('danger', 'Hanya Ketua RT yang berwenang menyetujui surat pengantar RT.');
            header("Location: ../surat.php");
            exit;
        }
        $suratId = intval($_POST['id'] ?? 0);
        $rtUser = currentUser();
        $rtNum = !empty($rtUser['rt']) && $rtUser['rt'] !== '-' ? $rtUser['rt'] : '003';
        $rwNum = !empty($rtUser['rw']) ? $rtUser['rw'] : '001';

        $noUrutRt = sprintf("%03d", rand(10, 99));
        $nomorPengantarRt = "{$noUrutRt}/RT.{$rtNum}-RW.{$rwNum}/Ds.CBT/{$romawi}/{$curYear}";

        $catatanRt = trim($_POST['catatan_rt'] ?? ('Pengantar telah disahkan oleh Ketua RT ' . $rtNum . ' / RW ' . $rwNum . '. Berkas valid dan warga berdomisili aktif.'));
        $rtId = $_SESSION['user_id'] ?? 6;

        $stmt = $pdo->prepare("UPDATE surat SET status = 'diverifikasi_rt', nomor_pengantar_rt = ?, catatan_rt = ?, rt_id = ?, tanggal_rt = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$nomorPengantarRt, $catatanRt, $rtId, $suratId]);

        setFlash('success', 'Surat Pengantar RT disetujui (No: ' . $nomorPengantarRt . ')! Diteruskan ke Ketua RW untuk pengesahan kewilayahan.');
        header("Location: ../surat.php");
        exit;

    // 5. Verifikasi & Pengesahan Mengetahui oleh Ketua RW (Tahap 2)
    case 'verifikasi_rw':
        if (!hasRole(['admin', 'rw'])) {
            setFlash('danger', 'Hanya Ketua RW yang berwenang mengesahkan pengantar tingkat RW.');
            header("Location: ../surat.php");
            exit;
        }
        $suratId = intval($_POST['id'] ?? 0);
        $rwUser = currentUser();
        $rwNum = !empty($rwUser['rw']) ? $rwUser['rw'] : '001';

        $noUrutRw = sprintf("%03d", rand(10, 99));
        $nomorPengantarRw = "{$noUrutRw}/RW.{$rwNum}/Ds.CBT/{$romawi}/{$curYear}";

        $catatanRw = trim($_POST['catatan_rw'] ?? ('Mengetahui dan menyetujui pengantar RT setempat untuk diteruskan ke Kantor Desa Cibuntu.'));
        $rwId = $_SESSION['user_id'] ?? 5;

        $stmt = $pdo->prepare("UPDATE surat SET status = 'diverifikasi_rw', nomor_pengantar_rw = ?, catatan_rw = ?, rw_id = ?, tanggal_rw = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$nomorPengantarRw, $catatanRw, $rwId, $suratId]);

        setFlash('success', 'Pengesahan Ketua RW ' . $rwNum . ' berhasil (No: ' . $nomorPengantarRw . ')! Berkas siap diproses di Loket Pelayanan Desa.');
        header("Location: ../surat.php");
        exit;

    // 6. Verifikasi Berkas Administrasi oleh Staff Pelayanan Desa (Tahap 3)
    case 'verifikasi_surat':
        if (!hasRole(['admin', 'staff'])) {
            setFlash('danger', 'Hanya staff pelayanan yang dapat memverifikasi berkas kelurahan.');
            header("Location: ../surat.php");
            exit;
        }
        $suratId = intval($_POST['id'] ?? 0);
        $catatanStaff = trim($_POST['catatan'] ?? 'Berkas administrasi dan pengantar RT/RW telah lengkap diverifikasi oleh Kasi Pelayanan Desa Cibuntu.');
        $staffId = $_SESSION['user_id'] ?? 3;

        $stmt = $pdo->prepare("UPDATE surat SET status = 'diverifikasi_staff', catatan_staff = ?, catatan = ?, staff_id = ? WHERE id = ?");
        $stmt->execute([$catatanStaff, $catatanStaff, $staffId, $suratId]);

        setFlash('success', 'Berkas pelayanan desa diverifikasi! Diteruskan ke meja Kepala Desa Cibuntu untuk pengesahan & tanda tangan digital.');
        header("Location: ../surat.php");
        exit;

    // 7. Persetujuan Akhir & TTD Digital oleh Kepala Desa (Tahap 4 - Final)
    case 'approve_surat':
        if (!hasRole(['admin', 'lurah', 'kades'])) {
            setFlash('danger', 'Hanya Kepala Desa yang berwenang memberikan tanda tangan dan persetujuan.');
            header("Location: ../surat.php");
            exit;
        }
        $suratId = intval($_POST['id'] ?? 0);
        $kadesId = $_SESSION['user_id'] ?? 2;
        $catatan = trim($_POST['catatan'] ?? 'Disetujui dan ditandatangani secara elektronik (e-Signature QR) oleh Kepala Desa Cibuntu.');

        $stmt = $pdo->prepare("UPDATE surat SET status = 'disetujui_kades', catatan = ?, lurah_id = ?, kades_id = ?, tanggal_disetujui = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$catatan, $kadesId, $kadesId, $suratId]);

        setFlash('success', 'Surat resmi telah disetujui & ditandatangani secara sah oleh Kepala Desa Cibuntu! Dokumen siap dicetak.');
        header("Location: ../surat.php");
        exit;

    // 8. Tolak Permohonan Surat
    case 'reject_surat':
        if (!hasRole(['admin', 'lurah', 'kades', 'staff', 'rw', 'rt'])) {
            setFlash('danger', 'Tidak ada hak akses menolak surat.');
            header("Location: ../surat.php");
            exit;
        }
        $suratId = intval($_POST['id'] ?? 0);
        $alasan = trim($_POST['alasan'] ?? 'Berkas persyaratan belum lengkap.');

        $stmt = $pdo->prepare("UPDATE surat SET status = 'ditolak', catatan = ? WHERE id = ?");
        $stmt->execute([$alasan, $suratId]);

        setFlash('danger', 'Permohonan surat ditolak dengan catatan: ' . $alasan);
        header("Location: ../surat.php");
        exit;

    // 9. Update Biodata Kependudukan Warga (SOP Kependudukan)
    case 'update_profil':
        $userId = intval($_POST['user_id'] ?? $_SESSION['user_id'] ?? 4);
        if (!hasRole(['admin']) && $userId !== intval($_SESSION['user_id'] ?? 0)) {
            $userId = $_SESSION['user_id'];
        }

        $nama = trim($_POST['nama'] ?? '');
        $noKk = trim($_POST['no_kk'] ?? '');
        $tempatLahir = trim($_POST['tempat_lahir'] ?? 'Bekasi');
        $tanggalLahir = trim($_POST['tanggal_lahir'] ?? '1995-05-14');
        $jenisKelamin = trim($_POST['jenis_kelamin'] ?? 'Laki-laki');
        $agama = trim($_POST['agama'] ?? 'Islam');
        $statusPerkawinan = trim($_POST['status_perkawinan'] ?? 'Kawin');
        $pekerjaan = trim($_POST['pekerjaan'] ?? 'Wiraswasta');
        $golonganDarah = trim($_POST['golongan_darah'] ?? '-');
        $noHp = trim($_POST['no_hp'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $rt = trim($_POST['rt'] ?? '003');
        $rw = trim($_POST['rw'] ?? '001');

        if (empty($nama) || empty($noKk)) {
            setFlash('danger', 'Nama lengkap dan Nomor Kartu Keluarga (KK) wajib diisi.');
            header("Location: ../profil.php");
            exit;
        }

        $stmt = $pdo->prepare("UPDATE users SET 
            nama = ?, 
            no_kk = ?, 
            tempat_lahir = ?, 
            tanggal_lahir = ?, 
            jenis_kelamin = ?, 
            agama = ?, 
            status_perkawinan = ?, 
            pekerjaan = ?, 
            golongan_darah = ?, 
            no_hp = ?, 
            alamat = ?, 
            rt = ?, 
            rw = ? 
            WHERE id = ?");
        
        $stmt->execute([
            $nama, $noKk, $tempatLahir, $tanggalLahir, $jenisKelamin, 
            $agama, $statusPerkawinan, $pekerjaan, $golonganDarah, 
            $noHp, $alamat, $rt, $rw, $userId
        ]);

        if ($userId === intval($_SESSION['user_id'] ?? 0)) {
            $_SESSION['user_nama'] = $nama;
            $_SESSION['user_rt'] = $rt;
            $_SESSION['user_rw'] = $rw;
        }

        setFlash('success', 'Data kependudukan warga berhasil diperbarui sesuai standar Disdukcapil!');
        header("Location: ../profil.php");
        exit;

    // 10. Tambah Agenda Kegiatan Desa
    case 'submit_agenda':
        if (!hasRole(['admin', 'lurah', 'kades', 'staff'])) {
            setFlash('danger', 'Hanya perangkat desa yang dapat menerbitkan agenda resmi.');
            header("Location: ../agenda.php");
            exit;
        }
        $namaKegiatan = trim($_POST['nama_kegiatan'] ?? '');
        $kategori = trim($_POST['kategori'] ?? 'Gotong Royong');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? date('Y-m-d'));
        $waktuMulai = trim($_POST['waktu_mulai'] ?? '08:00');
        $waktuSelesai = trim($_POST['waktu_selesai'] ?? '11:00');
        $penanggungJawab = trim($_POST['penanggung_jawab'] ?? 'Pemerintah Desa Cibuntu');

        if (empty($namaKegiatan) || empty($lokasi)) {
            setFlash('danger', 'Nama kegiatan dan lokasi wajib diisi.');
            header("Location: ../agenda.php");
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO agenda (nama_kegiatan, deskripsi, kategori, lokasi, tanggal, waktu_mulai, waktu_selesai, penanggung_jawab, status, peserta_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'akan_datang', 0)");
        $stmt->execute([$namaKegiatan, $deskripsi, $kategori, $lokasi, $tanggal, $waktuMulai, $waktuSelesai, $penanggungJawab]);

        setFlash('success', 'Jadwal kegiatan Desa Cibuntu berhasil dipublikasikan!');
        header("Location: ../agenda.php");
        exit;

    // 11. RSVP / Ikut Agenda Warga
    case 'rsvp_agenda':
        $agendaId = intval($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE agenda SET peserta_count = peserta_count + 1 WHERE id = ?");
        $stmt->execute([$agendaId]);
        setFlash('success', 'Terima kasih telah mengonfirmasi kehadiran Anda pada kegiatan warga Desa Cibuntu!');
        header("Location: ../agenda.php");
        exit;

    // 12. Tambah Pengumuman Desa (GoWarta)
    case 'submit_pengumuman':
        if (!hasRole(['admin', 'lurah', 'kades', 'staff'])) {
            setFlash('danger', 'Hanya perangkat desa yang dapat memuat pengumuman.');
            header("Location: ../pengumuman.php");
            exit;
        }
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        $kategori = trim($_POST['kategori'] ?? 'Info');
        $isPinned = isset($_POST['is_pinned']) ? 1 : 0;
        $penulisId = $_SESSION['user_id'] ?? 1;

        if (empty($judul) || empty($isi)) {
            setFlash('danger', 'Judul dan isi pengumuman tidak boleh kosong.');
            header("Location: ../pengumuman.php");
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO pengumuman (judul, isi, kategori, penulis_id, is_pinned) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$judul, $isi, $kategori, $penulisId, $isPinned]);

        setFlash('success', 'Pengumuman resmi Desa Cibuntu berhasil disiarkan!');
        header("Location: ../pengumuman.php");
        exit;

    // 13. Delete Item Generic
    case 'delete_item':
        if (!hasRole(['admin'])) {
            setFlash('danger', 'Hanya administrator yang dapat menghapus data permanen.');
            header("Location: ../index.php");
            exit;
        }
        $table = trim($_GET['type'] ?? '');
        $id = intval($_GET['id'] ?? 0);
        $redirect = trim($_GET['redirect'] ?? 'index.php');

        $allowedTables = ['laporan', 'surat', 'agenda', 'pengumuman'];
        if (in_array($table, $allowedTables) && $id > 0) {
            $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Data berhasil dihapus dari sistem.');
        }
        header("Location: ../" . $redirect);
        exit;

    default:
        header("Location: ../index.php");
        exit;
}
