# POS Kasir

Sistem Point of Sale (POS) berbasis web untuk manajemen kasir toko/restoran.

## Fitur

- Manajemen produk dan kategori
- Manajemen stok
- Sistem diskon (persentase dan nominal)
- Multiple metode pembayaran (Tunai, QRIS, Transfer)
- Cetak struk
- Laporan penjualan
- Manajemen pengguna
- Responsive design (mobile & desktop)

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx)
- Browser modern (Chrome, Firefox, Safari, Edge)

## Instalasi

1. Clone repository ini:
```bash
git clone https://github.com/username/pos_kasir.git
```

2. Import database:
- Buka phpMyAdmin atau MySQL client
- Import file `database/pos_kasir.sql`

3. Konfigurasi database:
- Buka file `config/database.php`
- Sesuaikan konfigurasi database:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'username');
define('DB_PASS', 'password');
define('DB_NAME', 'pos_kasir');
```

4. Konfigurasi web server:
- Pastikan web server mengarah ke folder project
- Pastikan folder `assets` dan `uploads` memiliki permission write

5. Akses aplikasi:
- Buka browser
- Akses `http://localhost/pos_kasir`
- Login dengan kredensial default:
  - Username: admin
  - Password: admin123

## Struktur Folder

```
pos_kasir/
├── ajax/              # File AJAX handler
├── assets/            # File statis (CSS, JS, images)
├── config/            # File konfigurasi
├── database/          # File database
├── pages/             # Halaman aplikasi
├── template/          # Template header & footer
├── uploads/           # Folder upload gambar
└── index.php          # File utama
```

## Kontribusi

1. Fork repository ini
2. Buat branch baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).

## Kontak

Jika ada pertanyaan atau saran, silakan buat issue di repository ini. 