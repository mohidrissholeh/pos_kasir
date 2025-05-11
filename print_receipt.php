<?php
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    die('ID transaksi tidak ditemukan');
}

$transaction_id = $_GET['id'];

// Ambil data transaksi
$query = "SELECT t.*, u.nama_user 
          FROM transaksi t 
          JOIN user u ON t.id_user = u.id_user 
          WHERE t.id_transaksi = '$transaction_id'";
$result = mysqli_query($koneksi, $query);
$transaction = mysqli_fetch_assoc($result);

if (!$transaction) {
    die('Transaksi tidak ditemukan');
}

// Ambil detail transaksi
$query = "SELECT dt.*, p.nama_produk FROM transaksi_detail dt JOIN produk p ON dt.id_produk = p.id_produk WHERE dt.id_transaksi = '$transaction_id'";
$result = mysqli_query($koneksi, $query);

// Ambil data diskon
$query = "SELECT td.*, d.nama_diskon, d.jenis_diskon 
          FROM transaksi_diskon td 
          JOIN diskon d ON td.id_diskon = d.id_diskon 
          WHERE td.id_transaksi = '$transaction_id'";
$result_diskon = mysqli_query($koneksi, $query);
$diskon = mysqli_fetch_assoc($result_diskon);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Struk #<?php echo $transaction['nomor_transaksi']; ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 300px;
            margin: 0 auto;
            padding: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .border-top {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="text-center mb-2">
        <h3 style="margin: 0;">NAMA TOKO</h3>
        <p style="margin: 0;">Alamat Toko</p>
        <p style="margin: 0;">Telp: 08123456789</p>
    </div>

    <div class="mb-2">
        <p style="margin: 0;">No: <?php echo $transaction['nomor_transaksi']; ?></p>
        <p style="margin: 0;">Tanggal: <?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></p>
        <p style="margin: 0;">Kasir: <?php echo $transaction['nama_user']; ?></p>
    </div>

    <div class="border-top">
        <?php while ($item = mysqli_fetch_assoc($result)): ?>
        <div class="mb-1">
            <div style="display: flex; justify-content: space-between;">
                <span><?php echo $item['nama_produk']; ?></span>
                <span><?php echo $item['jumlah']; ?> x <?php echo number_format($item['harga'], 0, ',', '.'); ?></span>
            </div>
            <div style="text-align: right;">
                Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="border-top">
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>Subtotal:</span>
            <span>Rp <?php echo number_format($transaction['total'] + ($diskon ? $diskon['nilai_diskon'] : 0), 0, ',', '.'); ?></span>
        </div>
        
        <?php if ($diskon): ?>
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>Diskon (<?php echo $diskon['nama_diskon']; ?>):</span>
            <span>- Rp <?php echo number_format($diskon['nilai_diskon'], 0, ',', '.'); ?></span>
        </div>
        <?php endif; ?>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>Total:</span>
            <span>Rp <?php echo number_format($transaction['total'], 0, ',', '.'); ?></span>
        </div>
        
        <?php if ($transaction['metode_pembayaran'] === 'cash'): ?>
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>Tunai:</span>
            <span>Rp <?php echo number_format($transaction['jumlah_uang'], 0, ',', '.'); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span>Kembali:</span>
            <span>Rp <?php echo number_format($transaction['jumlah_uang'] - $transaction['total'], 0, ',', '.'); ?></span>
        </div>
        <?php else: ?>
        <div style="display: flex; justify-content: space-between;">
            <span>Metode:</span>
            <span><?php echo strtoupper($transaction['metode_pembayaran']); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-2">
        <p style="margin: 0;">Terima kasih atas kunjungan Anda</p>
        <p style="margin: 0;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
    </div>

    <div class="text-center mt-2 no-print">
        <button onclick="window.print()">Cetak Struk</button>
    </div>

    <script>
        // Cetak otomatis saat halaman dimuat
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>