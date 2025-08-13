# 🏫 Sistem Pembayaran SPP (School Tuition Payment System)

Aplikasi web berbasis PHP untuk mengelola pembayaran SPP sekolah dengan fitur lengkap dan antarmuka modern.

## 📋 Deskripsi

Sistem Pembayaran SPP adalah aplikasi web yang dirancang khusus untuk membantu sekolah dalam mengelola pembayaran SPP siswa. Aplikasi ini menyediakan dashboard admin yang komprehensif untuk mengelola data siswa, mencatat pembayaran, dan menghasilkan laporan.

## ✨ Fitur Utama

### 🔐 Keamanan & Autentikasi
- Login admin dengan enkripsi password
- Perlindungan CSRF (Cross-Site Request Forgery)
- Manajemen sesi dengan timeout otomatis
- Validasi input dan sanitasi data
- Konfigurasi keamanan Apache (.htaccess)

### 👥 Manajemen Siswa
- Tambah, edit, dan hapus data siswa
- Pencarian dan filter siswa
- Paginasi untuk performa optimal
- Export data ke Excel
- Validasi data real-time
- Generate NIS otomatis

### 💰 Manajemen Pembayaran
- Pencatatan pembayaran SPP
- Filter berdasarkan status, bulan, dan siswa
- Statistik pembayaran real-time
- Status pembayaran (Lunas, Pending, Terlambat)
- Metode pembayaran (Tunai, Transfer, Online)

### 📊 Dashboard & Laporan
- Dashboard dengan statistik lengkap
- Grafik status pembayaran
- Aktivitas terbaru sistem
- Ringkasan pendapatan bulanan
- Export dan print laporan

### 🎨 Antarmuka Modern
- Desain responsif dengan Bootstrap 5
- Tema modern dengan Google Fonts
- Navigasi yang intuitif
- Animasi dan transisi halus
- Kompatibel dengan semua perangkat

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8.x
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework CSS**: Bootstrap 5.3
- **Library JS**: jQuery 3.7
- **Font**: Google Fonts (Inter)
- **Server**: Apache dengan mod_rewrite

## 📦 Instalasi

### Persyaratan Sistem
- PHP 8.0 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.3+
- Apache Web Server dengan mod_rewrite
- Ekstensi PHP: PDO, PDO_MySQL, mbstring, openssl

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   git clone [repository-url]
   # atau download dan extract file ZIP
   ```

2. **Setup Database**
   ```sql
   -- Buat database baru
   CREATE DATABASE spp_db;
   
   -- Import schema database
   mysql -u root -p spp_db < db_setup.sql
   ```

3. **Konfigurasi Database**
   Edit file `config.php` dan sesuaikan pengaturan database:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'spp_db');
   ```

4. **Set Permissions**
   ```bash
   chmod 755 spp/
   chmod 644 spp/*.php
   chmod 600 spp/config.php
   ```

5. **Akses Aplikasi**
   Buka browser dan akses: `http://localhost/spp/`

## 🚀 Penggunaan

### Login Admin
- **Username**: `admin`
- **Password**: `admin123`

### Menu Utama
1. **Dashboard** - Statistik dan ringkasan sistem
2. **Data Siswa** - Kelola informasi siswa
3. **Pembayaran** - Kelola transaksi pembayaran
4. **Laporan** - Generate dan export laporan

### Workflow Umum
1. Login sebagai admin
2. Tambah data siswa baru
3. Input pembayaran SPP
4. Monitor status pembayaran
5. Generate laporan bulanan

## 📁 Struktur File

```
spp/
├── 📄 index.php              # Halaman login
├── ⚙️ config.php             # Konfigurasi aplikasi
├── 🗄️ db.php                 # Koneksi database
├── 📊 db_setup.sql           # Schema database
├── 🔒 .htaccess              # Konfigurasi keamanan
├── 📖 README.md              # Dokumentasi
├── 📋 TODO.md                # Progress tracker
│
├── 👨‍💼 admin/                   # Halaman admin
│   ├── dashboard.php         # Dashboard utama
│   ├── students.php          # Daftar siswa
│   ├── student_add.php       # Tambah siswa
│   ├── student_edit.php      # Edit siswa
│   └── payments.php          # Daftar pembayaran
│
├── 🎨 assets/                 # Asset statis
│   ├── css/
│   │   └── style.css         # Styling custom
│   └── js/
│       └── main.js           # JavaScript utama
│
├── 🧩 include/                # Komponen UI
│   ├── header.php            # Header template
│   └── footer.php            # Footer template
│
├── 🔧 lib/                    # Library dan fungsi
│   └── common.php            # Fungsi umum
│
└── ⚡ process/                # Script pemrosesan
    ├── login_process.php     # Proses login
    ├── logout.php            # Proses logout
    └── add_student.php       # Proses tambah siswa
```

