<?php
// includes/db.php - Database SQLite, SOP Kependudukan & Auto Migration
date_default_timezone_set('Asia/Jakarta');

$dbDir = __DIR__ . '/../db';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

$dbPath = $dbDir . '/desa.sqlite';
$isFirstRun = !file_exists($dbPath);

try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if ($isFirstRun || filesize($dbPath) == 0) {
        initDatabase($pdo);
    } else {
        autoMigrateDatabase($pdo);
    }
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Helper Angka Bulan ke Romawi Standar Naskah Dinas Kemendagri
function getBulanRomawi($bln = null) {
    if ($bln === null) {
        $bln = date('n');
    }
    $map = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    return $map[intval($bln)] ?? 'I';
}

function autoMigrateDatabase($pdo) {
    // 1. Cek & Tambah Kolom Kependudukan di Tabel users
    $userCols = array_column($pdo->query("PRAGMA table_info(users)")->fetchAll(PDO::FETCH_ASSOC), "name");
    $userColsToAdd = [
        "no_kk" => "TEXT",
        "tempat_lahir" => "TEXT DEFAULT 'Bekasi'",
        "tanggal_lahir" => "DATE DEFAULT '1995-05-14'",
        "jenis_kelamin" => "TEXT DEFAULT 'Laki-laki'",
        "agama" => "TEXT DEFAULT 'Islam'",
        "status_perkawinan" => "TEXT DEFAULT 'Kawin'",
        "pekerjaan" => "TEXT DEFAULT 'Wiraswasta'",
        "kewarganegaraan" => "TEXT DEFAULT 'WNI'",
        "golongan_darah" => "TEXT DEFAULT '-'"
    ];
    foreach ($userColsToAdd as $col => $type) {
        if (!in_array($col, $userCols)) {
            $pdo->exec("ALTER TABLE users ADD COLUMN {$col} {$type}");
        }
    }

    // 2. Cek & Tambah Kolom Rantai Pengantar di Tabel surat
    $suratCols = array_column($pdo->query("PRAGMA table_info(surat)")->fetchAll(PDO::FETCH_ASSOC), "name");
    $suratColsToAdd = [
        "nomor_pengantar_rt" => "TEXT",
        "nomor_pengantar_rw" => "TEXT",
        "rw_id" => "INTEGER",
        "catatan_rw" => "TEXT",
        "tanggal_rw" => "DATETIME",
        "catatan_staff" => "TEXT",
        "kades_id" => "INTEGER"
    ];
    foreach ($suratColsToAdd as $col => $type) {
        if (!in_array($col, $suratCols)) {
            $pdo->exec("ALTER TABLE surat ADD COLUMN {$col} {$type}");
        }
    }
}

