import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ApiConfig {
  // Hosting Resmi maoneart.my.id
  static const String productionUrl = 'https://maoneart.my.id/godesa/api';
  // Local Termux Development
  static const String localUrl = 'http://127.0.0.1:8085/GoDesa/api';
  static const String lanUrl = 'http://192.168.1.100:8085/GoDesa/api';

  static String _activeBaseUrl = productionUrl;

  static String get baseUrl => _activeBaseUrl;

  static Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    _activeBaseUrl = prefs.getString('custom_api_url') ?? productionUrl;
  }

  static Future<void> setBaseUrl(String url) async {
    _activeBaseUrl = url.trim().replaceAll(RegExp(r'/+$'), '');
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('custom_api_url', _activeBaseUrl);
  }

  // Endpoints
  static String get login => '$_activeBaseUrl/auth/login.php';
  static String get profile => '$_activeBaseUrl/auth/profile.php';
  static String get stats => '$_activeBaseUrl/dashboard/stats.php';
  static String get suratList => '$_activeBaseUrl/surat/list.php';
  static String get suratCreate => '$_activeBaseUrl/surat/create.php';
  static String get suratApprove => '$_activeBaseUrl/surat/approve.php';
  static String get wargaList => '$_activeBaseUrl/warga/list.php';
}
