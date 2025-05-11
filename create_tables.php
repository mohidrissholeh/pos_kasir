<?php
require_once 'config/database.php';

// Buat tabel user
$query = "CREATE TABLE IF NOT EXISTS user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_user VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(32) NOT NULL,
    role ENUM('admin', 'kasir') NOT NULL,
    status_user ENUM('y', 'n') NOT NULL DEFAULT 'y',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL
)";
mysqli_query($koneksi, $query);

// Buat tabel produk
$query = "CREATE TABLE IF NOT EXISTS produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    status_produk ENUM('y', 'n') NOT NULL DEFAULT 'y',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL
)";
mysqli_query($koneksi, $query);

// Buat tabel transaksi
$query = "CREATE TABLE IF NOT EXISTS transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(20) NOT NULL UNIQUE,
    id_user INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metode_pembayaran ENUM('cash', 'qris', 'transfer') NOT NULL,
    jumlah_uang DECIMAL(10,2) NULL,
    status ENUM('selesai', 'batal') NOT NULL DEFAULT 'selesai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user)
)";
mysqli_query($koneksi, $query);

// Buat tabel transaksi_detail
$query = "CREATE TABLE IF NOT EXISTS transaksi_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
)";
mysqli_query($koneksi, $query);

// Buat tabel kategori
$query = "CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT(11) NOT NULL AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_kategori)
)";
mysqli_query($koneksi, $query);

// Tambah kolom gambar dan id_kategori di tabel produk
$query = "ALTER TABLE produk 
          ADD COLUMN gambar VARCHAR(255) DEFAULT NULL AFTER stok,
          ADD COLUMN id_kategori INT(11) DEFAULT NULL AFTER gambar,
          ADD FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE SET NULL";
mysqli_query($koneksi, $query);

// Tambah beberapa kategori default
$query = "INSERT INTO kategori (nama_kategori) VALUES 
          ('Makanan'),
          ('Minuman'),
          ('Snack'),
          ('Dessert')";
mysqli_query($koneksi, $query);

// Buat tabel history_stok
$query = "CREATE TABLE IF NOT EXISTS history_stok (
    id_history INT AUTO_INCREMENT PRIMARY KEY,
    id_produk INT NOT NULL,
    stok_sebelum INT NOT NULL,
    stok_sesudah INT NOT NULL,
    perubahan INT NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk),
    FOREIGN KEY (id_user) REFERENCES user(id_user)
)";
mysqli_query($koneksi, $query);

// Buat tabel diskon
$query = "CREATE TABLE IF NOT EXISTS diskon (
    id_diskon INT AUTO_INCREMENT PRIMARY KEY,
    nama_diskon VARCHAR(100) NOT NULL,
    jenis_diskon ENUM('persentase', 'nominal') NOT NULL,
    nilai_diskon DECIMAL(10,2) NOT NULL,
    tipe_diskon ENUM('manual', 'otomatis') NOT NULL,
    kondisi_diskon ENUM('produk', 'kategori', 'pembayaran', 'total_belanja') NULL,
    nilai_kondisi VARCHAR(255) NULL,
    tanggal_mulai DATE NULL,
    tanggal_selesai DATE NULL,
    status_diskon ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
mysqli_query($koneksi, $query);

// Buat tabel diskon_detail
$query = "CREATE TABLE IF NOT EXISTS diskon_detail (
    id_diskon_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_diskon INT NOT NULL,
    id_produk INT NULL,
    id_kategori INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_diskon) REFERENCES diskon(id_diskon),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk),
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
)";
mysqli_query($koneksi, $query);

// Buat tabel transaksi_diskon
$query = "CREATE TABLE IF NOT EXISTS transaksi_diskon (
    id_transaksi_diskon INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    id_diskon INT NOT NULL,
    nilai_diskon DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_diskon) REFERENCES diskon(id_diskon)
)";
mysqli_query($koneksi, $query);

// Cek apakah sudah ada user admin
$query = "SELECT * FROM user WHERE role = 'admin'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    // Buat user admin default
    $nama_user = 'Administrator';
    $username = 'admin';
    $password = md5('admin123');
    $role = 'admin';
    $status_user = 'y';
    
    $query = "INSERT INTO user (nama_user, username, password, role, status_user, created_at) 
              VALUES ('$nama_user', '$username', '$password', '$role', '$status_user', NOW())";
    mysqli_query($koneksi, $query);
    
    echo "User admin berhasil dibuat!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
}

