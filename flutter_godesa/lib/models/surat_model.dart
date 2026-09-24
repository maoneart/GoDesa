class SuratModel {
  final int id;
  final String nomorSurat;
  final String? nomorPengantarRt;
  final String? nomorPengantarRw;
  final int userId;
  final String jenisSurat;
  final String keperluan;
  final Map<String, dynamic> dataTambahan;
  final String status;
  final String? catatan;
  final String? catatanRt;
  final String? catatanRw;
  final String? catatanStaff;
  final String? pemohonNama;
  final String? pemohonNik;
  final String? pemohonRt;
  final String? pemohonRw;
  final String? tanggalDisetujui;
  final String? qrToken;
  final String? createdAt;

  SuratModel({
    required this.id,
    required this.nomorSurat,
    this.nomorPengantarRt,
    this.nomorPengantarRw,
    required this.userId,
    required this.jenisSurat,
    required this.keperluan,
    required this.dataTambahan,
    required this.status,
    this.catatan,
    this.catatanRt,
    this.catatanRw,
    this.catatanStaff,
    this.pemohonNama,
    this.pemohonNik,
    this.pemohonRt,
    this.pemohonRw,
    this.tanggalDisetujui,
    this.qrToken,
    this.createdAt,
  });

  factory SuratModel.fromJson(Map<String, dynamic> json) {
    Map<String, dynamic> extra = {};
    if (json['data_tambahan'] is Map) {
      extra = Map<String, dynamic>.from(json['data_tambahan']);
    }

    return SuratModel(
      id: int.tryParse(json['id'].toString()) ?? 0,
      nomorSurat: json['nomor_surat']?.toString() ?? '',
      nomorPengantarRt: json['nomor_pengantar_rt']?.toString(),
      nomorPengantarRw: json['nomor_pengantar_rw']?.toString(),
      userId: int.tryParse(json['user_id'].toString()) ?? 0,
      jenisSurat: json['jenis_surat']?.toString() ?? '',
      keperluan: json['keperluan']?.toString() ?? '',
      dataTambahan: extra,
      status: json['status']?.toString() ?? 'diajukan',
      catatan: json['catatan']?.toString(),
      catatanRt: json['catatan_rt']?.toString(),
      catatanRw: json['catatan_rw']?.toString(),
      catatanStaff: json['catatan_staff']?.toString(),
      pemohonNama: json['pemohon_nama']?.toString(),
      pemohonNik: json['pemohon_nik']?.toString(),
      pemohonRt: json['rt']?.toString(),
      pemohonRw: json['rw']?.toString(),
      tanggalDisetujui: json['tanggal_disetujui']?.toString(),
      qrToken: json['qr_token']?.toString(),
      createdAt: json['created_at']?.toString(),
    );
  }

  String get statusDisplay {
    switch (status) {
      case 'diajukan':
        return '1/4 Menunggu RT';
      case 'diverifikasi_rt':
        return '2/4 Menunggu RW';
      case 'diverifikasi_rw':
        return '3/4 Loket Desa';
      case 'diverifikasi_staff':
        return '4/4 Menunggu Kades';
      case 'disetujui_kades':
      case 'disetujui_lurah':
        return 'Disetujui Kades (Sah)';
      case 'ditolak':
        return 'Ditolak';
      default:
        return status;
    }
  }

  bool get isApproved => status == 'disetujui_kades' || status == 'disetujui_lurah';
}
