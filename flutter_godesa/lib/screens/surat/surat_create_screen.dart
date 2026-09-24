import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../config/app_theme.dart';
import '../../services/api_service.dart';
import '../../services/auth_service.dart';
import '../../widgets/maoneart_modal.dart';

class SuratCreateScreen extends StatefulWidget {
  const SuratCreateScreen({super.key});

  @override
  State<SuratCreateScreen> createState() => _SuratCreateScreenState();
}

class _SuratCreateScreenState extends State<SuratCreateScreen> {
  final _formKey = GlobalKey<FormState>();
  final AuthService _auth = AuthService();

  String _selectedJenis = 'Surat Keterangan Usaha (SKU)';
  final TextEditingController _keperluanCtrl = TextEditingController();

  // Field Khusus SKU
  final TextEditingController _namaUsahaCtrl = TextEditingController();
  final TextEditingController _bidangUsahaCtrl = TextEditingController();
  final TextEditingController _alamatUsahaCtrl = TextEditingController();
  final TextEditingController _sejakTahunCtrl = TextEditingController(text: '2021');

  // Field Khusus SKCK
  final TextEditingController _tujuanInstansiCtrl = TextEditingController(text: 'Polsek Cikarang Barat / Polres Metro Bekasi');

  // Field Khusus Domisili
  final TextEditingController _lamaTinggalCtrl = TextEditingController(text: '5 Tahun');
  String _statusTinggal = 'Rumah Milik Sendiri';

  bool _isSubmitting = false;

