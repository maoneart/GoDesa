<?php
// generate_full_dummy.php - Comprehensive Village Data Generator for Desa Cibuntu
set_time_limit(300);
ini_set('memory_limit', '256M');

$dbSqlitePath = __DIR__ . '/db/desa.sqlite';
$sqlDumpPath = __DIR__ . '/database.sql';

$namaDepanL = ["Ahmad", "Muhammad", "Budi", "Bambang", "Hermawan", "Dedi", "Agus", "Rizky", "Fajar", "Hendra", "Asep", "Joko", "Wahyu", "Eko", "Indra", "Tri", "Bayu", "Arif", "Hadi", "Surya", "Ilham", "Danang", "Sugeng", "Supriadi", "Teguh", "Lukman", "Rahmat", "Ginanjar", "Faisal", "Hasan", "Ridwan", "Sutisna", "Warsito", "Jaelani", "Maman", "Saepul", "Solihin", "Iskandar", "Yusuf", "Zainal"];
$namaDepanP = ["Siti", "Nur", "Dewi", "Sri", "Rina", "Fitri", "Lestari", "Ratna", "Wulandari", "Yuni", "Kartika", "Mega", "Indah", "Putri", "Annisa", "Dian", "Triana", "Rahayu", "Haryanti", "Susilowati", "Halimah", "Nurhasanah", "Aminah", "Salmah", "Khadijah", "Rohayati", "Fatimah", "Nuryani", "Sulastri"];
$namaBelakang = ["Pratama", "Hidayat", "Kusuma", "Santoso", "Saputra", "Wijaya", "Kurniawan", "Setiawan", "Utomo", "Nugroho", "Gunawan", "Susanto", "Wibowo", "Permana", "Subekti", "Siregar", "Maulana", "Firmansyah", "Ramadhan", "Anwar", "Hakim", "Zulkarnain", "Nasution", "Hasanah", "Suharto", "Sugiarto", "Herlambang"];
$pekerjaanList = ["Karyawan Swasta", "Wiraswasta", "Buruh Harian Lepas", "Pedagang Kelontong", "Guru Honorer", "PNS / ASN", "Mekanik Bengkel", "Supir Ekspedisi", "Ibu Rumah Tangga", "Pelajar/Mahasiswa", "Buruh Pabrik MM2100", "Petani / Pekebun", "Security Kawasan"];
$golDarah = ["A", "B", "AB", "O", "-"];

$defaultPass = password_hash("warga123", PASSWORD_DEFAULT);

$users = [];

// 1. Aparat Desa Inti
$users[] = [
    "nik" => "3216070000000001",
    "no_kk" => "3216070000000011",
    "nama" => "Administrator Desa Cibuntu",
    "tempat_lahir" => "Bekasi",
    "tanggal_lahir" => "1992-01-01",
    "jenis_kelamin" => "Laki-laki",
    "agama" => "Islam",
    "status_perkawinan" => "Kawin",
    "pekerjaan" => "Administrator Sistem Desa",
    "kewarganegaraan" => "WNI",
    "golongan_darah" => "O",
    "email" => "admin@cibuntu.desa.id",
    "password" => password_hash("admin123", PASSWORD_DEFAULT),
    "role" => "admin",
    "no_hp" => "081299887766",
    "alamat" => "Kantor Desa Cibuntu, Jl. Raya Cibuntu No. 01",
    "rt" => "001",
    "rw" => "001"
];

$users[] = [
    "nik" => "3216070000000002",
    "no_kk" => "3216070000000022",
    "nama" => "H. Abdul Rohim, S.Sos",
    "tempat_lahir" => "Bekasi",
    "tanggal_lahir" => "1968-08-17",
    "jenis_kelamin" => "Laki-laki",
    "agama" => "Islam",
    "status_perkawinan" => "Kawin",
    "pekerjaan" => "Kepala Desa Cibuntu",
    "kewarganegaraan" => "WNI",
    "golongan_darah" => "AB",
    "email" => "kades@cibuntu.desa.id",
    "password" => password_hash("kades123", PASSWORD_DEFAULT),
    "role" => "lurah",
    "no_hp" => "081388776655",
    "alamat" => "Jl. Raya Cibuntu No. 01 RT 002 / RW 001",
    "rt" => "002",
    "rw" => "001"
];

