import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../config/app_theme.dart';
import '../../models/user_model.dart';
import '../../services/api_service.dart';

class WargaDirectoryScreen extends StatefulWidget {
  const WargaDirectoryScreen({super.key});

  @override
  State<WargaDirectoryScreen> createState() => _WargaDirectoryScreenState();
}

class _WargaDirectoryScreenState extends State<WargaDirectoryScreen> {
  List<UserModel> _citizens = [];
  bool _isLoading = true;

  String _selectedRw = '';
  String _selectedRt = '';
  final TextEditingController _searchCtrl = TextEditingController();

  final List<String> _rwList = ['', '001', '002', '003', '004', '005'];
  final List<String> _rtList = ['', '001', '002', '003'];

  @override
  void initState() {
    super.initState();
    _loadWarga();
  }

  Future<void> _loadWarga() async {
    setState(() => _isLoading = true);
    final list = await ApiService.getWargaList(
      rw: _selectedRw.isEmpty ? null : _selectedRw,
      rt: _selectedRt.isEmpty ? null : _selectedRt,
      query: _searchCtrl.text.trim().isEmpty ? null : _searchCtrl.text.trim(),
    );

    if (mounted) {
      setState(() {
        _citizens = list;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'GoWarga - Data Kependudukan',
          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 16),
        ),
      ),
      body: Column(
        children: [
          // Filter & Search Box
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.white,
            child: Column(
              children: [
                // Search Input
                TextField(
                  controller: _searchCtrl,
                  onSubmitted: (_) => _loadWarga(),
                  decoration: InputDecoration(
                    hintText: 'Cari nama, NIK, No. KK, atau pekerjaan...',
                    prefixIcon: const Icon(Icons.search, color: Color(0xFF64748B)),
                    suffixIcon: IconButton(
                      icon: const Icon(Icons.clear, size: 18),
                      onPressed: () {
                        _searchCtrl.clear();
                        _loadWarga();
                      },
                    ),
                    filled: true,
                    fillColor: const Color(0xFFF1F5F9),
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
                    contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  ),
                ),
                const SizedBox(height: 10),
                // RW & RT Filter Dropdowns
                Row(
                  children: [
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                        decoration: BoxDecoration(
                          color: const Color(0xFFF1F5F9),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<String>(
                            value: _selectedRw,
                            isExpanded: true,
                            hint: const Text('Semua RW'),
                            items: _rwList.map((rw) {
                              return DropdownMenuItem(
                                value: rw,
                                child: Text(rw.isEmpty ? 'Semua RW' : 'RW $rw'),
                              );
                            }).toList(),
                            onChanged: (val) {
                              setState(() => _selectedRw = val ?? '');
                              _loadWarga();
                            },
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                        decoration: BoxDecoration(
                          color: const Color(0xFFF1F5F9),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<String>(
                            value: _selectedRt,
                            isExpanded: true,
                            hint: const Text('Semua RT'),
                            items: _rtList.map((rt) {
                              return DropdownMenuItem(
                                value: rt,
                                child: Text(rt.isEmpty ? 'Semua RT' : 'RT $rt'),
                              );
                            }).toList(),
                            onChanged: (val) {
                              setState(() => _selectedRt = val ?? '');
                              _loadWarga();
                            },
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // Total Counter & List
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Daftar Warga Terdata',
                  style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 13, color: const Color(0xFF0F172A)),
                ),
                Text(
                  '${_citizens.length} Jiwa',
                  style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700, fontSize: 12, color: AppTheme.gojekGreen),
                ),
              ],
            ),
          ),

          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(color: AppTheme.gojekGreen))
                : _citizens.isEmpty
                    ? Center(
                        child: Text(
                          'Tidak ada data warga ditemukan.',
                          style: GoogleFonts.plusJakartaSans(color: const Color(0xFF64748B)),
                        ),
                      )
                    : ListView.builder(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                        itemCount: _citizens.length,
                        itemBuilder: (context, idx) {
                          final c = _citizens[idx];
                          return _buildCitizenCard(c);
                        },
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildCitizenCard(UserModel c) {
    final isP = c.jenisKelamin == 'Perempuan';

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              color: isP ? const Color(0xFFFCE7F3) : AppTheme.gojekLightGreen,
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(
              isP ? Icons.female : Icons.male,
              color: isP ? const Color(0xFFDB2777) : AppTheme.gojekGreen,
              size: 24,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        c.nama,
                        style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.w800,
                          fontSize: 13,
                          color: const Color(0xFF0F172A),
                        ),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        'RT ${c.rt}/RW ${c.rw}',
                        style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w700, color: const Color(0xFF475569)),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(
                  'NIK: ${c.nik}',
                  style: GoogleFonts.plusJakartaSans(fontSize: 11, fontFamily: 'monospace', color: const Color(0xFF64748B)),
                ),
                Text(
                  'No. KK: ${c.noKk ?? '-'}',
                  style: GoogleFonts.plusJakartaSans(fontSize: 10, fontFamily: 'monospace', color: const Color(0xFF94A3B8)),
                ),
                const SizedBox(height: 6),
                Wrap(
                  spacing: 6,
                  runSpacing: 4,
                  children: [
                    _buildPill(c.pekerjaan ?? 'Wiraswasta', const Color(0xFFE0F2FE), const Color(0xFF0369A1)),
                    _buildPill(c.statusPerkawinan ?? 'Kawin', const Color(0xFFFEF3C7), const Color(0xFFB45309)),
                    if (c.golonganDarah != null && c.golonganDarah != '-')
                      _buildPill('Gol: ${c.golonganDarah}', const Color(0xFFDCFCE7), const Color(0xFF15803D)),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPill(String label, Color bg, Color text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(6)),
      child: Text(
        label,
        style: GoogleFonts.plusJakartaSans(fontSize: 9.5, fontWeight: FontWeight.w700, color: text),
      ),
    );
  }
}
