# Liba Web Partner - Firebase ve WordPress API Kurulum Rehberi

Cevaplarınız için teşekkürler! Sizin için en sağlıklı ve uzun ömürlü altyapıyı tasarladım. İşlemleri sıfırdan kuracağımız için WordPress ve Firebase tarafında yapmanız gereken ufak yönergeleri aşağıda derledim.

---

## 1. WordPress ve WooCommerce Entegrasyonu (En Sağlıklı Yöntem)

Mobil uygulamaların WooCommerce ile konuşabilmesi için en güvenli ve hızlı yöntem **JWT (JSON Web Tokens)** kullanmaktır. Böylece müşteriniz sisteme "kullanıcı adı ve şifre" ile giriş yaptığında, uygulama sadece bir "şifreli anahtar (Token)" alır; şifreyi cihazda tutmaz.

### Müşterilerinizin Sitelerine Kurmanız Gerekenler:
1. WordPress admin panelinden **Eklentiler > Yeni Ekle** bölümüne girin.
2. **"JWT Authentication for WP REST API"** eklentisini aratıp kurun ve etkinleştirin (Yazarı: Enrique Chavez).
3. Müşterinizin sunucusundaki (Cpanel/Plesk) `wp-config.php` dosyasına şu iki satırı ekleyin:
   ```php
   define('JWT_AUTH_SECRET_KEY', 'buraya-karmaşık-rastgele-bir-şifre-yazın');
   define('JWT_AUTH_CORS_ENABLE', true);
   ```
4. `.htaccess` dosyasından `HTTP_AUTHORIZATION` başlıklarına izin vermek için [eklentinin dokümantasyonundaki](https://tr.wordpress.org/plugins/jwt-authentication-for-wp-rest-api/) yönlendirici kodlarını (RewriteRule) en üste ekleyin.

*(Uygulamamız, giriş başarılı olduğunda WooCommerce uçlarına `Authorization: Bearer <TOKEN>` göndererek "en sağlıklı" şekilde haberleşecek biçimde kodlanmıştır.)*

---

## 2. Firebase Kurulumu (Bildirimler İçin)

Elimizde hazır bir Firebase projesi olmadığı için, bu kısmı Google üzerinden sıfırdan oluşturmanız gerekiyor. Korkmayın, 2 dakikalık bir işlemdir ve tamamen ücretsizdir!

### Adım Adım Firebase:
1. [Firebase Console](https://console.firebase.google.com)'a Google hesabınızla giriş yapın.
2. **"Add Project (Proje Ekle)"** butonuna tıklayın, adını `Liba Partner` yapın.
3. Google Analytics'i şimdilik kapatabilirsiniz (Daha hızlı oluşturur). Projeyi oluşturun.
4. Proje açıldığında ana ekranda **"Flutter"** simgesine tıklayın (Ortadaki büyük yuvarlak butonlar).
5. Karşınıza Google'ın size sunduğu yönergeler çıkacak. Bilgisayarınızda (Flutter yüklendikten sonra) komut satırına şu komutları yazmanızı isteyecek:
   ```bash
   dart pub global activate flutterfire_cli
   flutterfire configure --project=liba-partner-XXXX
   ```
6. Bu komutu çalıştırdığınızda Firebase, uygulamamızın içine `lib/firebase_options.dart` dosyasını otomatik olarak kuracak ve `android/app` içine `google-services.json` dosyasını kendisi indirecektir!

---

## 3. Randevu ve Özel İşlemler
"Belli değil, eklentilere API özellikleri ekleyerek sonradan düzenleyeceğiz" demiştiniz. Kesinlikle doğru yaklaşım! 

Kod tarafında randevular ve özel istekler için `/wp-json/liba/v1/appointments` gibi kurgusal (mock) bir altyapı hazırladım. Ekibinizle veya eklentilerle API'leri hazır ettiğinizde, yapmamız gereken tek şey URL adresini Flutter projesinde aktif edip "gerçek veriyi çek" demek olacak. Geçiş süreci için tasarımı tamamen esnek bıraktım.