## 🗄️ Struktur Database

### Tabel `admin`
- `id` - Primary key
- `username` - Username admin
- `password` - Password terenkripsi
- `full_name` - Nama lengkap
- `email` - Email admin
- `created_at` - Tanggal dibuat

### Tabel `students`
- `id` - Primary key
- `student_id` - NIS siswa (unique)
- `name` - Nama lengkap siswa
- `class` - Kelas siswa
- `phone` - Nomor telepon
- `email` - Email siswa
- `parent_name` - Nama orang tua
- `parent_phone` - Telepon orang tua
- `address` - Alamat lengkap
- `monthly_fee` - Biaya SPP bulanan
- `status` - Status siswa (Active/Inactive)
- `created_at` - Tanggal dibuat
- `updated_at` - Tanggal diupdate

### Tabel `payments`
- `id` - Primary key
- `student_id` - Foreign key ke tabel students
- `amount` - Jumlah pembayaran
- `payment_date` - Tanggal pembayaran
- `payment_month` - Bulan pembayaran (YYYY-MM)
- `status` - Status (Paid/Pending/Overdue)
- `payment_method` - Metode (Cash/Transfer/Online)
- `notes` - Catatan tambahan
- `created_by` - Admin yang input
- `created_at` - Tanggal dibuat
- `updated_at` - Tanggal diupdate

## 🔧 Konfigurasi

### Pengaturan Aplikasi
File `config.php` berisi berbagai pengaturan:
- Konfigurasi database
- Pengaturan sesi
- Format mata uang dan tanggal
- Konfigurasi keamanan
- Pengaturan pagination

### Keamanan
- Password di-hash menggunakan PHP `password_hash()`
- Proteksi CSRF dengan token
- Validasi dan sanitasi input
- Session timeout otomatis
- Header keamanan HTTP
- Proteksi file sensitif via .htaccess

## 🎨 Kustomisasi

### Mengubah Tema
Edit file `assets/css/style.css` untuk mengubah:
- Warna tema (CSS variables di `:root`)
- Font dan tipografi
- Layout dan spacing
- Animasi dan transisi

### Menambah Fitur
1. Buat file PHP baru di folder `admin/`
2. Tambahkan fungsi di `lib/common.php`
3. Update navigasi di `include/header.php`
4. Tambahkan styling di `assets/css/style.css`

## 🐛 Troubleshooting

### Masalah Umum

**1. Error Database Connection**
- Periksa konfigurasi di `config.php`
- Pastikan MySQL service berjalan
- Cek username dan password database

**2. Session Expired**
- Periksa pengaturan `session.gc_maxlifetime` di PHP
- Sesuaikan `SESSION_TIMEOUT` di config.php

**3. Permission Denied**
- Set permission yang benar untuk file dan folder
- Pastikan Apache memiliki akses read/write

**4. CSS/JS Tidak Load**
- Periksa path file di `include/header.php`
- Pastikan mod_rewrite aktif di Apache
- Clear browser cache

### Debug Mode
Untuk development, aktifkan error reporting di `config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📈 Pengembangan Lanjutan

### Fitur yang Bisa Ditambahkan
- [ ] Multi-user admin dengan role
- [ ] Portal orang tua/siswa
- [ ] Notifikasi email/SMS
- [ ] Payment gateway integration
- [ ] Backup otomatis database
- [ ] API untuk mobile app
- [ ] Multi-bahasa support
- [ ] Advanced reporting
- [ ] Bulk import/export
- [ ] Audit trail lengkap

### Optimasi Performa
- Implementasi caching (Redis/Memcached)
- Database indexing optimization
- CDN untuk asset statis
- Lazy loading untuk tabel besar
- Compression untuk response

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository ini
2. Buat branch fitur baru
3. Commit perubahan Anda
4. Push ke branch
5. Buat Pull Request

## 📄 Lisensi

Project ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lengkap.

## 👨‍💻 Developer

Dikembangkan dengan ❤️ untuk sistem manajemen sekolah yang lebih baik.

## 📞 Support

Jika Anda membutuhkan bantuan atau memiliki pertanyaan:
- Buat issue di repository
- Email: support@example.com
- Dokumentasi: [Wiki Page]

---

**Versi**: 1.0.0  
**Terakhir Update**: <?php echo date('d F Y'); ?>  
**Status**: ✅ Production Ready

> 💡 **Tips**: Selalu backup database sebelum melakukan update sistem!
