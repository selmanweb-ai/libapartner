import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class DioClient {
  late final Dio dio;
  final _storage = const FlutterSecureStorage();

  DioClient() {
    dio = Dio(
      BaseOptions(
        connectTimeout: const Duration(seconds: 15),
        receiveTimeout: const Duration(seconds: 15),
        responseType: ResponseType.json,
      ),
    );

    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Token'ı güvenli depodan oku
          final token = await _storage.read(key: 'jwt_token');
          
          if (token != null) {
            // WordPress JWT Eklentisi standardı olan Bearer token'ı ekle
            options.headers['Authorization'] = 'Bearer $token';
          }
          
          return handler.next(options);
        },
        onError: (DioException e, handler) async {
          if (e.response?.statusCode == 401 || e.response?.statusCode == 403) {
            // TODO: Token süresi dolmuş veya yetki yok, kullanıcıyı Login'e at
          }
          return handler.next(e);
        },
      ),
    );
  }

  // Dinamik site URL'sini ayarlamak için
  Future<void> setBaseUrl(String url) async {
    await _storage.write(key: 'site_url', value: url);
    dio.options.baseUrl = url;
  }
}

// Riverpod üzerinden global erişim için provider eklenebilir
// final dioProvider = Provider((ref) => DioClient());
