<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$id_produk = $_POST['id_produk'];

// Cek stok
$query = "SELECT stok FROM produk WHERE id_produk = '$id_produk'";
$result = mysqli_query($koneksi, $query);
$product = mysqli_fetch_assoc($result);

if ($product['stok'] > 0) {
    if (isset($_SESSION['cart'][$id_produk])) {
        $_SESSION['cart'][$id_produk]++;
    } else {
        $_SESSION['cart'][$id_produk] = 1;
    }
    
    // Update stok
    mysqli_query($koneksi, "UPDATE produk SET stok = stok - 1 WHERE id_produk = '$id_produk'");
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Stok habis']);
}
?> 