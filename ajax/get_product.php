<?php
require_once 'config/database.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $query = "SELECT * FROM produk WHERE id_produk = '$id' AND status_produk = 'y'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Produk tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'ID produk tidak ditemukan']);
}
?> 