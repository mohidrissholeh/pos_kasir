<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$output = '';
$total = 0;

foreach ($_SESSION['cart'] as $id_produk => $qty) {
    $query = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
    $result = mysqli_query($koneksi, $query);
    $product = mysqli_fetch_assoc($result);
    
    $subtotal = $product['harga'] * $qty;
    $total += $subtotal;
    
    $output .= '
    <div class="cart-item">
        <div class="row">
            <div class="col-6">
                <h6>'.$product['nama_produk'].'</h6>
                <p class="text-muted">Rp '.number_format($product['harga'], 0, ',', '.').'</p>
            </div>
            <div class="col-4">
                <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary btn-qty" data-action="decrease" data-id="'.$id_produk.'">-</button>
                    <input type="text" class="form-control text-center" value="'.$qty.'" readonly>
                    <button class="btn btn-outline-secondary btn-qty" data-action="increase" data-id="'.$id_produk.'">+</button>
                </div>
            </div>
            <div class="col-2 text-right">
                <button class="btn btn-sm btn-danger btn-remove" data-id="'.$id_produk.'">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-right">
                <small>Subtotal: Rp '.number_format($subtotal, 0, ',', '.').'</small>
            </div>
        </div>
    </div>';
}

if (empty($_SESSION['cart'])) {
    $output = '<div class="text-center text-muted">Keranjang kosong</div>';
}

echo $output;
?> 