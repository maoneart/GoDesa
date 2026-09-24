class UserModel {
  final int id;
  final String nik;
  final String? noKk;
  final String nama;
  final String? tempatLahir;
  final String? tanggalLahir;
  final String? jenisKelamin;
  final String? agama;
  final String? statusPerkawinan;
  final String? pekerjaan;
  final String? kewarganegaraan;
  final String? golonganDarah;
  final String? email;
  final String role;
  final String? noHp;
  final String? alamat;
  final String rt;
  final String rw;

  UserModel({
    required this.id,
    required this.nik,
    this.noKk,
    required this.nama,
    this.tempatLahir,
    this.tanggalLahir,
    this.jenisKelamin,
    this.agama,
    this.statusPerkawinan,
    this.pekerjaan,
    this.kewarganegaraan,
    this.golonganDarah,
    this.email,
    required this.role,
    this.noHp,
    this.alamat,
    required this.rt,
    required this.rw,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      nik: json['nik']?.toString() ?? '',
      noKk: json['no_kk']?.toString(),
      nama: json['nama']?.toString() ?? '',
      tempatLahir: json['tempat_lahir']?.toString(),
      tanggalLahir: json['tanggal_lahir']?.toString(),
      jenisKelamin: json['jenis_kelamin']?.toString(),
      agama: json['agama']?.toString(),
      statusPerkawinan: json['status_perkawinan']?.toString(),
      pekerjaan: json['pekerjaan']?.toString(),
      kewarganegaraan: json['kewarganegaraan']?.toString(),
      golonganDarah: json['golongan_darah']?.toString(),
      email: json['email']?.toString(),
      role: json['role']?.toString() ?? 'warga',
      noHp: json['no_hp']?.toString(),
      alamat: json['alamat']?.toString(),
      rt: json['rt']?.toString() ?? '003',
      rw: json['rw']?.toString() ?? '001',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nik': nik,
      'no_kk': noKk,
      'nama': nama,
      'tempat_lahir': tempatLahir,
      'tanggal_lahir': tanggalLahir,
      'jenis_kelamin': jenisKelamin,
      'agama': agama,
      'status_perkawinan': statusPerkawinan,
      'pekerjaan': pekerjaan,
      'kewarganegaraan': kewarganegaraan,
      'golongan_darah': golonganDarah,
      'email': email,
      'role': role,
      'no_hp': noHp,
      'alamat': alamat,
      'rt': rt,
      'rw': rw,
    };
  }

  String get roleDisplay {
    switch (role) {
      case 'kades':
      case 'lurah':
        return 'Kepala Desa';
      case 'staff':
        return 'Staff Pelayanan';
      case 'rw':
        return 'Ketua RW $rw';
      case 'rt':
        return 'Ketua RT $rt / RW $rw';
      case 'admin':
        return 'Admin Desa';
      default:
        return 'Warga (RT $rt/RW $rw)';
    }
  }
}
