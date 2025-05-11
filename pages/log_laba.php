<?php
// Set judul halaman
$page_title = "Log Laba Harian";

// Cek apakah user adalah admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Proses hapus log laba
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM log_laba_harian WHERE id_log = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data log laba berhasil dihapus',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location = 'index.php?page=log_laba';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Data log laba gagal dihapus',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
}

// Query untuk mengambil data log laba
$query = "SELECT l.*, u.nama_user,
          CASE 
              WHEN l.id_sumber = 'kas' THEN 'Kas Harian'
              WHEN l.id_sumber = 'modal' THEN 'Modal'
              WHEN l.id_sumber = 'pemasukan' THEN 'Pemasukan Lain'
              WHEN l.id_sumber = 'pengeluaran' THEN 'Pengeluaran'
              ELSE 'Lainnya'
          END as sumber_laba
          FROM log_laba_harian l 
          LEFT JOIN user u ON l.id_user = u.id_user 
          ORDER BY l.tanggal DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Log Laba Harian</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Laba Bersih</th>
                            <th>Sumber</th>
                            <th>Input Oleh</th>
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
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td>Rp <?php echo number_format($row['laba_bersih'], 0, ',', '.'); ?></td>
                            <td><?php echo $row['sumber_laba']; ?></td>
                            <td><?php echo $row['nama_user']; ?></td>
                            <td>
                                <a href="?page=log_laba&hapus=<?php echo $row['id_log']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus log laba ini?')">
                                    <i class="fas fa-trash"></i>
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

<script>
$(document).ready(function() {
    // Inisialisasi DataTables dengan fitur tambahan
    $('.datatable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[1, 'desc']], // Urutkan berdasarkan tanggal terbaru
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});
</script> 