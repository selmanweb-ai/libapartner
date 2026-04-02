# Liba Web Partner Uygulaması - APK Derleme Rehberi

Projenin tüm ana yapısı (Dashboard, Login, Siparişler, Ürünler, Animasyonlar) kodlanmış durumdadır. Ancak bilgisayarınızda şu an **Flutter SDK yüklü olmadığı için** doğrudan `flutter build apk` komutunu sistem çalıştıramadı. 

Uygulamanızı paketlemek ve bir Android `.apk` çıktısı alabilmek için aşağıdaki adımları bilgisayarınızda bir kez gerçekleştirmeniz gerekmektedir:

## 1. Flutter SDK'yı Bilgisayarınıza Kurun
- [Flutter Resmi Kurulum Dokümanına](https://docs.flutter.dev/get-started/install/windows) gidin.
- Flutter SDK ZIP dosyasını indirin ve örneğin `C:\src\flutter` şeklinde dizine çıkartın.
- Flutter klasörünün içindeki `bin` yolunu Windows Ortam Değişkenlerinde (Environment Variables) **Path** bölümüne ekleyin.
- Bilgisayarınızda `Android Studio` kurulu olmalıdır, kurulu değilse onu da indirin ve kurarken "Android SDK Command-line Tools" ve "Android SDK" araçlarının seçili olduğundan emin olun.

## 2. Proje Dosyalarını Yapılandırma
Komut istemini (CMD veya PowerShell) açın ve şu an bulunduğumuz klasöre gidin:
```bash
cd C:\Users\selma\OneDrive\Masaüstü\LibawebPartner
```

Ardından eksik platform dosyalarını (android, ios klasörlerini) canlandırmak için projeyi aynı klasörde şu komutla tetikleyin:
```bash
flutter create --org com.libaweb --project-name liba_partner .
```
Bu komut, yazdığımız Flutter modüllerini (`lib/` klasörü) bozmadan eksik dosyaları (`android/` vs.) tamamlayacaktır.

## 3. Bağımlılıkları İndirme
```bash
flutter pub get
```
Yazmış olduğumuz Modern UI (Glassmorphism), Animasyon (flutter_animate) ve Riverpod modülleri indirilecektir.

## 4. Test (Opsiyonel)
Telefonunuzu kablo ile bilgisayarınıza bağlayın (USB Hata Ayıklama modunu açın) veya bir emülatör başlatın. Ardından:
```bash
flutter run
```
komutu ile uygulamayı canlı olarak cihazınızda test edebilirsiniz. Tasarım kalitesini ve Apple/Stripe tarzı pürüzsüz geçişleri görebilirsiniz.

## 5. APK Çıktısı Alma (Release Build)
Her şey tamamsa ve test edilebilir versiyonu müşterilerinize gönderecekseniz, şu komutu çalıştırın:
```bash
flutter build apk --release
```

Bu işlem birkaç dakika sürebilir. İşlem tamamlandıktan sonra APK dosyanız şu konumda bulunacaktır:
`build\app\outputs\flutter-apk\app-release.apk`

Bu `.apk` dosyasını Android cihazlara kurabilir ve test edebilirsiniz.
