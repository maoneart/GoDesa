import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../config/app_theme.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';
import '../widgets/maoneart_modal.dart';
import 'surat/surat_list_screen.dart';
import 'surat/surat_create_screen.dart';
import 'warga/warga_directory_screen.dart';
import 'profil/profil_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final AuthService _auth = AuthService();
  int _currentIndex = 0;
  Map<String, dynamic>? _stats;
  List<dynamic> _pengumuman = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
    _auth.addListener(_onAuthChange);
  }

  @override
  void dispose() {
    _auth.removeListener(_onAuthChange);
    super.dispose();
  }

  void _onAuthChange() {
    if (mounted) setState(() {});
  }

  Future<void> _loadData() async {
    final res = await ApiService.getDashboardStats();
    if (mounted && res['success'] == true) {
      setState(() {
        _stats = res['data']['stats'];
        _pengumuman = res['data']['pengumuman'] ?? [];
        _isLoading = false;
      });
    } else {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = _auth.currentUser;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(64),
        child: Container(
          padding: const EdgeInsets.only(top: 28, left: 16, right: 16, bottom: 8),
          color: Colors.white,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  const Icon(Icons.location_on, color: AppTheme.gojekGreen, size: 20),
                  const SizedBox(width: 6),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        'Desa Cibuntu',
                        style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.w800,
                          fontSize: 14,
                          color: const Color(0xFF0F172A),
                        ),
                      ),
                      Text(
                        'Kecamatan Cibitung',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          color: const Color(0xFF64748B),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              InkWell(
                onTap: _showRoleSwitcher,
                borderRadius: BorderRadius.circular(20),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  decoration: BoxDecoration(
                    color: AppTheme.gojekLightGreen,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: AppTheme.gojekGreen.withOpacity(0.3)),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.swap_horiz, size: 14, color: AppTheme.gojekGreen),
                      const SizedBox(width: 4),
                      Text(
                        user?.roleDisplay ?? 'Warga',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          fontWeight: FontWeight.w800,
                          color: AppTheme.gojekDarkGreen,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: _loadData,
        color: AppTheme.gojekGreen,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. Gojek-Style Village Wallet Card (GoDesa Pay)
              _buildWalletCard(user),
              const SizedBox(height: 18),

              // 2. Panel Kerja Perangkat (Jika Aparat Desa / RT / RW)
              if (user != null && user.role != 'warga')
                _buildExecutivePanel(user),

              // 3. 8-Grid Services
              _buildServiceGrid(),
              const SizedBox(height: 20),

              // 4. Banner Pengumuman Desa (GoWarta)
              _buildPengumumanSection(),
              const SizedBox(height: 20),

              // 5. Statistik Desa
              _buildStatsRow(),
              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (idx) {
          if (idx == 1) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => const SuratListScreen()));
          } else if (idx == 2) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => const WargaDirectoryScreen()));
          } else if (idx == 3) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => const ProfilScreen()));
          } else {
            setState(() => _currentIndex = idx);
          }
        },
        type: BottomNavigationBarThemeData().type ?? BottomNavigationBarType.fixed,
        selectedItemColor: AppTheme.gojekGreen,
        unselectedItemColor: const Color(0xFF94A3B8),
        selectedLabelStyle: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700, fontSize: 11),
        unselectedLabelStyle: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w600, fontSize: 11),
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_filled), label: 'Beranda'),
          BottomNavigationBarItem(icon: Icon(Icons.description_outlined), label: 'GoSurat'),
          BottomNavigationBarItem(icon: Icon(Icons.people_alt_outlined), label: 'GoWarga'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Profil'),
        ],
      ),
    );
  }

  Widget _buildWalletCard(dynamic user) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF00AA13), Color(0xFF00880D)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(22),
        boxShadow: [
          BoxShadow(
            color: AppTheme.gojekGreen.withOpacity(0.35),
            blurRadius: 18,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    width: 38,
                    height: 38,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.badge, color: Colors.white, size: 22),
                  ),
                  const SizedBox(width: 10),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'GoDesa ID Warga',
                        style: GoogleFonts.plusJakartaSans(
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                          fontSize: 13,
                        ),
                      ),
                      Text(
                        user?.nik ?? '3216071405950001',
                        style: TextStyle(
                          color: Colors.white.withOpacity(0.85),
                          fontSize: 11,
                          fontFamily: 'monospace',
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  'RT ${user?.rt ?? '003'} / RW ${user?.rw ?? '001'}',
                  style: GoogleFonts.plusJakartaSans(
                    color: Colors.white,
                    fontWeight: FontWeight.w800,
                    fontSize: 10,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Pemegang Akun Resmi',
                    style: GoogleFonts.plusJakartaSans(
                      color: Colors.white.withOpacity(0.8),
                      fontSize: 11,
                    ),
                  ),
                  Text(
                    user?.nama ?? 'Hermawan',
                    style: GoogleFonts.plusJakartaSans(
                      color: Colors.white,
                      fontWeight: FontWeight.w900,
                      fontSize: 15,
                    ),
                  ),
                ],
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    'Iuran Kas RT:',
                    style: GoogleFonts.plusJakartaSans(
                      color: Colors.white.withOpacity(0.8),
                      fontSize: 10,
                    ),
                  ),
                  Text(
                    'Lunas Sep 2026',
                    style: GoogleFonts.plusJakartaSans(
                      color: Colors.yellowAccent,
                      fontWeight: FontWeight.w800,
                      fontSize: 13,
                    ),
                  ),
                ],
              ),
            ],
          ),
          const SizedBox(height: 16),
          // 4 Quick Actions
          Container(
            padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 10),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildQuickAction(Icons.note_add_rounded, 'Buat Surat', () {
                  Navigator.push(context, MaterialPageRoute(builder: (_) => const SuratCreateScreen()));
                }),
                _buildQuickAction(Icons.badge_rounded, 'GoWarga', () {
                  Navigator.push(context, MaterialPageRoute(builder: (_) => const WargaDirectoryScreen()));
                }),
                _buildQuickAction(Icons.history_edu_rounded, 'Riwayat', () {
                  Navigator.push(context, MaterialPageRoute(builder: (_) => const SuratListScreen()));
                }),
                _buildQuickAction(Icons.swap_calls_rounded, 'Ganti Peran', _showRoleSwitcher),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickAction(IconData icon, String label, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(10),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
        child: Column(
          children: [
            Container(
              width: 34,
              height: 34,
              decoration: BoxDecoration(
                color: AppTheme.gojekLightGreen,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: AppTheme.gojekGreen, size: 18),
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF1E293B),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildExecutivePanel(dynamic user) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFECFDF5),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFA7F3D0)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              const Icon(Icons.verified_user, color: Color(0xFF059669), size: 22),
              const SizedBox(width: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Panel Kerja ${user.roleDisplay}',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12,
                      fontWeight: FontWeight.w800,
                      color: const Color(0xFF065F46),
                    ),
                  ),
                  Text(
                    'Verifikasi & tandatangani pengajuan surat',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 11,
                      color: const Color(0xFF047857),
                    ),
                  ),
                ],
              ),
            ],
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.push(context, MaterialPageRoute(builder: (_) => const SuratListScreen()));
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF059669),
              elevation: 0,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
            child: Text(
              'Tinjau',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 11,
                fontWeight: FontWeight.w800,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildServiceGrid() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              'Layanan Digital Desa',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 14,
                fontWeight: FontWeight.w800,
                color: const Color(0xFF0F172A),
              ),
            ),
            Text(
              'Super-App Cibuntu',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 11,
                fontWeight: FontWeight.w600,
                color: const Color(0xFF94A3B8),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        GridView.count(
          crossAxisCount: 4,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 14,
          crossAxisSpacing: 10,
          children: [
            _buildServiceItem(Icons.description, 'GoSurat', AppTheme.gojekGreen, () {
              Navigator.push(context, MaterialPageRoute(builder: (_) => const SuratListScreen()));
            }),
            _buildServiceItem(Icons.groups_3_rounded, 'GoWarga', const Color(0xFF0284C7), () {
              Navigator.push(context, MaterialPageRoute(builder: (_) => const WargaDirectoryScreen()));
            }),
            _buildServiceItem(Icons.campaign_rounded, 'GoLapor', const Color(0xFFEF4444), () {
              MaoneArtModal.showAlert(
                context: context,
                title: 'GoLapor Warga',
                message: 'Fitur pengaduan warga Desa Cibuntu aktif di versi web & segera hadir di rilis APK berikutnya!',
              );
            }),
            _buildServiceItem(Icons.event_available, 'GoAgenda', const Color(0xFF8B5CF6), () {
              MaoneArtModal.showAlert(
                context: context,
                title: 'GoAgenda Desa',
                message: 'Jadwal Musrenbangdes & Posyandu Melati Cibuntu terintegrasi.',
              );
            }),
            _buildServiceItem(Icons.handshake_rounded, 'GoBansos', const Color(0xFFF59E0B), () {
              MaoneArtModal.showAlert(
                context: context,
                title: 'GoBansos Peduli',
                message: 'Penyaluran Beras Cadangan Pangan 10 Kg & BLT Dana Desa 2026.',
              );
            }),
            _buildServiceItem(Icons.emergency_rounded, 'GoDarurat', const Color(0xFFDC2626), () {
              MaoneArtModal.showConfirm(
                context: context,
                title: 'PANGGILAN DARURAT?',
                message: 'Hubungi Ambulans Siaga 24 Jam atau Babinsa/Bhabinkamtibmas Cibuntu?',
                confirmText: 'Ya, Hubungi',
                isDanger: true,
                icon: Icons.phone_in_talk,
                onConfirm: () {},
              );
            }),
            _buildServiceItem(Icons.storefront_rounded, 'GoUMKM', const Color(0xFF10B981), () {
              MaoneArtModal.showAlert(
                context: context,
                title: 'GoUMKM Pasar Desa',
                message: 'Etalase produk kuliner dan jasa warga Desa Cibuntu.',
              );
            }),
            _buildServiceItem(Icons.swap_horiz_rounded, 'Pilih Role', const Color(0xFF64748B), _showRoleSwitcher),
          ],
        ),
      ],
    );
  }

  Widget _buildServiceItem(IconData icon, String title, Color color, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: color.withOpacity(0.12),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(height: 6),
          Text(
            title,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 11,
              fontWeight: FontWeight.w700,
              color: const Color(0xFF1E293B),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPengumumanSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Kabar & Berita Desa',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 14,
            fontWeight: FontWeight.w800,
            color: const Color(0xFF0F172A),
          ),
        ),
        const SizedBox(height: 10),
        if (_pengumuman.isEmpty)
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFFE2E8F0)),
            ),
            child: const Center(child: Text('Belum ada siaran pengumuman.')),
          )
        else
          Column(
            children: _pengumuman.take(2).map((item) {
              return Container(
                margin: const EdgeInsets.only(bottom: 10),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: AppTheme.gojekLightGreen,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            item['kategori'] ?? 'Info',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                              color: AppTheme.gojekDarkGreen,
                            ),
                          ),
                        ),
                        Text(
                          'Pemerintah Desa',
                          style: GoogleFonts.plusJakartaSans(fontSize: 10, color: const Color(0xFF94A3B8)),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Text(
                      item['judul'] ?? '',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 13,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF0F172A),
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      item['isi'] ?? '',
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF64748B), height: 1.4),
                    ),
                  ],
                ),
              );
            }).toList(),
          ),
      ],
    );
  }

  Widget _buildStatsRow() {
    final totalWarga = _stats?['total_warga'] ?? 284;
    final totalSurat = _stats?['total_surat'] ?? 3;

    return Row(
      children: [
        Expanded(
          child: Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFFE2E8F0)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Total Warga Terdata', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: const Color(0xFF64748B))),
                const SizedBox(height: 4),
                Text('$totalWarga Jiwa', style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.gojekGreen)),
              ],
            ),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFFE2E8F0)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Surat Diterbitkan', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: const Color(0xFF64748B))),
                const SizedBox(height: 4),
                Text('$totalSurat Berkas', style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: const Color(0xFF0284C7))),
              ],
            ),
          ),
        ),
      ],
    );
  }

  void _showRoleSwitcher() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return Container(
          padding: const EdgeInsets.all(20),
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(color: const Color(0xFFCBD5E1), borderRadius: BorderRadius.circular(2)),
                ),
              ),
              const SizedBox(height: 16),
              Text(
                'Beralih Peran (Simulasi SOP Desa)',
                style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.w800, color: const Color(0xFF0F172A)),
              ),
              const SizedBox(height: 4),
              Text(
                'Uji alur birokrasi berjenjang Warga ➔ RT ➔ RW ➔ Staff ➔ Kades:',
                style: GoogleFonts.plusJakartaSans(fontSize: 12, color: const Color(0xFF64748B)),
              ),
              const SizedBox(height: 16),
              _buildRoleOption('warga', 'Hermawan (Warga)', 'RT 003 / RW 001', Icons.person),
              _buildRoleOption('rt', 'Bpk. Sutisna (Ketua RT 003)', 'Pengantar RT 003 / RW 001', Icons.home_work),
              _buildRoleOption('rw', 'Bpk. H. Warsito (Ketua RW 001)', 'Mengetahui Pengantar se-RW 001', Icons.account_tree_outlined),
              _buildRoleOption('sekdes', 'H. Muhammad Ridwan, S.AP', 'Sekretaris Desa (Sekdes)', Icons.admin_panel_settings),
              _buildRoleOption('staff', 'Rahmat Hidayat (Kasi Pelayanan)', 'Loket Verifikasi Desa', Icons.badge),
              _buildRoleOption('kades', 'H. Abdul Rohim, S.Sos', 'Kepala Desa Cibuntu (Approval & TTD)', Icons.how_to_reg),
            ],
          ),
        );
      },
    );
  }

  Widget _buildRoleOption(String roleKey, String title, String subtitle, IconData icon) {
    final isSelected = _auth.currentUser?.role == roleKey || (roleKey == 'kades' && _auth.currentUser?.role == 'lurah');

    return ListTile(
      onTap: () {
        Navigator.pop(context);
        _auth.switchDemoRole(roleKey);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Beralih ke peran: $title'),
            backgroundColor: AppTheme.gojekGreen,
            duration: const Duration(seconds: 2),
          ),
        );
      },
      leading: Container(
        width: 40,
        height: 40,
        decoration: BoxDecoration(
          color: isSelected ? AppTheme.gojekLightGreen : const Color(0xFFF1F5F9),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Icon(icon, color: isSelected ? AppTheme.gojekGreen : const Color(0xFF64748B), size: 20),
      ),
      title: Text(title, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700, fontSize: 13)),
      subtitle: Text(subtitle, style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF64748B))),
      trailing: isSelected ? const Icon(Icons.check_circle, color: AppTheme.gojekGreen, size: 20) : null,
    );
  }
}
