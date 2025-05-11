<?php
// Set judul halaman
$page_title = "Dashboard";

// Ambil data untuk ringkasan
$query = "SELECT 
    COUNT(*) as total_transaksi,
    SUM(total) as total_penjualan,
    COUNT(DISTINCT id_user) as total_kasir
    FROM transaksi 
    WHERE DATE(created_at) = CURDATE()";
$result = mysqli_query($koneksi, $query);
$summary = mysqli_fetch_assoc($result);

// Ambil data penjualan 7 hari terakhir
$query = "SELECT 
    DATE(created_at) as tanggal,
    COUNT(*) as jumlah_transaksi,
    SUM(total) as total_penjualan
    FROM transaksi 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY tanggal ASC";
$result = mysqli_query($koneksi, $query);
$penjualan_harian = [];
while ($row = mysqli_fetch_assoc($result)) {
    $penjualan_harian[] = $row;
}

// Ambil data produk terlaris
$query = "SELECT 
    p.nama_produk,
    SUM(td.jumlah) as total_terjual
    FROM transaksi_detail td
    JOIN produk p ON td.id_produk = p.id_produk
    WHERE td.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY td.id_produk
    ORDER BY total_terjual DESC
    LIMIT 5";
$result = mysqli_query($koneksi, $query);
$produk_terlaris = [];
while ($row = mysqli_fetch_assoc($result)) {
    $produk_terlaris[] = $row;
}

// Ambil data stok menipis
$query = "SELECT * FROM produk WHERE stok <= 5 AND status_produk = 'y' ORDER BY stok ASC";
$result = mysqli_query($koneksi, $query);
$stok_menipis = [];
while ($row = mysqli_fetch_assoc($result)) {
    $stok_menipis[] = $row;
}
?>

<!-- Ringkasan -->
<div class="row">
    <div class="col-md-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo number_format($summary['total_transaksi']); ?></h3>
                <p>Transaksi Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp <?php echo number_format($summary['total_penjualan'], 0, ',', '.'); ?></h3>
                <p>Penjualan Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo number_format($summary['total_kasir']); ?></h3>
                <p>Kasir Aktif Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

<!-- Grafik dan Tabel -->
<div class="row">
    <!-- Grafik Penjualan -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Penjualan 7 Hari Terakhir</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Produk Terlaris -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Produk Terlaris</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produk_terlaris as $produk): ?>
                            <tr>
                                <td><?php echo $produk['nama_produk']; ?></td>
                                <td><?php echo number_format($produk['total_terjual']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stok Menipis -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Stok Menipis</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Stok</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stok_menipis as $produk): ?>
                            <tr>
                                <td><?php echo $produk['nama_produk']; ?></td>
                                <td><?php echo $produk['stok']; ?></td>
                                <td>
                                    <?php if ($produk['stok'] == 0): ?>
                                        <span class="badge badge-danger">Habis</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Menipis</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Data untuk grafik
const salesData = <?php echo json_encode($penjualan_harian); ?>;

// Siapkan data untuk grafik
const labels = salesData.map(item => {
    const date = new Date(item.tanggal);
    return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' });
});
const transactionData = salesData.map(item => item.jumlah_transaksi);
const salesAmountData = salesData.map(item => item.total_penjualan);

// Buat grafik
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Transaksi',
            data: transactionData,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1,
            yAxisID: 'y'
        }, {
            label: 'Total Penjualan',
            data: salesAmountData,
            borderColor: 'rgb(255, 99, 132)',
            tension: 0.1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Jumlah Transaksi'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Total Penjualan (Rp)'
                },
                grid: {
                    drawOnChartArea: false
                }
            }
        }
    }
});
</script> 