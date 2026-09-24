import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../config/app_theme.dart';
import '../../models/surat_model.dart';
import '../../services/api_service.dart';
import '../../services/auth_service.dart';
import '../../widgets/maoneart_modal.dart';

class SuratDetailScreen extends StatefulWidget {
  final SuratModel surat;
  const SuratDetailScreen({super.key, required this.surat});

  @override
  State<SuratDetailScreen> createState() => _SuratDetailScreenState();
}

class _SuratDetailScreenState extends State<SuratDetailScreen> {
  final AuthService _auth = AuthService();
  late SuratModel _surat;
  bool _isProcessing = false;

  @override
  void initState() {
    super.initState();
    _surat = widget.surat;
  }

  Future<void> _handleAction(String action) async {
    final user = _auth.currentUser;
    if (user == null) return;

    String confirmTitle = 'Konfirmasi Tindakan';
    String confirmMsg = 'Lanjutkan proses pengesahan dokumen ini?';

    if (action == 'verifikasi_rt') {
      confirmTitle = 'Setujui Pengantar RT?';
      confirmMsg = 'Anda akan mengesahkan Surat Pengantar RT untuk pemohon ${_surat.pemohonNama}.';
    } else if (action == 'verifikasi_rw') {
      confirmTitle = 'Pengesahan Mengetahui RW?';
      confirmMsg = 'Anda akan memberikan tanda mengetahui Ketua RW untuk pengantar ini.';
    } else if (action == 'verifikasi_staff') {
      confirmTitle = 'Verifikasi Berkas Desa?';
      confirmMsg = 'Berkas administrasi akan diteruskan ke meja Kepala Desa Cibuntu.';
    } else if (action == 'approve_kades') {
      confirmTitle = 'Tanda Tangan & Sahkan Kades?';
      confirmMsg = 'Anda akan menandatangani dokumen ini secara elektronik (e-Signature QR Code).';
    }

    MaoneArtModal.showConfirm(
      context: context,
      title: confirmTitle,
      message: confirmMsg,
      confirmText: 'Ya, Sahkan',
      cancelText: 'Batal',
      isDanger: false,
      icon: Icons.verified_user,
      onConfirm: () async {
        setState(() => _isProcessing = true);
        final res = await ApiService.approveSurat(
          suratId: _surat.id,
          action: action,
          userId: user.id,
          rt: user.rt,
          rw: user.rw,
        );
        setState(() => _isProcessing = false);

        if (mounted) {
          if (res['success'] == true) {
            MaoneArtModal.showAlert(
              context: context,
              title: 'Berhasil!',
              message: res['message'],
              isSuccess: true,
            );
            Navigator.pop(context, true);
          } else {
            MaoneArtModal.showAlert(
              context: context,
              title: 'Gagal',
              message: res['message'],
              isSuccess: false,
            );
          }
        }
      },
    );
  }

  void _openPrintUrl() async {
    final url = Uri.parse('http://127.0.0.1:8085/GoDesa/cetak_surat.php?id=${_surat.id}');
    if (await canLaunchUrl(url)) {
      await launchUrl(url, mode: LaunchMode.externalApplication);
    } else {
      MaoneArtModal.showAlert(
        context: context,
        title: 'Cetak Dokumen',
        message: 'Akses link cetak: http://localhost:8085/GoDesa/cetak_surat.php?id=${_surat.id}',
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = _auth.currentUser;
    final role = user?.role ?? 'warga';

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'Detail Dokumen Resmi',
          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 16),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Status & Header Box
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          _surat.jenisSurat,
                          style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.w800,
                            fontSize: 15,
                            color: const Color(0xFF0F172A),
                          ),
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: _surat.isApproved ? AppTheme.gojekLightGreen : const Color(0xFFFEF3C7),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          _surat.statusDisplay,
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 11,
                            fontWeight: FontWeight.w800,
                            color: _surat.isApproved ? AppTheme.gojekGreen : const Color(0xFFB45309),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(
                    'No. Registrasi: ${_surat.nomorSurat}',
                    style: const TextStyle(
                      fontSize: 12,
                      fontFamily: 'monospace',
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF475569),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Rantai Pengantar RT & RW
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Rantai Pengantar Kewilayahan (SOP Desa):', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 13)),
                  const SizedBox(height: 10),
                  _buildRowInfo('Pengantar RT:', _surat.nomorPengantarRt ?? 'Menunggu Pengantar RT'),
                  const SizedBox(height: 6),
                  _buildRowInfo('Pengantar RW:', _surat.nomorPengantarRw ?? 'Menunggu Pengantar RW'),
                  const SizedBox(height: 6),
                  _buildRowInfo('Pemohon:', '${_surat.pemohonNama} (RT ${_surat.pemohonRt}/${_surat.pemohonRw})'),
                  const SizedBox(height: 6),
                  _buildRowInfo('Keperluan:', _surat.keperluan),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Tombol Cetak PDF jika sudah Sah
            if (_surat.isApproved)
              Padding(
                padding: const EdgeInsets.only(bottom: 16),
                child: SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton.icon(
                    onPressed: _openPrintUrl,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.gojekGreen,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                    icon: const Icon(Icons.print, color: Colors.white),
                    label: Text(
                      'Cetak / Unduh Dokumen Sah (PDF)',
                      style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, color: Colors.white),
                    ),
                  ),
                ),
              ),

            // Tombol Aksi Berdasarkan Giliran Peran SOP (Role Gating)
            if (role == 'rt' && _surat.status == 'diajukan')
              _buildActionButton(
                label: 'Setujui Pengantar RT',
                icon: Icons.approval,
                color: const Color(0xFF0D9488),
                onTap: () => _handleAction('verifikasi_rt'),
              ),

            if (role == 'rw' && _surat.status == 'diverifikasi_rt')
              _buildActionButton(
                label: 'Sahkan Mengetahui RW',
                icon: Icons.account_tree_outlined,
                color: const Color(0xFF4F46E5),
                onTap: () => _handleAction('verifikasi_rw'),
              ),

            if ((role == 'staff' || role == 'sekdes') && (_surat.status == 'diverifikasi_rw' || _surat.status == 'diverifikasi_rt'))
              _buildActionButton(
                label: 'Verifikasi Berkas Pelayanan Desa',
                icon: Icons.checklist_rtl,
                color: const Color(0xFF2563EB),
                onTap: () => _handleAction('verifikasi_staff'),
              ),

            if ((role == 'kades' || role == 'lurah') && (_surat.status == 'diverifikasi_staff' || _surat.status == 'diverifikasi_rw'))
              _buildActionButton(
                label: 'Tanda Tangan & Setujui Kades (e-Signature)',
                icon: Icons.draw,
                color: AppTheme.gojekGreen,
                onTap: () => _handleAction('approve_kades'),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildRowInfo(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 110,
          child: Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF64748B))),
        ),
        Expanded(
          child: Text(value, style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.w700, color: const Color(0xFF0F172A))),
        ),
      ],
    );
  }

  Widget _buildActionButton({
    required String label,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: SizedBox(
        width: double.infinity,
        height: 48,
        child: ElevatedButton.icon(
          onPressed: _isProcessing ? null : onTap,
          style: ElevatedButton.styleFrom(
            backgroundColor: color,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
          ),
          icon: Icon(icon, color: Colors.white),
          label: Text(
            label,
            style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, color: Colors.white, fontSize: 13),
          ),
        ),
      ),
    );
  }
}