// Tabel kategori pengeluaran
$query = "CREATE TABLE IF NOT EXISTS kategori_pengeluaran (
    id_kategori_pengeluaran INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
mysqli_query($koneksi, $query);

// Tabel sumber laba
$query = "CREATE TABLE IF NOT EXISTS sumber_laba (
    id_sumber_laba INT PRIMARY KEY AUTO_INCREMENT,
    nama_sumber VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
mysqli_query($koneksi, $query);

// Tabel modal
$query = "CREATE TABLE IF NOT EXISTS modal (
    id_modal INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    deskripsi TEXT NOT NULL,
    jumlah DECIMAL(10,2) NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user)
)";
mysqli_query($koneksi, $query);

// Tabel pemasukan lain
$query = "CREATE TABLE IF NOT EXISTS pemasukan_lain (
    id_pemasukan_lain INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    keterangan TEXT NOT NULL,
    jumlah DECIMAL(10,2) NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user)
)";
mysqli_query($koneksi, $query);

// Tabel pengeluaran
$query = "CREATE TABLE IF NOT EXISTS pengeluaran (
    id_pengeluaran INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    id_kategori INT NOT NULL,
    keterangan TEXT NOT NULL,
    jumlah DECIMAL(10,2) NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori_pengeluaran(id_kategori_pengeluaran),
    FOREIGN KEY (id_user) REFERENCES user(id_user)
)";
mysqli_query($koneksi, $query);

// Tabel kas harian
$query = "CREATE TABLE IF NOT EXISTS kas_harian (
    id_kas INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL UNIQUE,
    kas_awal DECIMAL(10,2) NOT NULL,
    total_penjualan DECIMAL(10,2) NOT NULL DEFAULT 0,
    pemasukan_lain DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_pengeluaran DECIMAL(10,2) NOT NULL DEFAULT 0,
    kas_akhir DECIMAL(10,2) NOT NULL,
    total_laba DECIMAL(10,2) NOT NULL DEFAULT 0,
    catatan TEXT,
    status ENUM('draft', 'selesai') DEFAULT 'draft',
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user)
)";
mysqli_query($koneksi, $query);

// Tabel log laba harian
$query = "CREATE TABLE IF NOT EXISTS log_laba_harian (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    tanggal DATE NOT NULL,
    laba_bersih DECIMAL(10,2) NOT NULL,
    id_sumber INT NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sumber) REFERENCES sumber_laba(id_sumber_laba),
    FOREIGN KEY (id_user) REFERENCES user(id_user)
)";
mysqli_query($koneksi, $query);

// Insert data awal untuk kategori pengeluaran
$kategori_pengeluaran = [
    ['Gaji Karyawan', 'Pembayaran gaji karyawan'],
    ['Listrik', 'Pembayaran tagihan listrik'],
    ['Air', 'Pembayaran tagihan air'],
    ['Internet', 'Pembayaran tagihan internet'],
    ['Belanja Bahan', 'Pembelian bahan baku'],
    ['Sewa Tempat', 'Pembayaran sewa tempat'],
    ['Pajak', 'Pembayaran pajak'],
    ['Lain-lain', 'Pengeluaran lainnya']
];

foreach ($kategori_pengeluaran as $kategori) {
    $query = "INSERT IGNORE INTO kategori_pengeluaran (nama_kategori, deskripsi) 
              VALUES ('$kategori[0]', '$kategori[1]')";
    mysqli_query($koneksi, $query);
}

// Insert data awal untuk sumber laba
$sumber_laba = [
    ['Penjualan Langsung', 'Laba dari penjualan di kasir'],
    ['QRIS', 'Laba dari pembayaran QRIS'],
    ['ShopeeFood', 'Laba dari penjualan di ShopeeFood'],
    ['GoFood', 'Laba dari penjualan di GoFood'],
    ['GrabFood', 'Laba dari penjualan di GrabFood'],
    ['Lain-lain', 'Sumber laba lainnya']
];

foreach ($sumber_laba as $sumber) {
    $query = "INSERT IGNORE INTO sumber_laba (nama_sumber, deskripsi) 
              VALUES ('$sumber[0]', '$sumber[1]')";
    mysqli_query($koneksi, $query);
}

echo "Tabel-tabel berhasil dibuat!";
?> 