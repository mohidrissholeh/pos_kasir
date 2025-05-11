<?php
require_once 'config/database.php';

// Buat tabel user
$query = "CREATE TABLE IF NOT EXISTS user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_user VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(32) NOT NULL,
    role ENUM('kasir', 'spv', 'admin') NOT NULL,
    status_user ENUM('y', 'n') NOT NULL DEFAULT 'y',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($koneksi, $query)) {
    echo "Tabel user berhasil dibuat";
} else {
    echo "Error: " . mysqli_error($koneksi);
}

// Tambah user admin default jika belum ada
$query = "SELECT * FROM user WHERE username = 'admin'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    $password = md5('passadmin');
    $query = "INSERT INTO user (nama_user, username, password, role, status_user) 
              VALUES ('Administrator', 'admin', '$password', 'admin', 'y')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<br>User admin berhasil ditambahkan";
    } else {
        echo "<br>Error: " . mysqli_error($koneksi);
    }
}
?> 