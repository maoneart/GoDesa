import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import 'api_service.dart';

class AuthService extends ChangeNotifier {
  static final AuthService _instance = AuthService._internal();
  factory AuthService() => _instance;
  AuthService._internal();

  UserModel? _currentUser;
  UserModel? get currentUser => _currentUser;
  bool get isLoggedIn => _currentUser != null;

  Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString('user_data');
    if (userJson != null) {
      try {
        _currentUser = UserModel.fromJson(jsonDecode(userJson));
        notifyListeners();
        return;
      } catch (_) {}
    }

    // Default Demo: Hermawan (Warga RT 003 / RW 001)
    _currentUser = UserModel(
      id: 53,
      nik: '3216071405950001',
      noKk: '32160701030001',
      nama: 'Hermawan (Warga)',
      tempatLahir: 'Bekasi',
      tanggalLahir: '1995-05-14',
      jenisKelamin: 'Laki-laki',
      agama: 'Islam',
      statusPerkawinan: 'Kawin',
      pekerjaan: 'Karyawan Swasta / Desainer Grafis',
      kewarganegaraan: 'WNI',
      golonganDarah: 'O',
      email: 'hermawan@gmail.com',
      role: 'warga',
      noHp: '089533377788',
      alamat: 'Kp. Cibuntu RT 003 / RW 001, Desa Cibuntu',
      rt: '003',
      rw: '001',
    );
    notifyListeners();
  }

  Future<bool> login(String identifier, String password) async {
    final res = await ApiService.login(identifier, password);
    if (res['success'] == true && res['user'] != null) {
      _currentUser = res['user'];
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('user_data', jsonEncode(_currentUser!.toJson()));
      notifyListeners();
      return true;
    }
    return false;
  }

  Future<void> switchDemoRole(String role) async {
    if (role == 'kades') {
      _currentUser = UserModel(
        id: 2,
        nik: '3216070000000002',
        noKk: '3216070000000022',
        nama: 'H. Abdul Rohim, S.Sos',
        role: 'kades',
        pekerjaan: 'Kepala Desa Cibuntu',
        rt: '002',
        rw: '001',
        email: 'kades@cibuntu.desa.id',
      );
    } else if (role == 'sekdes') {
      _currentUser = UserModel(
        id: 3,
        nik: '3216070000000004',
        noKk: '3216070000000044',
        nama: 'H. Muhammad Ridwan, S.AP',
        role: 'staff',
        pekerjaan: 'Sekretaris Desa (Sekdes)',
        rt: '001',
        rw: '002',
        email: 'sekdes@cibuntu.desa.id',
      );
    } else if (role == 'staff') {
      _currentUser = UserModel(
        id: 4,
        nik: '3216070000000003',
        noKk: '3216070000000033',
        nama: 'Rahmat Hidayat (Kasi Pelayanan)',
        role: 'staff',
        pekerjaan: 'Perangkat Desa',
        rt: '003',
        rw: '002',
        email: 'staff@cibuntu.desa.id',
      );
    } else if (role == 'rw') {
      _currentUser = UserModel(
        id: 5,
        nik: '32160701000000005',
        noKk: '32160701000000055',
        nama: 'Bpk. H. Warsito (Ketua RW 001)',
        role: 'rw',
        pekerjaan: 'Ketua RW 001',
        rt: '-',
        rw: '001',
        email: 'rw001@cibuntu.desa.id',
      );
    } else if (role == 'rt') {
      _currentUser = UserModel(
        id: 8,
        nik: '3216070103000006',
        noKk: '3216070103000066',
        nama: 'Bpk. Sutisna (Ketua RT 003)',
        role: 'rt',
        pekerjaan: 'Ketua RT 003',
        rt: '003',
        rw: '001',
        email: 'rt003.rw001@cibuntu.desa.id',
      );
    } else {
      _currentUser = UserModel(
        id: 53,
        nik: '3216071405950001',
        noKk: '32160701030001',
        nama: 'Hermawan (Warga)',
        role: 'warga',
        pekerjaan: 'Karyawan Swasta / Desainer Grafis',
        rt: '003',
        rw: '001',
        email: 'hermawan@gmail.com',
      );
    }

    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('user_data', jsonEncode(_currentUser!.toJson()));
    notifyListeners();
  }

  Future<void> logout() async {
    _currentUser = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user_data');
    notifyListeners();
  }
}
