import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../config/app_theme.dart';
import '../../config/api_config.dart';
import '../../services/auth_service.dart';
import '../../widgets/maoneart_modal.dart';
import '../home_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _nikController = TextEditingController();
  final TextEditingController _passController = TextEditingController();
  bool _obscurePassword = true;
  bool _isLoading = false;

  @override
  void dispose() {
    _nikController.dispose();
    _passController.dispose();
    super.dispose();
  }

  void _handleQuickFill({
    required String title,
    required String nik,
    required String pass,
  }) {
    _nikController.text = nik;
    _passController.text = pass;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          'Akun $title terpilih. Tekan "Masuk ke GoDesa" untuk login.',
          style: GoogleFonts.plusJakartaSans(fontSize: 12),
        ),
        backgroundColor: const Color(0xFF0F172A),
        behavior: SnackBarBehavior.floating,
        duration: const Duration(seconds: 2),
      ),
    );
  }

  Future<void> _performLogin() async {
    final nik = _nikController.text.trim();
    final pass = _passController.text.trim();

    if (nik.isEmpty) {
      MaoneArtModal.showAlert(
        context: context,
        title: 'NIK Belum Diisi',
        message: 'Silakan masukkan 16 digit Nomor Induk Kependudukan (NIK) Anda.',
        icon: Icons.badge_outlined,
      );
      return;
    }

    if (pass.isEmpty) {
      MaoneArtModal.showAlert(
        context: context,
        title: 'Password Belum Diisi',
        message: 'Silakan masukkan kata sandi akun Anda.',
        icon: Icons.lock_outline,
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      final success = await AuthService().login(nik, pass);
      if (!mounted) return;
      setState(() => _isLoading = false);

      if (success) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const HomeScreen()),
        );
      } else {
        MaoneArtModal.showAlert(
          context: context,
          title: 'Gagal Masuk',
          message: 'NIK atau Password yang Anda masukkan salah. Pastikan data akun Anda sudah sesuai.',
          isDanger: true,
          icon: Icons.error_outline_rounded,
        );
      }
    } catch (e) {
      if (!mounted) return;
      setState(() => _isLoading = false);
      MaoneArtModal.showAlert(
        context: context,
        title: 'Koneksi Server Bermasalah',
        message: 'Tidak dapat terhubung ke server API Desa ($e). Pastikan koneksi internet Anda aktif.',
        isDanger: true,
        icon: Icons.wifi_off_rounded,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 12),

              // Logo & App Branding
              Row(
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      color: AppTheme.gojekGreen,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(
                          color: AppTheme.gojekGreen.withOpacity(0.35),
                          blurRadius: 16,
                          offset: const Offset(0, 6),
                        )
                      ],
                    ),
                    child: const Icon(
                      Icons.location_city_rounded,
                      size: 28,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Text(
                            'Go',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 22,
                              fontWeight: FontWeight.w900,
                              color: AppTheme.gojekGreen,
                              letterSpacing: -0.5,
                            ),
                          ),
                          Text(
                            'Desa',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 22,
                              fontWeight: FontWeight.w900,
                              color: const Color(0xFF0F172A),
                              letterSpacing: -0.5,
                            ),
                          ),
                        ],
                      ),
                      Text(
                        'Desa Cibuntu, Kec. Cibitung',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: const Color(0xFF64748B),
                        ),
                      ),
                    ],
                  ),
                ],
              ),

              const SizedBox(height: 32),

              // Greeting
              Text(
                'Selamat Datang!',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 24,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF0F172A),
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                'Silakan masuk menggunakan NIK dan kata sandi Anda untuk mengakses layanan mandiri dan administrasi desa.',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 13,
                  height: 1.5,
                  color: const Color(0xFF64748B),
                ),
              ),

              const SizedBox(height: 28),

              // Form NIK
              Text(
                'Nomor Induk Kependudukan (NIK)',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFF1E293B),
                ),
              ),
              const SizedBox(height: 8),
              Container(
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: TextField(
                  controller: _nikController,
                  keyboardType: TextInputType.number,
                  inputFormatters: [
                    FilteringTextInputFormatter.digitsOnly,
                    LengthLimitingTextInputFormatter(18),
                  ],
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    letterSpacing: 1.2,
                    color: const Color(0xFF0F172A),
                  ),
                  decoration: InputDecoration(
                    prefixIcon: const Icon(Icons.badge_outlined, color: AppTheme.gojekGreen, size: 20),
                    suffixIcon: _nikController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear, size: 18, color: Color(0xFF94A3B8)),
                            onPressed: () => setState(() => _nikController.clear()),
                          )
                        : null,
                    hintText: '16 digit NIK sesuai KTP',
                    hintStyle: GoogleFonts.plusJakartaSans(
                      fontSize: 13,
                      fontWeight: FontWeight.w500,
                      letterSpacing: 0,
                      color: const Color(0xFF94A3B8),
                    ),
                    border: InputBorder.none,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  ),
                  onChanged: (_) => setState(() {}),
                ),
              ),

              const SizedBox(height: 18),

              // Form Password
              Text(
                'Kata Sandi',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFF1E293B),
                ),
              ),
              const SizedBox(height: 8),
              Container(
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: TextField(
                  controller: _passController,
                  obscureText: _obscurePassword,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: const Color(0xFF0F172A),
                  ),
                  decoration: InputDecoration(
                    prefixIcon: const Icon(Icons.lock_outline, color: AppTheme.gojekGreen, size: 20),
                    suffixIcon: IconButton(
                      icon: Icon(
                        _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                        size: 20,
                        color: const Color(0xFF94A3B8),
                      ),
                      onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                    ),
                    hintText: 'Masukkan kata sandi',
                    hintStyle: GoogleFonts.plusJakartaSans(
                      fontSize: 13,
                      fontWeight: FontWeight.w500,
                      color: const Color(0xFF94A3B8),
                    ),
                    border: InputBorder.none,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  ),
                ),
              ),

              const SizedBox(height: 24),

              // Primary Login Button
              SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _performLogin,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.gojekGreen,
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                  child: _isLoading
                      ? const SizedBox(
                          width: 22,
                          height: 22,
                          child: CircularProgressIndicator(
                            strokeWidth: 2.5,
                            valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                          ),
                        )
                      : Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              'Masuk ke GoDesa',
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 15,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                            const SizedBox(width: 8),
                            const Icon(Icons.arrow_forward_rounded, size: 18),
                          ],
                        ),
                ),
              ),

              const SizedBox(height: 32),

              // Quick Accounts Section for Demo & Aparatur
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.flash_on_rounded, size: 18, color: Color(0xFFF59E0B)),
                        const SizedBox(width: 6),
                        Text(
                          'Pilih Cepat Akun Demo / Aparatur:',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: const Color(0xFF1E293B),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Tekan salah satu tombol untuk mengisi NIK dan password otomatis:',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 11,
                        color: const Color(0xFF64748B),
                      ),
                    ),
                    const SizedBox(height: 12),
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        _buildQuickChip(
                          icon: '🏛️',
                          title: 'Kades',
                          name: 'H. Abdul Rohim, S.Sos',
                          nik: '3216070000000002',
                          pass: 'kades123',
                          color: const Color(0xFF0F172A),
                        ),
                        _buildQuickChip(
                          icon: '📜',
                          title: 'Sekdes',
                          name: 'H. Muhammad Ridwan',
                          nik: '3216070000000004',
                          pass: 'sekdes123',
                          color: const Color(0xFF0369A1),
                        ),
                        _buildQuickChip(
                          icon: '🏢',
                          title: 'Kasi Pelayanan',
                          name: 'Rahmat Hidayat',
                          nik: '3216070000000003',
                          pass: 'staff123',
                          color: const Color(0xFF0D9488),
                        ),
                        _buildQuickChip(
                          icon: '👥',
                          title: 'Ketua RW 001',
                          name: 'Bpk. H. Warsito',
                          nik: '32160701000000005',
                          pass: 'rw123',
                          color: const Color(0xFFD97706),
                        ),
                        _buildQuickChip(
                          icon: '🏠',
                          title: 'Ketua RT 001',
                          name: 'Bpk. M. Kosasih',
                          nik: '3216070101000006',
                          pass: 'rt123',
                          color: const Color(0xFF4F46E5),
                        ),
                        _buildQuickChip(
                          icon: '👤',
                          title: 'Hermawan (Warga)',
                          name: 'Hermawan (KK)',
                          nik: '3216071405950001',
                          pass: 'warga123',
                          color: AppTheme.gojekGreen,
                        ),
                        _buildQuickChip(
                          icon: '👤',
                          title: 'Budi (Warga)',
                          name: 'Budi Kusuma (KK)',
                          nik: '32160710910003',
                          pass: 'warga123',
                          color: const Color(0xFF059669),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 24),

              // Server Indicator
              Center(
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 8,
                      height: 8,
                      decoration: const BoxDecoration(
                        color: AppTheme.gojekGreen,
                        shape: BoxShape.circle,
                      ),
                    ),
                    const SizedBox(width: 6),
                    Text(
                      'Terkoneksi ke Hosting: ${ApiConfig.baseUrl.replaceAll('https://', '')}',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 10,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF64748B),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildQuickChip({
    required String icon,
    required String title,
    required String name,
    required String nik,
    required String pass,
    required Color color,
  }) {
    return InkWell(
      onTap: () => _handleQuickFill(title: title, nik: nik, pass: pass),
      borderRadius: BorderRadius.circular(10),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: color.withOpacity(0.35)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(icon, style: const TextStyle(fontSize: 13)),
            const SizedBox(width: 5),
            Text(
              title,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 11,
                fontWeight: FontWeight.w700,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
