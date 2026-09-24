import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../config/app_theme.dart';
import '../../config/api_config.dart';
import '../../services/auth_service.dart';
import '../../widgets/maoneart_modal.dart';

class ProfilScreen extends StatefulWidget {
  const ProfilScreen({super.key});

  @override
  State<ProfilScreen> createState() => _ProfilScreenState();
}

class _ProfilScreenState extends State<ProfilScreen> {
  final AuthService _auth = AuthService();

  void _showApiSettings() {
    final ctrl = TextEditingController(text: ApiConfig.baseUrl);

    showDialog(
      context: context,
      builder: (ctx) {
        return AlertDialog(
          title: Text('Pengaturan API Host', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 16)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Pilih atau masukkan URL API Backend:',
                style: GoogleFonts.plusJakartaSans(fontSize: 12, color: const Color(0xFF64748B)),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: ctrl,
                decoration: InputDecoration(
                  hintText: 'https://maoneart.my.id/godesa/api',
                  filled: true,
                  fillColor: const Color(0xFFF1F5F9),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                ),
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  TextButton(
                    onPressed: () {
                      ctrl.text = ApiConfig.productionUrl;
                    },
                    child: const Text('Hosting maoneart.my.id', style: TextStyle(fontSize: 11)),
                  ),
                  TextButton(
                    onPressed: () {
                      ctrl.text = ApiConfig.localUrl;
                    },
                    child: const Text('Local 8085', style: TextStyle(fontSize: 11)),
                  ),
                ],
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Batal'),
            ),
            ElevatedButton(
              onPressed: () async {
                await ApiConfig.setBaseUrl(ctrl.text);
                Navigator.pop(ctx);
                setState(() {});
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Host API berhasil diperbarui')),
                );
              },
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.gojekGreen),
              child: const Text('Simpan', style: TextStyle(color: Colors.white)),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final user = _auth.currentUser;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'Profil & Identitas Kependudukan',
          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 16),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.settings),
            onPressed: _showApiSettings,
            tooltip: 'Pengaturan Host API',
          )
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            // User Header Card
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(22),
              ),
              child: Row(
                children: [
                  Container(
                    width: 56,
                    height: 56,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(18),
                    ),
                    child: const Icon(Icons.person, color: Colors.white, size: 30),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          user?.nama ?? 'Hermawan',
                          style: GoogleFonts.plusJakartaSans(
                            color: Colors.white,
                            fontWeight: FontWeight.w800,
                            fontSize: 16,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          user?.email ?? 'warga@cibuntu.desa.id',
                          style: GoogleFonts.plusJakartaSans(color: const Color(0xFF94A3B8), fontSize: 12),
                        ),
                        const SizedBox(height: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: AppTheme.gojekGreen.withOpacity(0.3),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            '${user?.roleDisplay.toUpperCase()}',
                            style: GoogleFonts.plusJakartaSans(
                              color: const Color(0xFF86EFAC),
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Biodata Lengkap Sesuai KTP / KK
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Biodata Kependudukan (SIAK Desa):',
                    style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 13, color: const Color(0xFF0F172A)),
                  ),
                  const SizedBox(height: 14),
                  _buildBioItem('Nomor Induk Kependudukan (NIK)', user?.nik ?? '-'),
                  _buildBioItem('Nomor Kartu Keluarga (KK)', user?.noKk ?? '32160701030001'),
                  _buildBioItem('Tempat & Tanggal Lahir', '${user?.tempatLahir ?? 'Bekasi'}, ${user?.tanggalLahir ?? '1995-05-14'}'),
                  _buildBioItem('Jenis Kelamin', user?.jenisKelamin ?? 'Laki-laki'),
                  _buildBioItem('Agama / Kewarganegaraan', '${user?.agama ?? 'Islam'} / ${user?.kewarganegaraan ?? 'WNI'}'),
                  _buildBioItem('Pekerjaan', user?.pekerjaan ?? 'Wiraswasta'),
                  _buildBioItem('Status Perkawinan', user?.statusPerkawinan ?? 'Kawin'),
                  _buildBioItem('Golongan Darah', user?.golonganDarah ?? 'O'),
                  _buildBioItem('Wilayah Domisili', 'RT ${user?.rt ?? '003'} / RW ${user?.rw ?? '001'} Desa Cibuntu'),
                  _buildBioItem('Alamat Rumah', user?.alamat ?? 'Kp. Cibuntu RT 003 / RW 001'),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Host Server Information
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Server API Host:', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: const Color(0xFF64748B))),
                      const SizedBox(height: 2),
                      Text(
                        ApiConfig.baseUrl,
                        style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700, fontFamily: 'monospace'),
                      ),
                    ],
                  ),
                  TextButton(
                    onPressed: _showApiSettings,
                    child: const Text('Ganti', style: TextStyle(fontSize: 12, color: AppTheme.gojekGreen)),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBioItem(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 10, color: const Color(0xFF94A3B8))),
          const SizedBox(height: 2),
          Text(
            value,
            style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w700, color: const Color(0xFF1E293B)),
          ),
          const Divider(height: 12, color: Color(0xFFF1F5F9)),
        ],
      ),
    );
  }
}
