# WooCommerce için MorPOS

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-9.0%2B-purple.svg)](https://woocommerce.com/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-GPLv3-blue.svg)](LICENSE)

**WooCommerce için MorPOS**, **Morpara MorPOS** ödeme sistemini WooCommerce mağazalarıyla entegre eden güvenli ve kullanımı kolay bir ödeme geçidi eklentisidir. Müşteriler siparişlerini tamamlarken güvenli **Hosted Payment Page (HPP)** akışıyla yönlendirilir.

![MorPOS Payment Gateway](assets/img/morpos-logo-small.png)

## ✨ Özellikler

- 🛒 **WooCommerce Entegrasyonu**: MorPOS'u sorunsuz şekilde ödeme yöntemi olarak ekler
- 🔒 **Güvenli Ödemeler**: Maksimum güvenlik için Hosted Payment Page (HPP)
- 🏗️ **WooCommerce Blocks Desteği**: Yeni Sepet ve Ödeme blokları ile uyumlu
- 🌍 **Çoklu Para Birimi**: TRY, USD, EUR para birimlerini destekler
- 💳 **Çoklu Ödeme Seçenekleri**: Kredi kartı, banka kartı ve taksitli ödemeler
- 🧪 **Sandbox Modu**: Geliştirme için test ortamı
- 🔧 **Kolay Yapılandırma**: Basit yönetici paneli kurulumu
- 🛡️ **Güvenlik Özellikleri**: TLS 1.2+ gereksinimi, imzalı API iletişimi

## 📋 Gereksinimler

### Sunucu Gereksinimleri

| Bileşen | Minimum | Önerilen |
|---------|---------|----------|
| **WordPress** | 6.0 | 6.8+ |
| **WooCommerce** | 9.0 | 10.0+ |
| **PHP** | 7.4 | 8.2+ |
| **TLS** | 1.2 | 1.3 |

### PHP Uzantıları

- `cURL` - API iletişimi için gerekli
- `json` - Veri işleme için gerekli
- `hash` - Güvenlik imzaları için gerekli
- `openssl` - Güvenli bağlantılar için gerekli

### WordPress Özellikleri

- **WooCommerce Eklentisi**: Kurulu ve aktif olmalıdır
- **Pretty Permalinks**: Ödeme geri çağrıları için gerekli
- **SSL Sertifikası**: Üretim ortamları için önerilen

## 🚀 Kurulum

### Yöntem 1: WordPress.org Deposu (Önerilen)

1. **WordPress Yöneticisinden**
   - WordPress yönetici paneline gidin → **Eklentiler** → **Yeni Ekle**
   - "MorPOS for WooCommerce" araması yapın
   - **Şimdi Yükle** → **Etkinleştir**'e tıklayın

2. **WP-CLI ile**
   ```bash
   wp plugin install morpos-gateway --activate
   ```

### Yöntem 2: Manuel Yükleme

1. Eklenti ZIP dosyasını [WordPress.org](https://wordpress.org/plugins/morpos-gateway/)'dan indirin
2. WordPress yönetici paneline gidin → **Eklentiler** → **Yeni Ekle**
3. **Eklenti Yükle** → **Dosya Seç**'e tıklayın
4. ZIP dosyasını seçin ve **Şimdi Yükle**'ye tıklayın
5. **Eklentiyi Etkinleştir**'e tıklayın

### Yöntem 3: Manuel Kurulum (Geliştiriciler)

1. **Eklentiyi İndirin**
   ```bash
   git clone https://github.com/morpara/morpos-woocommerce.git
   ```

2. **WordPress'e Yükleyin**
   ```bash
   cp -r morpos-woocommerce/ /path/to/wordpress/wp-content/plugins/morpos-gateway/
   ```

3. **Eklentiyi Etkinleştirin**
   - WordPress yönetici paneline gidin → **Eklentiler**
   - **MorPOS for WooCommerce**'i bulun
   - **Etkinleştir**'e tıklayın

## ⚙️ Yapılandırma

### 1. Temel Kurulum

**WooCommerce** → **Ayarlar** → **Ödemeler** → **MorPOS** sayfasına gidin

<div align="center">
  <img src="docs/images/morpos-settings-edit.png" alt="MorPOS Settings" width="600">
</div>

### 2. Gerekli Ayarlar

Aşağıdaki zorunlu alanları doldurun:

| Alan | Açıklama | Örnek |
|------|----------|-------|
| **Merchant ID** | Benzersiz bayi kimliğiniz | `12345` |
| **Client ID** | OAuth istemci kimliği | `your_client_id` |
| **Client Secret** | OAuth istemci şifresi | `your_client_secret` |
| **API Key** | API istekleri için kimlik doğrulama anahtarı | `your_api_key` |

### 3. Ortam Ayarları

- **Test Modu**: Geliştirme/test için etkinleştirin
  - Sandbox uç noktalarını kullanır
  - Gerçek işlem yapılmaz
  - Test kart numaraları kabul edilir

- **Form Türü**: Ödeme arayüzünü seçin
  - `Hosted`: MorPOS ödeme sayfasına yönlendirme (önerilen)
  - `Embedded`: Sitenizde ödeme formu

### 4. Bağlantı Testi

Kimlik bilgilerini girdikten sonra:
1. **Bağlantıyı Test Et** düğmesine tıklayın
2. Yeşil onay işaretinin görünmesini doğrulayın
3. Sistem gereksinimlerinin durumunu kontrol edin

## 🛠️ Geliştirme ve Hata Ayıklama

### Loglama

`wp-config.php` dosyasına ekleyerek hata ayıklama loglamasını etkinleştirin:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Loglar `/wp-content/debug.log` dosyasına yazılacaktır

## 🔍 Sorun Giderme

### Yaygın Sorunlar

#### "Ödeme başlatılırken bir hata oluştu." Hatası

Bu genellikle yapılandırma sorunlarını belirtir:

1. **Kimlik Bilgilerini Kontrol Edin**: Tüm API kimlik bilgilerinin doğru olduğunu doğrulayın
2. **Bağlantı Testi**: Bağlantı testi özelliğini kullanın
3. **Gereksinimleri Kontrol Edin**: Sunucunun minimum gereksinimleri karşıladığından emin olun
4. **SSL/TLS**: TLS 1.2+ desteklendiğini doğrulayın
5. **Zaman Senkronizasyonu**: Sunucu saatinin doğru olduğundan emin olun

#### Ödeme İşlenmiyor

1. **Para Birimi Desteği**: Para biriminin desteklendiğinden emin olun (TRY, USD, EUR)
2. **Tutar Limitleri**: Minimum/maksimum işlem limitlerini kontrol edin
3. **Ağ**: Giden HTTPS bağlantılarına izin verildiğini doğrulayın
4. **Loglar**: API hataları için WordPress hata ayıklama loglarını kontrol edin

#### Ödeme Sayfası Sorunları

1. **Permalinkler**: Pretty permalink'lerin etkin olduğundan emin olun
2. **WooCommerce**: WooCommerce'in düzgün yapılandırıldığını doğrulayın
3. **Çakışmalar**: Test etmek için diğer eklentileri geçici olarak devre dışı bırakın
4. **Tema**: Varsayılan WordPress teması ile test edin

### Sistem Gereksinimleri Kontrolü

Eklenti, ayarlar sayfasından erişilebilen yerleşik bir sistem gereksinimleri kontrolörü içerir. Şunları doğrular:

- ✅ PHP sürüm uyumluluğu
- ✅ TLS sürüm desteği
- ✅ Gerekli PHP uzantıları
- ✅ WordPress sürümü
- ✅ WooCommerce sürümü

### Hata Ayıklama Modu

Detaylı loglama etkinleştirin:

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Logları kontrol edin: wp-content/debug.log
```

## 🌐 Uluslararasılaştırma

Eklenti birden fazla dili destekler:

- **Türkçe (tr_TR)**: Yerel destek
- **İngilizce (en_US)**: Varsayılan dil

### Çeviri Katkısı

**Ön koşullar**: WP-CLI sisteminizde kurulu olmalıdır.

#### WP-CLI Kurulumu

WP-CLI kurulu değilse, şu adımları izleyin:

```bash
# WP-CLI'yi indirin
curl -O https://raw.githubusercontent.com/wp-cli/wp-cli/v2.8.1/utils/wp-completion.bash

# Çalıştırılabilir yapın
chmod +x wp-cli.phar

# Global konuma taşıyın
sudo mv wp-cli.phar /usr/local/bin/wp

# Kurulumu doğrulayın
wp --info
```

Detaylı kurulum talimatları için: [WP-CLI Kurulum Kılavuzu](https://wp-cli.org/#installing)

#### Çeviri Dosyalarının Oluşturulması

1. POT dosyası oluşturun:
   ```bash
   wp i18n make-pot . languages/morpos.pot --domain=morpos --include=includes,assets,views
   ```

2. JSON çeviri dosyaları oluşturun:
   ```bash
   wp i18n make-json languages --no-purge
   ```

3. Çevirileri GitHub pull request'leri aracılığıyla gönderin

## 🤝 Katkıda Bulunma

Katkılarınızı bekliyoruz! Başlamak için:

### Geliştirme Kurulumu

1. **Repository'yi Fork Edin**
   ```bash
   git clone https://github.com/YOUR_USERNAME/morpos-woocommerce.git
   cd morpos-woocommerce
   ```

2. **Yerel WordPress Kurun**
   - WordPress'i yerel olarak kurun
   - WooCommerce'i kurun ve etkinleştirin
   - Eklentiyi `wp-content/plugins/morpos-gateway/` dizinine kopyalayın

3. **Değişiklik Yapın**
   - WordPress kodlama standartlarını takip edin
   - Uygun dokümantasyon ekleyin
   - Farklı WordPress/WooCommerce sürümleriyle test edin

4. **Pull Request Gönderin**
   - Feature branch oluşturun: `git checkout -b feature/your-feature`
   - Değişiklikleri commit edin: `git commit -m "Add your feature"`
   - Branch'i push edin: `git push origin feature/your-feature`
   - GitHub'da pull request açın

### Kodlama Standartları

- [WordPress Kodlama Standartları](https://developer.wordpress.org/coding-standards/)'nı takip edin
- Anlamlı değişken isimleri ve yorumlar kullanın
- Desteklenen WordPress/WooCommerce sürümleriyle uyumluluğu test edin
- Fonksiyonlar ve sınıflar için PHPDoc yorumları ekleyin

## 📄 Lisans

Bu proje **GPLv3** Lisansı altında lisanslanmıştır - detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 🆘 Destek

- **Dokümantasyon**: Bu README ve kod içi yorumları kontrol edin
- **Sorunlar**: [GitHub Issues](https://github.com/morpara/morpos-woocommerce/issues)
- **Topluluk**: [WordPress Destek Forumu](https://wordpress.org/support/plugin/morpos-gateway/)
- **İletişim**: [Morpara Destek](https://morpara.com/support)

## 🙏 Teşekkürler

- **WooCommerce Ekibi** - Mükemmel e-ticaret platformu için
- **WordPress Topluluğu** - Sağlam CMS temeli için
- **Morpara** - Güvenli ödeme altyapısı için

---

**❤️ ile [Morpara](https://morpara.com/) tarafından yapılmıştır**

MorPOS ödeme çözümleri hakkında daha fazla bilgi için [morpara.com](https://morpara.com/)'u ziyaret edin.