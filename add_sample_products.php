<?php
require_once 'config/database.php';

// Data produk contoh
$products = [
    [
        'nama_produk' => 'Kopi Hitam',
        'harga' => 5000,
        'stok' => 100
    ],
    [
        'nama_produk' => 'Kopi Susu',
        'harga' => 7000,
        'stok' => 100
    ],
    [
        'nama_produk' => 'Teh Tarik',
        'harga' => 8000,
        'stok' => 100
    ],
    [
        'nama_produk' => 'Nasi Goreng',
        'harga' => 15000,
        'stok' => 50
    ],
    [
        'nama_produk' => 'Mie Goreng',
        'harga' => 15000,
        'stok' => 50
    ],
    [
        'nama_produk' => 'Ayam Goreng',
        'harga' => 12000,
        'stok' => 50
    ],
    [
        'nama_produk' => 'Kentang Goreng',
        'harga' => 8000,
        'stok' => 50
    ],
    [
        'nama_produk' => 'Es Teh',
        'harga' => 3000,
        'stok' => 100
    ],
    [
        'nama_produk' => 'Es Jeruk',
        'harga' => 4000,
        'stok' => 100
    ],
    [
        'nama_produk' => 'Air Mineral',
        'harga' => 3000,
        'stok' => 100
    ]
];

// Cek apakah tabel produk kosong
$query = "SELECT COUNT(*) as total FROM produk";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);

if ($row['total'] == 0) {
    // Tambahkan produk contoh
    foreach ($products as $product) {
        $nama_produk = $product['nama_produk'];
        $harga = $product['harga'];
        $stok = $product['stok'];
        
        $query = "INSERT INTO produk (nama_produk, harga, stok, status_produk, created_at) 
                  VALUES ('$nama_produk', '$harga', '$stok', 'y', NOW())";
        mysqli_query($koneksi, $query);
    }
    
    echo "Produk contoh berhasil ditambahkan!";
} else {
    echo "Tabel produk sudah berisi data!";
}
?> 