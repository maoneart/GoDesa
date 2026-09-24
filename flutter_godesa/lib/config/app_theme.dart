import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // Gojek Signature Colors
  static const Color gojekGreen = Color(0xFF00AA13);
  static const Color gojekDarkGreen = Color(0xFF00880D);
  static const Color gojekLightGreen = Color(0xFFE8F8EE);
  static const Color gojekDark = Color(0xFF1C1C1C);
  static const Color gojekRed = Color(0xFFEE2737);
  static const Color gojekBlue = Color(0xFF00AED6);
  static const Color gojekAmber = Color(0xFFF59E0B);
  static const Color bgLight = Color(0xFFF8FAFC);
  static const Color cardBorder = Color(0xFFE2E8F0);

  static ThemeData lightTheme = ThemeData(
    useMaterial3: true,
    brightness: Brightness.light,
    scaffoldBackgroundColor: bgLight,
    primaryColor: gojekGreen,
    colorScheme: const ColorScheme.light(
      primary: gojekGreen,
      secondary: gojekDarkGreen,
      surface: Colors.white,
      error: gojekRed,
    ),
    textTheme: GoogleFonts.plusJakartaSansTextTheme().copyWith(
      titleLarge: const TextStyle(fontWeight: FontWeight.w800, color: gojekDark),
      titleMedium: const TextStyle(fontWeight: FontWeight.w700, color: gojekDark),
      bodyLarge: const TextStyle(color: gojekDark),
      bodyMedium: const TextStyle(color: Color(0xFF475569)),
    ),
    appBarTheme: AppBarTheme(
      backgroundColor: Colors.white,
      elevation: 0,
      centerTitle: true,
      iconTheme: const IconThemeData(color: gojekDark),
      titleTextStyle: GoogleFonts.plusJakartaSans(
        color: gojekDark,
        fontSize: 16,
        fontWeight: FontWeight.w800,
      ),
    ),
    cardTheme: CardTheme(
      color: Colors.white,
      elevation: 1,
      shadowColor: Colors.black.withOpacity(0.05),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: const BorderSide(color: cardBorder, width: 1),
      ),
    ),
  );
}
