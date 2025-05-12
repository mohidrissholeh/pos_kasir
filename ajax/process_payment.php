<?php
session_start();
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek login
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Log data yang diterima
error_log("Data POST: " . print_r($_POST, true));

// Terima data dari AJAX
$items = $_POST['items'];
$payment_method = $_POST['payment_method'];
$cash_amount = isset($_POST['cash_amount']) ? $_POST['cash_amount'] : 0;
$diskon = isset($_POST['diskon']) ? $_POST['diskon'] : null;

// Validasi data
if (empty($items) || !is_array($items)) {
    die(json_encode(['status' => 'error', 'message' => 'Data item tidak valid']));
}

// Hitung total
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Kurangi dengan diskon jika ada
if ($diskon) {
    $total -= $diskon['nilai'];
}

// Generate nomor transaksi
$nomor_transaksi = 'TRX' . date('YmdHis') . rand(1000, 9999);

// Mulai transaksi
mysqli_begin_transaction($koneksi);

try {
    // 1. Insert ke tabel transaksi
    $query = "INSERT INTO transaksi (nomor_transaksi, id_user, total, metode_pembayaran, jumlah_uang, status) 
              VALUES (?, ?, ?, ?, ?, 'selesai')";
    $stmt = mysqli_prepare($koneksi, $query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($koneksi));
    }
    
    mysqli_stmt_bind_param($stmt, "sidsd", $nomor_transaksi, $_SESSION['user_id'], $total, $payment_method, $cash_amount);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
    }
    
    $transaction_id = mysqli_insert_id($koneksi);
    error_log("Transaction ID: " . $transaction_id);
    
    // 2. Insert detail transaksi dan update stok
    foreach ($items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        
        // Insert detail transaksi
        $query = "INSERT INTO transaksi_detail (id_transaksi, id_produk, jumlah, harga, subtotal) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        if (!$stmt) {
            throw new Exception("Error preparing detail statement: " . mysqli_error($koneksi));
        }
        
        mysqli_stmt_bind_param($stmt, "iiidd", $transaction_id, $item['id'], $item['quantity'], $item['price'], $subtotal);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing detail statement: " . mysqli_stmt_error($stmt));
        }
        
        // Update stok produk
        $query = "UPDATE produk SET stok = stok - ? WHERE id_produk = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        if (!$stmt) {
            throw new Exception("Error preparing stock update statement: " . mysqli_error($koneksi));
        }
        
        mysqli_stmt_bind_param($stmt, "ii", $item['quantity'], $item['id']);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing stock update: " . mysqli_stmt_error($stmt));
        }
    }
    
    // 3. Simpan data diskon jika ada
    if ($diskon) {
        $query = "INSERT INTO transaksi_diskon (id_transaksi, id_diskon, nilai_diskon) 
                  VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        if (!$stmt) {
            throw new Exception("Error preparing diskon statement: " . mysqli_error($koneksi));
        }
        
        mysqli_stmt_bind_param($stmt, "iid", $transaction_id, $diskon['id'], $diskon['nilai']);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing diskon statement: " . mysqli_stmt_error($stmt));
        }
    }
    
    // Commit transaksi
    mysqli_commit($koneksi);
    
    // Kirim response sukses
    echo json_encode([
        'status' => 'success',
        'message' => 'Pembayaran berhasil',
        'transaction_id' => $transaction_id,
        'nomor_transaksi' => $nomor_transaksi
    ]);
    
} catch (Exception $e) {
    // Rollback jika terjadi error
    mysqli_rollback($koneksi);
    
    // Log error
    error_log("Payment Error: " . $e->getMessage());
    
    // Kirim response error
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?> 