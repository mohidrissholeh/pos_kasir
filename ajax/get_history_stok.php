<?php
session_start();
require_once '../config/database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Cek parameter
if (!isset($_GET['id_produk'])) {
    die(json_encode(['status' => 'error', 'message' => 'ID Produk tidak valid']));
}

$id_produk = $_GET['id_produk'];

// Ambil data history stok
$query = "SELECT h.*, u.nama_user, 
          DATE_FORMAT(h.created_at, '%d/%m/%Y %H:%i') as created_at 
          FROM history_stok h 
          JOIN user u ON h.id_user = u.id_user 
          WHERE h.id_produk = ? 
          ORDER BY h.created_at DESC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?> 