$users[] = [
    "nik" => "3216070000000004",
    "no_kk" => "3216070000000044",
    "nama" => "H. Muhammad Ridwan, S.AP",
    "tempat_lahir" => "Bekasi",
    "tanggal_lahir" => "1975-04-12",
    "jenis_kelamin" => "Laki-laki",
    "agama" => "Islam",
    "status_perkawinan" => "Kawin",
    "pekerjaan" => "Sekretaris Desa (Sekdes)",
    "kewarganegaraan" => "WNI",
    "golongan_darah" => "B",
    "email" => "sekdes@cibuntu.desa.id",
    "password" => password_hash("sekdes123", PASSWORD_DEFAULT),
    "role" => "staff",
    "no_hp" => "081288990011",
    "alamat" => "Kp. Cibuntu Kaum RT 001 / RW 002",
    "rt" => "001",
    "rw" => "002"
];

$users[] = [
    "nik" => "3216070000000003",
    "no_kk" => "3216070000000033",
    "nama" => "Rahmat Hidayat (Kasi Pelayanan)",
    "tempat_lahir" => "Bekasi",
    "tanggal_lahir" => "1988-11-20",
    "jenis_kelamin" => "Laki-laki",
    "agama" => "Islam",
    "status_perkawinan" => "Kawin",
    "pekerjaan" => "Perangkat Desa (Kasi Pelayanan)",
    "kewarganegaraan" => "WNI",
    "golongan_darah" => "O",
    "email" => "staff@cibuntu.desa.id",
    "password" => password_hash("staff123", PASSWORD_DEFAULT),
    "role" => "staff",
    "no_hp" => "085711223344",
    "alamat" => "Dusun II Cibuntu RT 003 / RW 002",
    "rt" => "003",
    "rw" => "002"
];

// Struktur 5 RW & 12 RT
$rwConfig = [
    "001" => [
        "nama_rw" => "Bpk. H. Warsito",
        "rt_list" => [
            "001" => "Bpk. M. Kosasih",
            "002" => "Bpk. Maryanto",
            "003" => "Bpk. Sutisna"
        ]
    ],
    "002" => [
        "nama_rw" => "Bpk. H. Mulyadi",
        "rt_list" => [
            "001" => "Bpk. Dedi Supriyadi",
            "002" => "Bpk. Bambang Irawan"
        ]
    ],
    "003" => [
        "nama_rw" => "Bpk. H. Sanusi",
        "rt_list" => [
            "001" => "Bpk. Asep Saepudin",
            "002" => "Bpk. H. Rahmatullah",
            "003" => "Bpk. Suhendra"
        ]
    ],
    "004" => [
        "nama_rw" => "Bpk. H. Achmad Fauzi",
        "rt_list" => [
            "001" => "Bpk. Agus Salim",
            "002" => "Bpk. Hendro Wijaya"
        ]
    ],
    "005" => [
        "nama_rw" => "Bpk. H. Zainuddin",
        "rt_list" => [
            "001" => "Bpk. Joko Prasetyo",
            "002" => "Bpk. Wawan Setiawan"
        ]
    ]
];

$nikSeq = 1000;

