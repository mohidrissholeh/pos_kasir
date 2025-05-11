<?php
// Set judul halaman
$page_title = "Manajemen Modal";

// Include fungsi
require_once 'includes/functions.php';

// Cek apakah user adalah admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: /pos_kasir/");
    exit();
}

// Proses tambah/edit modal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];
    $jumlah = $_POST['jumlah'];
    $status = $_POST['status'];
    
    if (empty($_POST['id_modal'])) {
        // Tambah modal baru
        $query = "INSERT INTO modal (tanggal, deskripsi, jumlah, status, id_user) 
                  VALUES ('$tanggal', '$deskripsi', '$jumlah', '$status', '{$_SESSION['user_id']}')";
        
        if (mysqli_query($koneksi, $query)) {
            // Catat log laba
            catatLogLaba($koneksi, $tanggal, $jumlah, 'modal', $_SESSION['user_id']);
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data modal berhasil ditambahkan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location = '/pos_kasir/modal';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Data modal gagal ditambahkan',
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>";
        }
    } else {
        // Edit modal
        $id_modal = $_POST['id_modal'];
        $query = "UPDATE modal SET 
                  tanggal = '$tanggal',
                  deskripsi = '$deskripsi',
                  jumlah = '$jumlah',
                  status = '$status'
                  WHERE id_modal = '$id_modal'";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data modal berhasil disimpan!'); window.location.href = '/pos_kasir/modal';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data modal!'); window.location.href = '/pos_kasir/modal';</script>";
        }
    }
}

// Proses hapus modal
if (isset($_GET['delete'])) {
    $id_modal = $_GET['delete'];
    
    // Cek apakah modal sudah digunakan dalam kas harian
    $query = "SELECT COUNT(*) as total FROM kas_harian WHERE kas_awal > 0";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['total'] > 0) {
        echo "<script>alert('Modal tidak dapat dihapus karena sudah digunakan dalam kas harian!'); window.location.href = '/pos_kasir/modal';</script>";
        exit();
    }
    
    $query = "DELETE FROM modal WHERE id_modal = '$id_modal'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Modal berhasil dihapus!'); window.location.href = '/pos_kasir/modal';</script>";
    } else {
        echo "<script>alert('Gagal menghapus modal!'); window.location.href = '/pos_kasir/modal';</script>";
    }
}

// Query untuk mengambil data modal
$query = "SELECT m.*, u.nama_user 
          FROM modal m 
          LEFT JOIN user u ON m.id_user = u.id_user 
          ORDER BY m.tanggal DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen Modal</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalForm">
                        <i class="fas fa-plus"></i> Tambah Modal
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
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
                                <td><?php echo $row['deskripsi']; ?></td>
                                <td>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($row['status'] === 'aktif'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['nama_user']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" 
                                            onclick="editModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deleteModal(<?php echo $row['id_modal']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Form Modal</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_modal" id="id_modal">
                    
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editModal(modal) {
    $('#id_modal').val(modal.id_modal);
    $('#tanggal').val(modal.tanggal);
    $('#deskripsi').val(modal.deskripsi);
    $('#jumlah').val(modal.jumlah);
    $('#status').val(modal.status);
    
    $('#modalForm').modal('show');
}

function deleteModal(id) {
    if (confirm('Apakah Anda yakin ingin menghapus modal ini?')) {
        window.location.href = '/pos_kasir/modal?delete=' + id;
    }
}
</script> 