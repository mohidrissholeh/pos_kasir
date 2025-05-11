<?php
// Filter tanggal
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Query untuk mengambil data transaksi
$query = "SELECT t.*, u.nama_user 
          FROM transaksi t 
          JOIN user u ON t.id_user = u.id_user 
          WHERE DATE(t.created_at) BETWEEN '$start_date' AND '$end_date'
          ORDER BY t.created_at DESC";
$result = mysqli_query($koneksi, $query);

// Hitung total penjualan
$query_total = "SELECT 
                COUNT(*) as total_transaksi,
                SUM(total) as total_penjualan,
                SUM(CASE WHEN metode_pembayaran = 'cash' THEN total ELSE 0 END) as total_cash,
                SUM(CASE WHEN metode_pembayaran = 'qris' THEN total ELSE 0 END) as total_qris,
                SUM(CASE WHEN metode_pembayaran = 'transfer' THEN total ELSE 0 END) as total_transfer
                FROM transaksi 
                WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$result_total = mysqli_query($koneksi, $query_total);
$total = mysqli_fetch_assoc($result_total);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laporan Penjualan</h3>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Ringkasan -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo number_format($total['total_transaksi']); ?></h3>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>Rp <?php echo number_format($total['total_penjualan'], 0, ',', '.'); ?></h3>
                                <p>Total Penjualan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>Rp <?php echo number_format($total['total_cash'], 0, ',', '.'); ?></h3>
                                <p>Total Tunai</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>Rp <?php echo number_format($total['total_qris'] + $total['total_transfer'], 0, ',', '.'); ?></h3>
                                <p>Total Non-Tunai</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Transaksi -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No Transaksi</th>
                                <th>Kasir</th>
                                <th>Metode</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td><?php echo $row['nomor_transaksi']; ?></td>
                                <td><?php echo $row['nama_user']; ?></td>
                                <td><?php echo ucfirst($row['metode_pembayaran']); ?></td>
                                <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="print_receipt.php?id=<?php echo $row['id_transaksi']; ?>" 
                                       class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 