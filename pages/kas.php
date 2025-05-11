<?php
// Set judul halaman
$page_title = "Manajemen Kas Harian";

// Include fungsi
require_once 'includes/functions.php';

// Cek apakah user adalah admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Proses tambah/edit kas harian
if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $kas_awal = $_POST['kas_awal'];
    $total_penjualan = $_POST['total_penjualan'];
    $pemasukan_lain = $_POST['pemasukan_lain'];
    $total_pengeluaran = $_POST['total_pengeluaran'];
    $catatan = $_POST['catatan'];
    $status = $_POST['status'];
    
    // Hitung kas akhir
    $kas_akhir = $kas_awal + $total_penjualan + $pemasukan_lain - $total_pengeluaran;
    
    // Hitung total laba
    $total_laba = $total_penjualan + $pemasukan_lain - $total_pengeluaran;
    
    if (empty($_POST['id_kas'])) {
        // Tambah kas harian baru
        $query = "INSERT INTO kas_harian (tanggal, kas_awal, total_penjualan, pemasukan_lain, 
                                        total_pengeluaran, kas_akhir, total_laba, catatan, status, id_user) 
                  VALUES ('$tanggal', '$kas_awal', '$total_penjualan', '$pemasukan_lain', 
                          '$total_pengeluaran', '$kas_akhir', '$total_laba', '$catatan', '$status', '{$_SESSION['user_id']}')";
    } else {
        // Edit kas harian
        $id_kas = $_POST['id_kas'];
        $query = "UPDATE kas_harian SET 
                  tanggal = '$tanggal',
                  kas_awal = '$kas_awal',
                  total_penjualan = '$total_penjualan',
                  pemasukan_lain = '$pemasukan_lain',
                  total_pengeluaran = '$total_pengeluaran',
                  kas_akhir = '$kas_akhir',
                  total_laba = '$total_laba',
                  catatan = '$catatan',
                  status = '$status'
                  WHERE id_kas = '$id_kas'";
    }
    
    if (mysqli_query($koneksi, $query)) {
        // Jika status selesai, catat log laba
        if ($status === 'selesai') {
            catatLogLaba($koneksi, $tanggal, $total_laba, 'kas', $_SESSION['user_id']);
        }
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data kas harian berhasil disimpan',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location = 'index.php?page=kas';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Data kas harian gagal disimpan',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
}

// Proses hapus kas harian
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM kas_harian WHERE id_kas = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data kas harian berhasil dihapus',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location = 'index.php?page=kas';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Data kas harian gagal dihapus',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
}

// Query untuk mengambil data kas harian
$query = "SELECT k.*, u.nama_user 
          FROM kas_harian k 
          LEFT JOIN user u ON k.id_user = u.id_user 
          ORDER BY k.tanggal DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen Kas Harian</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalForm">
                        <i class="fas fa-plus"></i> Tambah Kas Harian
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kas Awal</th>
                            <th>Total Penjualan</th>
                            <th>Pemasukan Lain</th>
                            <th>Total Pengeluaran</th>
                            <th>Kas Akhir</th>
                            <th>Total Laba</th>
                            <th>Status</th>
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
                            <td>Rp <?php echo number_format($row['kas_awal'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['total_penjualan'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['pemasukan_lain'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['total_pengeluaran'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['kas_akhir'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['total_laba'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($row['status'] === 'selesai'): ?>
                                    <span class="badge badge-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['nama_user']; ?></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" 
                                        onclick="editKas(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?page=kas&hapus=<?php echo $row['id_kas']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus kas harian ini?')">
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

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="formKas">
                <div class="modal-header">
                    <h5 class="modal-title">Form Kas Harian</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kas" id="id_kas">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Kas Awal</label>
                                <input type="number" name="kas_awal" id="kas_awal" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Total Penjualan</label>
                                <input type="number" name="total_penjualan" id="total_penjualan" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Pemasukan Lain</label>
                                <input type="number" name="pemasukan_lain" id="pemasukan_lain" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Pengeluaran</label>
                                <input type="number" name="total_pengeluaran" id="total_pengeluaran" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Kas Akhir</label>
                                <input type="number" name="kas_akhir" id="kas_akhir" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Total Laba</label>
                                <input type="number" name="total_laba" id="total_laba" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="draft">Draft</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fungsi untuk menghitung kas akhir dan total laba
function hitungKas() {
    var kas_awal = parseFloat($('#kas_awal').val()) || 0;
    var total_penjualan = parseFloat($('#total_penjualan').val()) || 0;
    var pemasukan_lain = parseFloat($('#pemasukan_lain').val()) || 0;
    var total_pengeluaran = parseFloat($('#total_pengeluaran').val()) || 0;
    
    var kas_akhir = kas_awal + total_penjualan + pemasukan_lain - total_pengeluaran;
    var total_laba = total_penjualan + pemasukan_lain - total_pengeluaran;
    
    $('#kas_akhir').val(kas_akhir);
    $('#total_laba').val(total_laba);
}

// Event listener untuk input yang mempengaruhi perhitungan
$('#kas_awal, #total_penjualan, #pemasukan_lain, #total_pengeluaran').on('input', hitungKas);

// Fungsi untuk mengisi form edit
function editKas(kas) {
    $('#id_kas').val(kas.id_kas);
    $('#tanggal').val(kas.tanggal);
    $('#kas_awal').val(kas.kas_awal);
    $('#total_penjualan').val(kas.total_penjualan);
    $('#pemasukan_lain').val(kas.pemasukan_lain);
    $('#total_pengeluaran').val(kas.total_pengeluaran);
    $('#kas_akhir').val(kas.kas_akhir);
    $('#total_laba').val(kas.total_laba);
    $('#status').val(kas.status);
    $('#catatan').val(kas.catatan);
    
    $('#modalForm').modal('show');
}
</script> 