  final List<String> _jenisSuratList = [
    'Surat Keterangan Usaha (SKU)',
    'Surat Pengantar SKCK',
    'Surat Keterangan Domisili',
    'Surat Keterangan Tidak Mampu (SKTM)',
    'Surat Keterangan Belum Menikah',
    'Surat Keterangan Kematian',
  ];

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSubmitting = true);
    final user = _auth.currentUser;

    Map<String, dynamic> extra = {};
    if (_selectedJenis == 'Surat Keterangan Usaha (SKU)') {
      extra['nama_usaha'] = _namaUsahaCtrl.text.trim();
      extra['bidang_usaha'] = _bidangUsahaCtrl.text.trim();
      extra['alamat_usaha'] = _alamatUsahaCtrl.text.trim();
      extra['sejak_tahun'] = _sejakTahunCtrl.text.trim();
    } else if (_selectedJenis == 'Surat Pengantar SKCK') {
      extra['tujuan_instansi'] = _tujuanInstansiCtrl.text.trim();
    } else if (_selectedJenis == 'Surat Keterangan Domisili') {
      extra['lama_tinggal'] = _lamaTinggalCtrl.text.trim();
      extra['status_tinggal'] = _statusTinggal;
    }

    final res = await ApiService.createSurat(
      userId: user?.id ?? 4,
      jenisSurat: _selectedJenis,
      keperluan: _keperluanCtrl.text.trim(),
      extra: extra,
    );

    setState(() => _isSubmitting = false);

    if (mounted) {
      if (res['success'] == true) {
        MaoneArtModal.showAlert(
          context: context,
          title: 'Permohonan Terkirim!',
          message: 'Dokumen berhasil diajukan dan diteruskan ke Ketua RT untuk verifikasi surat pengantar.',
          buttonText: 'Lihat Daftar',
          isSuccess: true,
        );
        Navigator.pop(context, true);
      } else {
        MaoneArtModal.showAlert(
          context: context,
          title: 'Gagal Mengajukan',
          message: res['message'] ?? 'Terjadi kesalahan pada server.',
          isSuccess: false,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = _auth.currentUser;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'Ajukan Dokumen Baru',
          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 16),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Identitas Pemohon Box
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Data Pemohon (SIAK Kependudukan):', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF64748B))),
                    const SizedBox(height: 4),
                    Text(
                      '${user?.nama} (NIK: ${user?.nik})',
                      style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 13, color: const Color(0xFF0F172A)),
                    ),
                    Text(
                      'No. KK: ${user?.noKk ?? '-'} • RT ${user?.rt} / RW ${user?.rw}',
                      style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF475569)),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              // Dropdown Jenis Surat
              Text('Pilih Jenis Dokumen', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700, fontSize: 13)),
              const SizedBox(height: 6),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 14),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: const Color(0xFFCBD5E1)),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: _selectedJenis,
                    isExpanded: true,
                    style: GoogleFonts.plusJakartaSans(fontSize: 13, color: const Color(0xFF0F172A), fontWeight: FontWeight.w600),
                    items: _jenisSuratList.map((val) {
                      return DropdownMenuItem(value: val, child: Text(val));
                    }).toList(),
                    onChanged: (val) {
                      if (val != null) setState(() => _selectedJenis = val);
                    },
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Keperluan Pengajuan
              Text('Keperluan Pengajuan Surat', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700, fontSize: 13)),
              const SizedBox(height: 6),
              TextFormField(
                controller: _keperluanCtrl,
                decoration: InputDecoration(
                  hintText: 'Contoh: Pengajuan Tambahan Modal Usaha KUR Bank BRI',
                  filled: true,
                  fillColor: Colors.white,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: Color(0xFFCBD5E1))),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                ),
                validator: (val) => val == null || val.isEmpty ? 'Wajib diisi' : null,
              ),
              const SizedBox(height: 16),

              // Dynamic Fields per Surat
              if (_selectedJenis == 'Surat Keterangan Usaha (SKU)') ...[
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: AppTheme.gojekLightGreen,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: AppTheme.gojekGreen.withOpacity(0.3)),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Rincian Usaha Pemohon (SKU):', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 12, color: AppTheme.gojekDarkGreen)),
                      const SizedBox(height: 10),
                      _buildTextField(_namaUsahaCtrl, 'Nama Usaha / Toko', 'Contoh: Toko Kelontong Berkah'),
                      const SizedBox(height: 10),
                      _buildTextField(_bidangUsahaCtrl, 'Bidang / Jenis Usaha', 'Contoh: Perdagangan Sembako'),
                      const SizedBox(height: 10),
                      _buildTextField(_alamatUsahaCtrl, 'Alamat Tempat Usaha', 'Contoh: Kp. Cibuntu No. 12 RT 003 / RW 001'),
                      const SizedBox(height: 10),
                      _buildTextField(_sejakTahunCtrl, 'Beroperasi Sejak Tahun', 'Contoh: 2021', isNumber: true),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
              ],

              if (_selectedJenis == 'Surat Pengantar SKCK') ...[
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: const Color(0xFFEFF6FF),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: const Color(0xFFBFDBFE)),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Tujuan Pengantar SKCK:', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 12, color: const Color(0xFF1D4ED8))),
                      const SizedBox(height: 10),
                      _buildTextField(_tujuanInstansiCtrl, 'Tujuan Kantor Kepolisian', 'Polsek Cikarang Barat / Polres Metro Bekasi'),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
              ],

              // Tombol Submit
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: _isSubmitting ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.gojekGreen,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  child: _isSubmitting
                      ? const CircularProgressIndicator(color: Colors.white, strokeWidth: 2)
                      : Text(
                          'Kirim Permohonan ke Ketua RT',
                          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 14, color: Colors.white),
                        ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTextField(TextEditingController ctrl, String label, String hint, {bool isNumber = false}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.w700, color: const Color(0xFF1E293B))),
        const SizedBox(height: 4),
        TextFormField(
          controller: ctrl,
          keyboardType: isNumber ? TextInputType.number : TextInputType.text,
          decoration: InputDecoration(
            hintText: hint,
            filled: true,
            fillColor: Colors.white,
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: const BorderSide(color: Color(0xFFCBD5E1))),
            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          ),
        ),
      ],
    );
  }
}
