<?php
session_start();
require_once 'config/database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    die('Unauthorized');
}

$id_diskon = $_GET['id_diskon'];
$tipe = $_GET['tipe'];

if ($tipe === 'produk') {
    $query = "SELECT id_produk FROM diskon_detail WHERE id_diskon = '$id_diskon' AND id_produk IS NOT NULL";
} else if ($tipe === 'kategori') {
    $query = "SELECT id_kategori FROM diskon_detail WHERE id_diskon = '$id_diskon' AND id_kategori IS NOT NULL";
}

$result = mysqli_query($koneksi, $query);
$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    if ($tipe === 'produk') {
        $data[] = $row['id_produk'];
    } else if ($tipe === 'kategori') {
        $data[] = $row['id_kategori'];
    }
}

echo json_encode($data); 