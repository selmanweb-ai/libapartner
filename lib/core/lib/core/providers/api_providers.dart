import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../network/dio_client.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

// 1. Ağ İstemcisi
final dioClientProvider = Provider((ref) => DioClient());

// 2. Dashboard Verileri
final dashboardProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final dioClient = ref.read(dioClientProvider);
  final storage = const FlutterSecureStorage();
  final siteUrl = await storage.read(key: 'site_url');
  
  if (siteUrl == null) throw Exception("Sistem URL'si bulunamadı");

  final response = await dioClient.dio.get('$siteUrl/wp-json/liba/v1/dashboard/summary');
  return Map<String, dynamic>.from(response.data);
});

// 3. Siparişler
final ordersProvider = FutureProvider<List<dynamic>>((ref) async {
  final dioClient = ref.read(dioClientProvider);
  final storage = const FlutterSecureStorage();
  final siteUrl = await storage.read(key: 'site_url');
  
  final response = await dioClient.dio.get('$siteUrl/wp-json/liba/v1/orders');
  return List<dynamic>.from(response.data); 
});

// 4. Ürünler
final productsProvider = FutureProvider<List<dynamic>>((ref) async {
  final dioClient = ref.read(dioClientProvider);
  final storage = const FlutterSecureStorage();
  final siteUrl = await storage.read(key: 'site_url');
  
  final response = await dioClient.dio.get('$siteUrl/wp-json/liba/v1/products');
  return List<dynamic>.from(response.data);
});

// 5. Randevular / Formlar
final appointmentsProvider = FutureProvider<List<dynamic>>((ref) async {
  final dioClient = ref.read(dioClientProvider);
  final storage = const FlutterSecureStorage();
  final siteUrl = await storage.read(key: 'site_url');
  
  final response = await dioClient.dio.get('$siteUrl/wp-json/liba/v1/appointments');
  return List<dynamic>.from(response.data);
});
