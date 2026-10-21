# SPBE Kabupaten Malang

Sistem Informasi Manajemen SPBE (Sistem Pemerintahan Berbasis Elektronik) untuk Kabupaten Malang.

## Tentang Aplikasi

Aplikasi SPBE Kabupaten Malang adalah sistem informasi yang dikembangkan untuk mendukung implementasi Sistem Pemerintahan Berbasis Elektronik di lingkungan Pemerintah Kabupaten Malang. Aplikasi ini memfasilitasi pengelolaan data layanan publik, monitoring progres SPBE, serta manajemen dokumen regulasi dan pengetahuan.

## Fitur Utama

### Manajemen Layanan

-   Pendataan layanan publik dari setiap Organisasi Perangkat Daerah (OPD)
-   Tracking status dan progres layanan
-   Export data layanan ke format Excel
-   Sistem approval untuk validasi data

### Dashboard & Monitoring

-   Visualisasi data statistik SPBE
-   Widget progres implementasi
-   Monitoring real-time status layanan
-   Laporan komprehensif

### Manajemen Dokumen

-   Repository dokumen regulasi SPBE
-   Knowledge base pengetahuan
-   Manajemen dokumen paparan
-   Standard Operating Procedures (SOP)

### Sistem Multi-Role

-   Admin: Akses penuh ke semua fitur sistem
-   OPD: Manajemen data untuk OPD masing-masing
-   Workflow approval untuk user baru

## Teknologi yang Digunakan

-   **Backend**: Laravel 12 dengan PHP 8.2+
-   **Admin Panel**: Filament 3.3
-   **Frontend**: Alpine.js dan Tailwind CSS 4
-   **Database**: MySQL (Production), SQLite (Development)
-   **Build Tool**: Vite 7

## Keamanan

Aplikasi ini dilengkapi dengan fitur keamanan standar industri:

-   SSL/TLS encryption
-   Rate limiting untuk mencegah abuse
-   Security headers (CSP, X-Frame-Options)
-   Input sanitization dan XSS protection
-   User approval system
-   Policy-based authorization
-   CSRF protection

## Performance

-   Circuit Breaker untuk auto-fallback ke cache
-   Smart caching system (10x faster performance)
-   Database connection pooling
-   Query result caching
-   Lazy loading untuk optimasi gambar

## Monitoring & Logging

-   API health monitoring real-time
-   Enhanced logging dengan 5 custom channels
-   Performance monitoring untuk request lambat
-   Automated database backup harian
-   Audit trail untuk aktivitas user

## Dokumentasi

Dokumentasi lengkap tersedia di folder `documentation/`:

1. **01_PANDUAN_UTAMA.md** - Panduan instalasi dan setup
2. **02_REFERENSI_CEPAT.md** - Command shortcuts untuk developer
3. **03_PANDUAN_TESTING.md** - Panduan testing aplikasi
4. **04_PANDUAN_DEPLOYMENT.md** - Panduan deployment production
5. **05_PANDUAN_KONTRIBUSI.md** - Panduan untuk kontributor
6. **06_KEBIJAKAN_KEAMANAN.md** - Kebijakan keamanan sistem
7. **07_SETUP_RECAPTCHA_SMTP.txt** - Setup reCAPTCHA dan SMTP

## Tim Pengembang

Aplikasi ini dikembangkan oleh:
**Dinas Komunikasi dan Informatika Kabupaten Malang**

## Lisensi

MIT License

## Kontak

Untuk pertanyaan, saran, atau dukungan teknis, silakan hubungi:

-   Email: diskominfo@malangkab.go.id
-   Website: https://diskominfo.malangkab.go.id

---

**Dikembangkan dengan ❤️ untuk Kabupaten Malang**