foreach ($rwConfig as $rwNum => $conf) {
    // Akun Ketua RW
    $users[] = [
        "nik" => "321607" . sprintf("%02d", intval($rwNum)) . "000000005",
        "no_kk" => "321607" . sprintf("%02d", intval($rwNum)) . "000000055",
        "nama" => $conf["nama_rw"],
        "tempat_lahir" => "Bekasi",
        "tanggal_lahir" => "196" . rand(0, 5) . "-0" . rand(1, 9) . "-12",
        "jenis_kelamin" => "Laki-laki",
        "agama" => "Islam",
        "status_perkawinan" => "Kawin",
        "pekerjaan" => "Ketua RW {$rwNum} / Tokoh Masyarakat",
        "kewarganegaraan" => "WNI",
        "golongan_darah" => "A",
        "email" => "rw{$rwNum}@cibuntu.desa.id",
        "password" => password_hash("rw123", PASSWORD_DEFAULT),
        "role" => "rw",
        "no_hp" => "0812" . rand(10000000, 99999999),
        "alamat" => "Kp. Cibuntu RW {$rwNum}, Desa Cibuntu",
        "rt" => "-",
        "rw" => $rwNum
    ];

    foreach ($conf["rt_list"] as $rtNum => $namaRt) {
        // Akun Ketua RT
        $users[] = [
            "nik" => "321607" . sprintf("%02d", intval($rwNum)) . sprintf("%02d", intval($rtNum)) . "000006",
            "no_kk" => "321607" . sprintf("%02d", intval($rwNum)) . sprintf("%02d", intval($rtNum)) . "000066",
            "nama" => $namaRt,
            "tempat_lahir" => "Bekasi",
            "tanggal_lahir" => "197" . rand(0, 8) . "-0" . rand(1, 9) . "-18",
            "jenis_kelamin" => "Laki-laki",
            "agama" => "Islam",
            "status_perkawinan" => "Kawin",
            "pekerjaan" => "Ketua RT {$rtNum} / Wiraswasta",
            "kewarganegaraan" => "WNI",
            "golongan_darah" => "B",
            "email" => "rt{$rtNum}.rw{$rwNum}@cibuntu.desa.id",
            "password" => password_hash("rt123", PASSWORD_DEFAULT),
            "role" => "rt",
            "no_hp" => "0856" . rand(10000000, 99999999),
            "alamat" => "Kp. Cibuntu RT {$rtNum} / RW {$rwNum}, Desa Cibuntu",
            "rt" => $rtNum,
            "rw" => $rwNum
        ];

        // 10 KK per RT dengan variasi realistis
        for ($kkIndex = 1; $kkIndex <= 10; $kkIndex++) {
            $noKk = "321607" . sprintf("%02d", intval($rwNum)) . sprintf("%02d", intval($rtNum)) . sprintf("%04d", $kkIndex);

            // Special Case: RT 003 / RW 001 KK 1 adalah Hermawan & Istri
            if ($rwNum === "001" && $rtNum === "003" && $kkIndex === 1) {
                $users[] = [
                    "nik" => "3216071405950001",
                    "no_kk" => $noKk,
                    "nama" => "Hermawan (Warga)",
                    "tempat_lahir" => "Bekasi",
                    "tanggal_lahir" => "1995-05-14",
                    "jenis_kelamin" => "Laki-laki",
                    "agama" => "Islam",
                    "status_perkawinan" => "Kawin",
                    "pekerjaan" => "Karyawan Swasta / Desainer Grafis",
                    "kewarganegaraan" => "WNI",
                    "golongan_darah" => "O",
                    "email" => "hermawan@gmail.com",
                    "password" => password_hash("warga123", PASSWORD_DEFAULT),
                    "role" => "warga",
                    "no_hp" => "089533377788",
                    "alamat" => "Kp. Cibuntu RT 003 / RW 001, Desa Cibuntu",
                    "rt" => "003",
                    "rw" => "001"
                ];
                $users[] = [
                    "nik" => "3216075209970001",
                    "no_kk" => $noKk,
                    "nama" => "Siti Nurhaliza, S.Pd",
                    "tempat_lahir" => "Bekasi",
                    "tanggal_lahir" => "1997-09-12",
                    "jenis_kelamin" => "Perempuan",
                    "agama" => "Islam",
                    "status_perkawinan" => "Kawin",
                    "pekerjaan" => "Guru Honorer",
                    "kewarganegaraan" => "WNI",
                    "golongan_darah" => "A",
                    "email" => "siti.nurhaliza@gmail.com",
                    "password" => $defaultPass,
                    "role" => "warga",
                    "no_hp" => "089511223344",
                    "alamat" => "Kp. Cibuntu RT 003 / RW 001, Desa Cibuntu",
                    "rt" => "003",
                    "rw" => "001"
                ];
                continue;
            }

            // Variasi Tipe KK
            $tipeKK = ($kkIndex % 5);
            $namaBelakangKeluarga = $namaBelakang[array_rand($namaBelakang)];

            if ($tipeKK == 1) {
                // Tipe 1: Suami + Istri + 2 Anak (Keluarga Inti)
                $suamiNama = $namaDepanL[array_rand($namaDepanL)] . " " . $namaBelakangKeluarga;
                $istriNama = $namaDepanP[array_rand($namaDepanP)] . " " . $namaBelakang[array_rand($namaBelakang)];
                $anak1Nama = $namaDepanL[array_rand($namaDepanL)] . " " . $namaBelakangKeluarga;
                $anak2Nama = $namaDepanP[array_rand($namaDepanP)] . " " . $namaBelakangKeluarga;

                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0001",
                    "no_kk" => $noKk, "nama" => $suamiNama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "198" . rand(0, 8) . "-0" . rand(1, 9) . "-10",
                    "jenis_kelamin" => "Laki-laki", "agama" => "Islam", "status_perkawinan" => "Kawin", "pekerjaan" => $pekerjaanList[array_rand($pekerjaanList)],
                    "kewarganegaraan" => "WNI", "golongan_darah" => $golDarah[array_rand($golDarah)], "email" => strtolower(str_replace(' ', '', $suamiNama)) . "@gmail.com",
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "08" . rand(1100000000, 9900000000), "alamat" => "Kp. Cibuntu No. " . rand(1, 80) . " RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0002",
                    "no_kk" => $noKk, "nama" => $istriNama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "198" . rand(2, 9) . "-0" . rand(1, 9) . "-22",
                    "jenis_kelamin" => "Perempuan", "agama" => "Islam", "status_perkawinan" => "Kawin", "pekerjaan" => "Ibu Rumah Tangga",
                    "kewarganegaraan" => "WNI", "golongan_darah" => $golDarah[array_rand($golDarah)], "email" => null,
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "08" . rand(1100000000, 9900000000), "alamat" => "Kp. Cibuntu No. " . rand(1, 80) . " RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0003",
                    "no_kk" => $noKk, "nama" => $anak1Nama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "200" . rand(4, 7) . "-0" . rand(1, 9) . "-05",
                    "jenis_kelamin" => "Laki-laki", "agama" => "Islam", "status_perkawinan" => "Belum Kawin", "pekerjaan" => "Buruh Pabrik MM2100",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "O", "email" => null,
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "08" . rand(1100000000, 9900000000), "alamat" => "Kp. Cibuntu RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
            } elseif ($tipeKK == 2) {
                // Tipe 2: Tinggal bersama Orang Tua (Keluarga 3 Generasi)
                $kakekNama = "H. " . $namaDepanL[array_rand($namaDepanL)] . " " . $namaBelakangKeluarga;
                $nenekNama = "Hj. " . $namaDepanP[array_rand($namaDepanP)];
                $anakNama = $namaDepanL[array_rand($namaDepanL)] . " " . $namaBelakangKeluarga;
                $menantuNama = $namaDepanP[array_rand($namaDepanP)] . " " . $namaBelakang[array_rand($namaBelakang)];

                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0001",
                    "no_kk" => $noKk, "nama" => $kakekNama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "195" . rand(2, 8) . "-03-15",
                    "jenis_kelamin" => "Laki-laki", "agama" => "Islam", "status_perkawinan" => "Kawin", "pekerjaan" => "Pensiunan / Petani",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "B", "email" => null,
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "0813" . rand(10000000, 99999999), "alamat" => "Kp. Cibuntu RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0002",
                    "no_kk" => $noKk, "nama" => $nenekNama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "195" . rand(5, 9) . "-07-20",
                    "jenis_kelamin" => "Perempuan", "agama" => "Islam", "status_perkawinan" => "Kawin", "pekerjaan" => "Ibu Rumah Tangga",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "AB", "email" => null,
                    "password" => $defaultPass, "role" => "warga", "no_hp" => null, "alamat" => "Kp. Cibuntu RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0003",
                    "no_kk" => $noKk, "nama" => $anakNama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "198" . rand(5, 9) . "-11-12",
                    "jenis_kelamin" => "Laki-laki", "agama" => "Islam", "status_perkawinan" => "Kawin", "pekerjaan" => "Wiraswasta Bengkel",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "O", "email" => strtolower(str_replace(' ', '', $anakNama)) . "@gmail.com",
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "0857" . rand(10000000, 99999999), "alamat" => "Kp. Cibuntu RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0004",
                    "no_kk" => $noKk, "nama" => $menantuNama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "1990-06-25",
                    "jenis_kelamin" => "Perempuan", "agama" => "Islam", "status_perkawinan" => "Kawin", "pekerjaan" => "Pedagang Warung",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "A", "email" => null,
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "0878" . rand(10000000, 99999999), "alamat" => "Kp. Cibuntu RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
            } elseif ($tipeKK == 3) {
                // Tipe 3: Tinggal Sendiri (Lajang Pekerja Kawasan Industri MM2100)
                $lajangNama = $namaDepanL[array_rand($namaDepanL)] . " " . $namaBelakangKeluarga;
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0001",
                    "no_kk" => $noKk, "nama" => $lajangNama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "199" . rand(6, 9) . "-08-14",
                    "jenis_kelamin" => "Laki-laki", "agama" => "Islam", "status_perkawinan" => "Belum Kawin", "pekerjaan" => "Karyawan Swasta MM2100",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "O", "email" => strtolower(str_replace(' ', '', $lajangNama)) . "@gmail.com",
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "0896" . rand(10000000, 99999999), "alamat" => "Kp. Cibuntu Kontrakan Blok B RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
            } elseif ($tipeKK == 4) {
                // Tipe 4: Janda / Duda Lansia Tinggal Sendiri (Penerima Bansos)
                $lansiaNama = (rand(0, 1) ? "Nenek " : "Kakek ") . $namaDepanP[array_rand($namaDepanP)] . " " . $namaBelakangKeluarga;
                $isP = strpos($lansiaNama, "Nenek") !== false;
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0001",
                    "no_kk" => $noKk, "nama" => $lansiaNama, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "195" . rand(0, 5) . "-02-19",
                    "jenis_kelamin" => $isP ? "Perempuan" : "Laki-laki", "agama" => "Islam", "status_perkawinan" => "Cerai Mati", "pekerjaan" => "Tidak Bekerja / Lansia",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "-", "email" => null,
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "0852" . rand(10000000, 99999999), "alamat" => "Kp. Cibuntu RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
            } else {
                // Tipe 5: Pasangan Baru Menikah (Suami + Istri)
                $suamiMuda = $namaDepanL[array_rand($namaDepanL)] . " " . $namaBelakangKeluarga;
                $istriMuda = $namaDepanP[array_rand($namaDepanP)] . " " . $namaBelakang[array_rand($namaBelakang)];
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0001",
                    "no_kk" => $noKk, "nama" => $suamiMuda, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "199" . rand(7, 9) . "-04-09",
                    "jenis_kelamin" => "Laki-laki", "agama" => "Islam", "status_perkawinan" => "Kawin", "pekerjaan" => "Wiraswasta / Kurir",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "B", "email" => strtolower(str_replace(' ', '', $suamiMuda)) . "@gmail.com",
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "0812" . rand(10000000, 99999999), "alamat" => "Kp. Cibuntu Perum RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
                $users[] = [
                    "nik" => "321607" . sprintf("%04d", ++$nikSeq) . "0002",
                    "no_kk" => $noKk, "nama" => $istriMuda, "tempat_lahir" => "Bekasi", "tanggal_lahir" => "2000-10-15",
                    "jenis_kelamin" => "Perempuan", "agama" => "Islam", "status_perkawinan" => "Kawin", "pekerjaan" => "Karyawan Toko",
                    "kewarganegaraan" => "WNI", "golongan_darah" => "A", "email" => null,
                    "password" => $defaultPass, "role" => "warga", "no_hp" => "0895" . rand(10000000, 99999999), "alamat" => "Kp. Cibuntu Perum RT {$rtNum} / RW {$rwNum}", "rt" => $rtNum, "rw" => $rwNum
                ];
            }
        }
    }
}