function initDatabase($pdo) {
    // 1. Users table (Standar Biodata Kependudukan Disdukcapil/Kemendagri)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nik TEXT UNIQUE NOT NULL,
        no_kk TEXT,
        nama TEXT NOT NULL,
        tempat_lahir TEXT DEFAULT 'Bekasi',
        tanggal_lahir DATE,
        jenis_kelamin TEXT DEFAULT 'Laki-laki', -- Laki-laki / Perempuan
        agama TEXT DEFAULT 'Islam', -- Islam, Kristen, Katolik, Hindu, Buddha, Konghucu
        status_perkawinan TEXT DEFAULT 'Kawin', -- Belum Kawin, Kawin, Cerai Hidup, Cerai Mati
        pekerjaan TEXT DEFAULT 'Wiraswasta',
        kewarganegaraan TEXT DEFAULT 'WNI',
        golongan_darah TEXT DEFAULT '-',
        email TEXT,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'warga', -- admin, lurah/kades, staff, rw, rt, warga
        no_hp TEXT,
        alamat TEXT,
        rt TEXT DEFAULT '003',
        rw TEXT DEFAULT '001',
        avatar TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Laporan Warga (GoLapor)
    $pdo->exec("CREATE TABLE IF NOT EXISTS laporan (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        kategori TEXT NOT NULL,
        judul TEXT NOT NULL,
        deskripsi TEXT NOT NULL,
        lokasi TEXT NOT NULL,
        foto TEXT,
        status TEXT DEFAULT 'menunggu', -- menunggu, diproses, selesai, ditolak
        tanggapan TEXT,
        petugas_nama TEXT,
        foto_selesai TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Surat Menyurat (GoSurat Berjenjang SOP Desa)
    $pdo->exec("CREATE TABLE IF NOT EXISTS surat (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nomor_surat TEXT UNIQUE,
        nomor_pengantar_rt TEXT,
        nomor_pengantar_rw TEXT,
        user_id INTEGER NOT NULL,
        jenis_surat TEXT NOT NULL,
        keperluan TEXT NOT NULL,
        data_tambahan TEXT, -- JSON format
        status TEXT DEFAULT 'diajukan', -- diajukan, diverifikasi_rt, diverifikasi_rw, diverifikasi_staff, disetujui_kades, ditolak
        catatan TEXT,
        rt_id INTEGER,
        catatan_rt TEXT,
        tanggal_rt DATETIME,
        rw_id INTEGER,
        catatan_rw TEXT,
        tanggal_rw DATETIME,
        staff_id INTEGER,
        catatan_staff TEXT,
        lurah_id INTEGER,
        kades_id INTEGER,
        tanggal_disetujui DATETIME,
        qr_token TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Pengumuman Desa (GoWarta)
    $pdo->exec("CREATE TABLE IF NOT EXISTS pengumuman (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        judul TEXT NOT NULL,
        isi TEXT NOT NULL,
        kategori TEXT DEFAULT 'Info',
        banner TEXT,
        penulis_id INTEGER,
        is_pinned INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Agenda & Schedule Kegiatan Desa (GoAgenda)
    $pdo->exec("CREATE TABLE IF NOT EXISTS agenda (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama_kegiatan TEXT NOT NULL,
        deskripsi TEXT NOT NULL,
        kategori TEXT NOT NULL,
        lokasi TEXT NOT NULL,
        tanggal DATE NOT NULL,
        waktu_mulai TEXT NOT NULL,
        waktu_selesai TEXT,
        penanggung_jawab TEXT,
        status TEXT DEFAULT 'akan_datang', -- akan_datang, berlangsung, selesai, dibatalkan
        peserta_count INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Seeding Default Users (SOP Kependudukan Lengkap)
    $defaultUsers = [
        [
            'nik' => '3216070000000001',
            'no_kk' => '3216070000000011',
            'nama' => 'Administrator Desa Cibuntu',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1992-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'Administrator Sistem Desa',
            'kewarganegaraan' => 'WNI',
            'golongan_darah' => 'O',
            'email' => 'admin@cibuntu.desa.id',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'no_hp' => '081299887766',
            'alamat' => 'Kantor Desa Cibuntu, Kec. Cibitung',
            'rt' => '01',
            'rw' => '01'
        ],
        [
            'nik' => '3216070000000002',
            'no_kk' => '3216070000000022',
            'nama' => 'H. Abdul Rohim, S.Sos',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1968-08-17',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'Kepala Desa Cibuntu',
            'kewarganegaraan' => 'WNI',
            'golongan_darah' => 'AB',
            'email' => 'kades@cibuntu.desa.id',
            'password' => password_hash('lurah123', PASSWORD_DEFAULT),
            'role' => 'lurah',
            'no_hp' => '081388776655',
            'alamat' => 'Jl. Raya Cibuntu No. 01, Kec. Cibitung',
            'rt' => '02',
            'rw' => '01'
        ],
        [
            'nik' => '3216070000000003',
            'no_kk' => '3216070000000033',
            'nama' => 'Rahmat Hidayat (Kasi Pelayanan)',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1988-11-20',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'Perangkat Desa (Kasi Pelayanan)',
            'kewarganegaraan' => 'WNI',
            'golongan_darah' => 'O',
            'email' => 'staff@cibuntu.desa.id',
            'password' => password_hash('staff123', PASSWORD_DEFAULT),
            'role' => 'staff',
            'no_hp' => '085711223344',
            'alamat' => 'Dusun II Cibuntu, Kec. Cibitung',
            'rt' => '03',
            'rw' => '02'
        ],
        [
            'nik' => '3216071405950001',
            'no_kk' => '3216071405950002',
            'nama' => 'Hermawan (Warga)',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1995-05-14',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'Karyawan Swasta / Desainer Grafis',
            'kewarganegaraan' => 'WNI',
            'golongan_darah' => 'O',
            'email' => 'hermawan@gmail.com',
            'password' => password_hash('warga123', PASSWORD_DEFAULT),
            'role' => 'warga',
            'no_hp' => '089533377788',
            'alamat' => 'Kp. Cibuntu RT 003 / RW 001, Desa Cibuntu',
            'rt' => '003',
            'rw' => '001'
        ],
        [
            'nik' => '3216070000000005',
            'no_kk' => '3216070000000055',
            'nama' => 'Bpk. H. Warsito (Ketua RW 001)',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1962-03-10',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'Ketua RW 001 / Tokoh Masyarakat',
            'kewarganegaraan' => 'WNI',
            'golongan_darah' => 'A',
            'email' => 'rw001@cibuntu.desa.id',
            'password' => password_hash('rw123', PASSWORD_DEFAULT),
            'role' => 'rw',
            'no_hp' => '081233445566',
            'alamat' => 'Kp. Cibuntu RW 001, Desa Cibuntu',
            'rt' => '-',
            'rw' => '001'
        ],
        [
            'nik' => '3216070000000006',
            'no_kk' => '3216070000000066',
            'nama' => 'Bpk. Sutisna (Ketua RT 003 / RW 001)',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1974-06-25',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'Ketua RT 003 / Wiraswasta',
            'kewarganegaraan' => 'WNI',
            'golongan_darah' => 'B',
            'email' => 'rt003@cibuntu.desa.id',
            'password' => password_hash('rt123', PASSWORD_DEFAULT),
            'role' => 'rt',
            'no_hp' => '085677889900',
            'alamat' => 'Kp. Cibuntu RT 003 / RW 001, Desa Cibuntu',
            'rt' => '003',
            'rw' => '001'
        ],
        [
            'nik' => '3216075209970001',
            'no_kk' => '3216071405950002',
            'nama' => 'Siti Nurhaliza, S.Pd',
            'tempat_lahir' => 'Bekasi',
            'tanggal_lahir' => '1997-09-12',
            'jenis_kelamin' => 'Perempuan',
            'agama' => 'Islam',
            'status_perkawinan' => 'Kawin',
            'pekerjaan' => 'Guru Honorer',
            'kewarganegaraan' => 'WNI',
            'golongan_darah' => 'A',
            'email' => 'siti@gmail.com',
            'password' => password_hash('warga123', PASSWORD_DEFAULT),
            'role' => 'warga',
            'no_hp' => '089511223344',
            'alamat' => 'Kp. Cibuntu RT 003 / RW 001, Desa Cibuntu',
            'rt' => '003',
            'rw' => '001'
        ]
    ];

    $stmtUser = $pdo->prepare("INSERT INTO users (nik, no_kk, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pekerjaan, kewarganegaraan, golongan_darah, email, password, role, no_hp, alamat, rt, rw) 
    VALUES (:nik, :no_kk, :nama, :tempat_lahir, :tanggal_lahir, :jenis_kelamin, :agama, :status_perkawinan, :pekerjaan, :kewarganegaraan, :golongan_darah, :email, :password, :role, :no_hp, :alamat, :rt, :rw)");
    
    foreach ($defaultUsers as $u) {
        $stmtUser->execute($u);
    }

    // Seeding Pengumuman
    $pengumumanData = [
        [
            'judul' => 'Jadwal Perekaman KTP-el & Kartu Identitas Anak (KIA) di Balai Desa Cibuntu',
            'isi' => 'Pemerintah Desa Cibuntu bekerja sama dengan Disdukcapil Kabupaten Bekasi mengadakan pelayanan keliling administrasi kependudukan di Kantor Desa Cibuntu, Kec. Cibitung. Warga dimohon membawa fotokopi Kartu Keluarga dan berkas pendukung.',
            'kategori' => 'Pelayanan',
            'is_pinned' => 1
        ],
        [
            'judul' => 'Penyaluran Bantuan Cadangan Pangan Pemerintah (Beras 10 Kg)',
            'isi' => 'Diberitahukan kepada Keluarga Penerima Manfaat (KPM) Desa Cibuntu bahwa beras bantuan pangan tahap II dapat diambil di Aula Balai Desa Cibuntu mulai pukul 08.30 WIB dengan membawa Surat Undangan & KTP asli.',
            'kategori' => 'Bansos',
            'is_pinned' => 1
        ],
        [
            'judul' => 'Himbauan Pencegahan DBD & Gotong Royong Saluran Lingkungan',
            'isi' => 'Mengingat musim penghujan, seluruh Ketua RT dan RW se-Desa Cibuntu dihimbau menggerakkan warganya untuk PSN (Pemberantasan Sarang Nyamuk) 3M Plus dan menjaga kebersihan saluran air lingkungan.',
            'kategori' => 'Himbauan',
            'is_pinned' => 0
        ]
    ];
    $stmtP = $pdo->prepare("INSERT INTO pengumuman (judul, isi, kategori, penulis_id, is_pinned) VALUES (:judul, :isi, :kategori, 1, :is_pinned)");
    foreach ($pengumumanData as $p) {
        $stmtP->execute($p);
    }

    // Seeding Agenda Kegiatan
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $nextWeek = date('Y-m-d', strtotime('+4 days'));
    $sunday = date('Y-m-d', strtotime('next Sunday'));

    $agendaData = [
        [
            'nama_kegiatan' => 'Gotong Royong Bersih Saluran Air RW 001 Cibuntu',
            'deskripsi' => 'Pembersihan saluran drainase utama guna mencegah genangan air saat hujan lebat. Diharapkan partisipasi kepala keluarga per RT.',
            'kategori' => 'Gotong Royong',
            'lokasi' => 'Pos Ronda RW 001 Desa Cibuntu',
            'tanggal' => $tomorrow,
            'waktu_mulai' => '07:30',
            'waktu_selesai' => '11:00',
            'penanggung_jawab' => 'Ketua RW 001 & Satgas Kebersihan Desa',
            'status' => 'akan_datang',
            'peserta_count' => 42
        ],
        [
            'nama_kegiatan' => 'Posyandu Melati Cibuntu (Balita & Ibu Hamil)',
            'deskripsi' => 'Penimbangan berat badan balita, imunisasi dasar lengkap, pembagian vitamin A, dan PMT gizi seimbang.',
            'kategori' => 'Posyandu',
            'lokasi' => 'Balai Warga RT 003 / RW 001 Cibuntu',
            'tanggal' => $nextWeek,
            'waktu_mulai' => '08:30',
            'waktu_selesai' => '12:00',
            'penanggung_jawab' => 'Kader Posyandu & Bidan Desa Cibuntu',
            'status' => 'akan_datang',
            'peserta_count' => 58
        ],
        [
            'nama_kegiatan' => 'Musrenbangdes Penetapan RKPDes Cibuntu 2026',
            'deskripsi' => 'Musyawarah Rencana Pembangunan Desa bersama BPD, LPM, Ketua RT/RW, dan Tokoh Masyarakat Kecamatan Cibitung.',
            'kategori' => 'Musrenbangdes',
            'lokasi' => 'Aula Pertemuan Kantor Desa Cibuntu',
            'tanggal' => $sunday,
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '14:00',
            'penanggung_jawab' => 'Kepala Desa & BPD Cibuntu',
            'status' => 'akan_datang',
            'peserta_count' => 80
        ]
    ];
    $stmtA = $pdo->prepare("INSERT INTO agenda (nama_kegiatan, deskripsi, kategori, lokasi, tanggal, waktu_mulai, waktu_selesai, penanggung_jawab, status, peserta_count) VALUES (:nama_kegiatan, :deskripsi, :kategori, :lokasi, :tanggal, :waktu_mulai, :waktu_selesai, :penanggung_jawab, :status, :peserta_count)");
    foreach ($agendaData as $a) {
        $stmtA->execute($a);
    }

    // Seeding Laporan
    $laporanData = [
        [
            'user_id' => 4,
            'kategori' => 'Infrastruktur',
            'judul' => 'Lampu PJU Padam di Pertigaan Jalan Desa RT 003',
            'deskripsi' => 'Lampu penerangan jalan umum sudah padam, jalanan gelap rawan tindak kejahatan dan kecelakaan di malam hari.',
            'lokasi' => 'Kp. Cibuntu Blok C RT 003/001',
            'status' => 'diproses',
            'tanggapan' => 'Laporan sudah diteruskan ke tim teknis PJU desa. Teknisi dijadwalkan ganti bohlam LED sore ini.',
            'petugas_nama' => 'Rahmat Hidayat (Kasi Pelayanan)'
        ],
        [
            'user_id' => 4,
            'kategori' => 'Kebersihan',
            'judul' => 'Sampah Liar Menumpuk di Pinggir Saluran Irigasi',
            'deskripsi' => 'Ada pembuang sampah liar di pinggir tanggul irigasi perbatasan RT.',
            'lokasi' => 'Bantaran Saluran Irigasi RW 001',
            'status' => 'menunggu',
            'tanggapan' => null,
            'petugas_nama' => null
        ],
        [
            'user_id' => 4,
            'kategori' => 'Pelayanan',
            'judul' => 'Pengaspalan Lubang Jalan Menuju Balai Desa',
            'deskripsi' => 'Lubang jalan sedalam 10 cm membahayakan pengendara motor saat hujan.',
            'lokasi' => 'Jl. Utama Kp. Cibuntu RT 003',
            'status' => 'selesai',
            'tanggapan' => 'Alhamdulillah telah selesai ditambal hotmix darurat oleh satgas perbaikan jalan desa.',
            'petugas_nama' => 'Bpk. Kades & Tim Tanggap Cepat'
        ]
    ];
    $stmtL = $pdo->prepare("INSERT INTO laporan (user_id, kategori, judul, deskripsi, lokasi, status, tanggapan, petugas_nama) VALUES (:user_id, :kategori, :judul, :deskripsi, :lokasi, :status, :tanggapan, :petugas_nama)");
    foreach ($laporanData as $l) {
        $stmtL->execute($l);
    }

    // Seeding Surat Berjenjang SOP Standar Kemendagri
    $curYear = date('Y');
    $romawi = getBulanRomawi();
    $suratData = [
        [
            'nomor_surat' => "503/042/Ds.CBT/{$romawi}/{$curYear}",
            'nomor_pengantar_rt' => "014/RT.003-RW.001/Ds.CBT/{$romawi}/{$curYear}",
            'nomor_pengantar_rw' => "008/RW.001/Ds.CBT/{$romawi}/{$curYear}",
            'user_id' => 4,
            'jenis_surat' => 'Surat Keterangan Usaha (SKU)',
            'keperluan' => 'Pengajuan Tambahan Modal Usaha KUR Bank BRI',
            'data_tambahan' => json_encode(['nama_usaha' => 'Toko Kelontong Berkah Cibuntu', 'bidang_usaha' => 'Perdagangan Sembako & Kelontong', 'alamat_usaha' => 'Kp. Cibuntu No. 12 RT 003/001', 'sejak_tahun' => '2021']),
            'status' => 'disetujui_kades',
            'catatan' => 'Berkas valid dan tempat usaha terverifikasi oleh Ketua RT setempat.',
            'rt_id' => 6,
            'catatan_rt' => 'Telah diverifikasi oleh Ketua RT 003. Usaha kelontong benar berlokasi di RT 003.',
            'tanggal_rt' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'rw_id' => 5,
            'catatan_rw' => 'Mengetahui dan menyetujui pengantar Ketua RT 003 untuk diproses di Kantor Desa Cibuntu.',
            'tanggal_rw' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'staff_id' => 3,
            'catatan_staff' => 'Berkas lengkap dan sesuai SOP pelayanan umum Desa Cibuntu.',
            'lurah_id' => 2,
            'kades_id' => 2,
            'tanggal_disetujui' => date('Y-m-d H:i:s'),
            'qr_token' => md5('SKU-3216071405950001-' . date('Ymd'))
        ],
        [
            'nomor_surat' => "300/018/Ds.CBT/{$romawi}/{$curYear}",
            'nomor_pengantar_rt' => "015/RT.003-RW.001/Ds.CBT/{$romawi}/{$curYear}",
            'nomor_pengantar_rw' => "009/RW.001/Ds.CBT/{$romawi}/{$curYear}",
            'user_id' => 4,
            'jenis_surat' => 'Surat Pengantar SKCK',
            'keperluan' => 'Pendaftaran Rekrutmen Kerja Kawasan MM2100 Cibitung',
            'data_tambahan' => json_encode(['tujuan_instansi' => 'Polsek Cikarang Barat / Polres Metro Bekasi']),
            'status' => 'diverifikasi_staff',
            'catatan' => 'Diverifikasi staff pelayanan, menunggu paraf dan tanda tangan Kepala Desa.',
            'rt_id' => 6,
            'catatan_rt' => 'Pengantar RT disetujui untuk permohonan SKCK.',
            'tanggal_rt' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'rw_id' => 5,
            'catatan_rw' => 'Mengetahui Ketua RW 001, pemohon berkelakuan baik di lingkungan masyarakat.',
            'tanggal_rw' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'staff_id' => 3,
            'catatan_staff' => 'Berkas diteruskan ke meja Kepala Desa Cibuntu.',
            'lurah_id' => null,
            'kades_id' => null,
            'tanggal_disetujui' => null,
            'qr_token' => md5('SKCK-3216071405950001-' . date('Ymd'))
        ]
    ];

    $stmtS = $pdo->prepare("INSERT INTO surat (nomor_surat, nomor_pengantar_rt, nomor_pengantar_rw, user_id, jenis_surat, keperluan, data_tambahan, status, catatan, rt_id, catatan_rt, tanggal_rt, rw_id, catatan_rw, tanggal_rw, staff_id, catatan_staff, lurah_id, kades_id, tanggal_disetujui, qr_token) 
    VALUES (:nomor_surat, :nomor_pengantar_rt, :nomor_pengantar_rw, :user_id, :jenis_surat, :keperluan, :data_tambahan, :status, :catatan, :rt_id, :catatan_rt, :tanggal_rt, :rw_id, :catatan_rw, :tanggal_rw, :staff_id, :catatan_staff, :lurah_id, :kades_id, :tanggal_disetujui, :qr_token)");
    foreach ($suratData as $s) {
        $stmtS->execute($s);
    }
}
