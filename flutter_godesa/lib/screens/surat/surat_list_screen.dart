import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../config/app_theme.dart';
import '../../models/surat_model.dart';
import '../../services/api_service.dart';
import '../../services/auth_service.dart';
import 'surat_create_screen.dart';
import 'surat_detail_screen.dart';

class SuratListScreen extends StatefulWidget {
  const SuratListScreen({super.key});

  @override
  State<SuratListScreen> createState() => _SuratListScreenState();
}

class _SuratListScreenState extends State<SuratListScreen> {
  final AuthService _auth = AuthService();
  List<SuratModel> _letters = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadLetters();
  }

  Future<void> _loadLetters() async {
    setState(() => _isLoading = true);
    final user = _auth.currentUser;
    if (user != null) {
      final list = await ApiService.getSuratList(
        userId: user.id,
        role: user.role,
        rt: user.rt,
        rw: user.rw,
      );
      if (mounted) {
        setState(() {
          _letters = list;
          _isLoading = false;
        });
      }
    } else {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = _auth.currentUser;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'GoSurat Desa Cibuntu',
          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 16),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadLetters,
          )
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          final res = await Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const SuratCreateScreen()),
          );
          if (res == true) _loadLetters();
        },
        backgroundColor: AppTheme.gojekGreen,
        icon: const Icon(Icons.add, color: Colors.white),
        label: Text(
          'Ajukan Surat',
          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, color: Colors.white),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.gojekGreen))
          : _letters.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.folder_open, size: 64, color: Colors.grey.shade400),
                      const SizedBox(height: 12),
                      Text(
                        'Belum ada permohonan surat',
                        style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.w700,
                          fontSize: 14,
                          color: const Color(0xFF64748B),
                        ),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _loadLetters,
                  color: AppTheme.gojekGreen,
                  child: ListView.builder(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    itemCount: _letters.length,
                    itemBuilder: (context, idx) {
                      final item = _letters[idx];
                      return _buildSuratCard(item, user);
                    },
                  ),
                ),
    );
  }

  Widget _buildSuratCard(SuratModel item, dynamic user) {
    Color badgeColor = const Color(0xFFF59E0B);
    Color badgeBg = const Color(0xFFFEF3C7);

    if (item.isApproved) {
      badgeColor = AppTheme.gojekGreen;
      badgeBg = AppTheme.gojekLightGreen;
    } else if (item.status == 'ditolak') {
      badgeColor = AppTheme.gojekRed;
      badgeBg = const Color(0xFFFEE2E2);
    } else if (item.status == 'diverifikasi_rt' || item.status == 'diverifikasi_rw') {
      badgeColor = const Color(0xFF0284C7);
      badgeBg = const Color(0xFFE0F2FE);
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () async {
            final res = await Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => SuratDetailScreen(surat: item)),
            );
            if (res == true) _loadLetters();
          },
          borderRadius: BorderRadius.circular(16),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Container(
                          width: 36,
                          height: 36,
                          decoration: BoxDecoration(
                            color: AppTheme.gojekLightGreen,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: const Icon(Icons.description, color: AppTheme.gojekGreen, size: 20),
                        ),
                        const SizedBox(width: 10),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              item.jenisSurat,
                              style: GoogleFonts.plusJakartaSans(
                                fontWeight: FontWeight.w800,
                                fontSize: 13,
                                color: const Color(0xFF0F172A),
                              ),
                            ),
                            Text(
                              item.nomorSurat,
                              style: const TextStyle(
                                fontSize: 11,
                                fontFamily: 'monospace',
                                color: Color(0xFF64748B),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: badgeBg,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        item.statusDisplay,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 10,
                          fontWeight: FontWeight.w800,
                          color: badgeColor,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF8FAFC),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Pemohon:', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF64748B))),
                          Text(
                            '${item.pemohonNama ?? user?.nama} (RT ${item.pemohonRt ?? user?.rt}/${item.pemohonRw ?? user?.rw})',
                            style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.w700),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Keperluan:', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF64748B))),
                          Expanded(
                            child: Text(
                              item.keperluan,
                              textAlign: TextAlign.end,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.w600),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      item.createdAt ?? 'September 2026',
                      style: GoogleFonts.plusJakartaSans(fontSize: 10, color: const Color(0xFF94A3B8)),
                    ),
                    Row(
                      children: [
                        Text(
                          'Detail & Tindakan',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.gojekGreen,
                          ),
                        ),
                        const Icon(Icons.chevron_right, size: 16, color: AppTheme.gojekGreen),
                      ],
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