echo "Total Citizens Generated: " . count($users) . "\n";

// --- Write to SQLite ---
$pdo = new PDO("sqlite:" . $dbSqlitePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Reset & Re-create SQLite Users table
$pdo->exec("DROP TABLE IF EXISTS users");
$pdo->exec("CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nik TEXT UNIQUE NOT NULL,
    no_kk TEXT,
    nama TEXT NOT NULL,
    tempat_lahir TEXT DEFAULT 'Bekasi',
    tanggal_lahir DATE,
    jenis_kelamin TEXT DEFAULT 'Laki-laki',
    agama TEXT DEFAULT 'Islam',
    status_perkawinan TEXT DEFAULT 'Kawin',
    pekerjaan TEXT DEFAULT 'Wiraswasta',
    kewarganegaraan TEXT DEFAULT 'WNI',
    golongan_darah TEXT DEFAULT '-',
    email TEXT,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'warga',
    no_hp TEXT,
    alamat TEXT,
    rt TEXT DEFAULT '003',
    rw TEXT DEFAULT '001',
    avatar TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$stmtInsert = $pdo->prepare("INSERT INTO users (nik, no_kk, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pekerjaan, kewarganegaraan, golongan_darah, email, password, role, no_hp, alamat, rt, rw) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$pdo->beginTransaction();
foreach ($users as $u) {
    $stmtInsert->execute([
        $u['nik'], $u['no_kk'], $u['nama'], $u['tempat_lahir'], $u['tanggal_lahir'],
        $u['jenis_kelamin'], $u['agama'], $u['status_perkawinan'], $u['pekerjaan'],
        $u['kewarganegaraan'], $u['golongan_darah'], $u['email'], $u['password'],
        $u['role'], $u['no_hp'], $u['alamat'], $u['rt'], $u['rw']
    ]);
}
$pdo->commit();
echo "SQLite Users Inserted Successfully!\n";

// Generate MySQL Dump (database.sql)
$sqlDump = "-- Database Dump for GoDesa (Desa Cibuntu, Kec. Cibitung, Kab. Bekasi)\n";
$sqlDump .= "-- Hosted at maoneart.my.id\n";
$sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

$sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n";
$sqlDump .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
$sqlDump .= "START TRANSACTION;\n";
$sqlDump .= "SET time_zone = '+07:00';\n\n";

$sqlDump .= "CREATE DATABASE IF NOT EXISTS `u9585642_godesa` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
$sqlDump .= "USE `u9585642_godesa`;\n\n";

$sqlDump .= "DROP TABLE IF EXISTS `users`;\n";
$sqlDump .= "CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(20) NOT NULL UNIQUE,
  `no_kk` varchar(20) DEFAULT NULL,
  `nama` varchar(150) NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT 'Bekasi',
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT 'Laki-laki',
  `agama` varchar(50) DEFAULT 'Islam',
  `status_perkawinan` varchar(50) DEFAULT 'Kawin',
  `pekerjaan` varchar(100) DEFAULT 'Wiraswasta',
  `kewarganegaraan` varchar(20) DEFAULT 'WNI',
  `golongan_darah` varchar(5) DEFAULT '-',
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'warga',
  `no_hp` varchar(25) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `rt` varchar(10) DEFAULT '003',
  `rw` varchar(10) DEFAULT '001',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rt_rw` (`rw`,`rt`),
  KEY `idx_nik` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sqlDump .= "INSERT INTO `users` (`id`, `nik`, `no_kk`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `agama`, `status_perkawinan`, `pekerjaan`, `kewarganegaraan`, `golongan_darah`, `email`, `password`, `role`, `no_hp`, `alamat`, `rt`, `rw`) VALUES\n";

$values = [];
$uid = 1;
foreach ($users as $u) {
    $nik = addslashes($u['nik']);
    $noKk = addslashes($u['no_kk']);
    $nama = addslashes($u['nama']);
    $tempat = addslashes($u['tempat_lahir']);
    $tgl = $u['tanggal_lahir'];
    $jk = $u['jenis_kelamin'];
    $agama = $u['agama'];
    $statusKawin = $u['status_perkawinan'];
    $pekerjaan = addslashes($u['pekerjaan']);
    $wargaNegara = $u['kewarganegaraan'];
    $goldar = $u['golongan_darah'];
    $email = $u['email'] ? "'" . addslashes($u['email']) . "'" : "NULL";
    $pwd = addslashes($u['password']);
    $role = $u['role'];
    $hp = $u['no_hp'] ? "'" . addslashes($u['no_hp']) . "'" : "NULL";
    $alamat = addslashes($u['alamat']);
    $rt = $u['rt'];
    $rw = $u['rw'];

    $values[] = "({$uid}, '{$nik}', '{$noKk}', '{$nama}', '{$tempat}', '{$tgl}', '{$jk}', '{$agama}', '{$statusKawin}', '{$pekerjaan}', '{$wargaNegara}', '{$goldar}', {$email}, '{$pwd}', '{$role}', {$hp}, '{$alamat}', '{$rt}', '{$rw}')";
    $uid++;
}
$sqlDump .= implode(",\n", $values) . ";\n\n";

// Table surat
$sqlDump .= "DROP TABLE IF EXISTS `surat`;\n";
$sqlDump .= "CREATE TABLE `surat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_surat` varchar(100) DEFAULT NULL UNIQUE,
  `nomor_pengantar_rt` varchar(100) DEFAULT NULL,
  `nomor_pengantar_rw` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `jenis_surat` varchar(100) NOT NULL,
  `keperluan` text NOT NULL,
  `data_tambahan` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'diajukan',
  `catatan` text DEFAULT NULL,
  `rt_id` int(11) DEFAULT NULL,
  `catatan_rt` text DEFAULT NULL,
  `tanggal_rt` datetime DEFAULT NULL,
  `rw_id` int(11) DEFAULT NULL,
  `catatan_rw` text DEFAULT NULL,
  `tanggal_rw` datetime DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `catatan_staff` text DEFAULT NULL,
  `lurah_id` int(11) DEFAULT NULL,
  `kades_id` int(11) DEFAULT NULL,
  `tanggal_disetujui` datetime DEFAULT NULL,
  `qr_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

// Table laporan
$sqlDump .= "DROP TABLE IF EXISTS `laporan`;\n";
$sqlDump .= "CREATE TABLE `laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','diproses','selesai','ditolak') DEFAULT 'menunggu',
  `tanggapan` text DEFAULT NULL,
  `petugas_nama` varchar(100) DEFAULT NULL,
  `foto_selesai` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

// Table agenda
$sqlDump .= "DROP TABLE IF EXISTS `agenda`;\n";
$sqlDump .= "CREATE TABLE `agenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kegiatan` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` varchar(10) NOT NULL,
  `waktu_selesai` varchar(10) DEFAULT NULL,
  `penanggung_jawab` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'akan_datang',
  `peserta_count` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

// Table pengumuman
$sqlDump .= "DROP TABLE IF EXISTS `pengumuman`;\n";
$sqlDump .= "CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `kategori` varchar(50) DEFAULT 'Info',
  `banner` varchar(255) DEFAULT NULL,
  `penulis_id` int(11) DEFAULT 1,
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sqlDump .= "COMMIT;\n";

file_put_contents($sqlDumpPath, $sqlDump);
echo "File database.sql generated successfully (" . filesize($sqlDumpPath) . " bytes)!\n";
