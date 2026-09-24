import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/user_model.dart';
import '../models/surat_model.dart';

class ApiService {
  static final http.Client _client = http.Client();

  // Login
  static Future<Map<String, dynamic>> login(String identifier, String password) async {
    try {
      final res = await _client.post(
        Uri.parse(ApiConfig.login),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'identifier': identifier, 'password': password}),
      );

      final json = jsonDecode(res.body);
      if (res.statusCode == 200 && json['success'] == true) {
        final user = UserModel.fromJson(json['data']['user']);
        return {'success': true, 'user': user, 'token': json['data']['token']};
      } else {
        return {'success': false, 'message': json['message'] ?? 'Login gagal'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Koneksi ke server gagal: $e'};
    }
  }

  // Dashboard Stats
  static Future<Map<String, dynamic>> getDashboardStats() async {
    try {
      final res = await _client.get(Uri.parse(ApiConfig.stats));
      final json = jsonDecode(res.body);
      if (res.statusCode == 200 && json['success'] == true) {
        return {'success': true, 'data': json['data']};
      }
      return {'success': false, 'message': json['message']};
    } catch (e) {
      return {'success': false, 'message': 'Gagal memuat statistik: $e'};
    }
  }

  // Surat List
  static Future<List<SuratModel>> getSuratList({
    required int userId,
    required String role,
    String? rt,
    String? rw,
  }) async {
    try {
      final uri = Uri.parse(ApiConfig.suratList).replace(queryParameters: {
        'user_id': userId.toString(),
        'role': role,
        if (rt != null && rt.isNotEmpty) 'rt': rt,
        if (rw != null && rw.isNotEmpty) 'rw': rw,
      });

      final res = await _client.get(uri);
      final json = jsonDecode(res.body);
      if (res.statusCode == 200 && json['success'] == true) {
        final list = (json['data'] as List)
            .map((item) => SuratModel.fromJson(item))
            .toList();
        return list;
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  // Create Surat
  static Future<Map<String, dynamic>> createSurat({
    required int userId,
    required String jenisSurat,
    required String keperluan,
    required Map<String, dynamic> extra,
  }) async {
    try {
      final res = await _client.post(
        Uri.parse(ApiConfig.suratCreate),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'user_id': userId,
          'jenis_surat': jenisSurat,
          'keperluan': keperluan,
          'data_tambahan': extra,
        }),
      );

      final json = jsonDecode(res.body);
      return {
        'success': json['success'] == true,
        'message': json['message'] ?? 'Respon server',
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung: $e'};
    }
  }

  // Approve Surat
  static Future<Map<String, dynamic>> approveSurat({
    required int suratId,
    required String action,
    required int userId,
    String catatan = '',
    String? rt,
    String? rw,
  }) async {
    try {
      final res = await _client.post(
        Uri.parse(ApiConfig.suratApprove),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'id': suratId,
          'action': action,
          'user_id': userId,
          'catatan': catatan,
          if (rt != null) 'rt': rt,
          if (rw != null) 'rw': rw,
        }),
      );

      final json = jsonDecode(res.body);
      return {
        'success': json['success'] == true,
        'message': json['message'] ?? 'Respon server',
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal verifikasi: $e'};
    }
  }

  // Get Warga List
  static Future<List<UserModel>> getWargaList({
    String? rw,
    String? rt,
    String? query,
  }) async {
    try {
      final uri = Uri.parse(ApiConfig.wargaList).replace(queryParameters: {
        if (rw != null && rw.isNotEmpty) 'rw': rw,
        if (rt != null && rt.isNotEmpty) 'rt': rt,
        if (query != null && query.isNotEmpty) 'q': query,
      });

      final res = await _client.get(uri);
      final json = jsonDecode(res.body);
      if (res.statusCode == 200 && json['success'] == true) {
        final list = (json['data']['warga'] as List)
            .map((item) => UserModel.fromJson(item))
            .toList();
        return list;
      }
      return [];
    } catch (e) {
      return [];
    }
  }
}
