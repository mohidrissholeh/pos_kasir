<?php
// Set judul halaman
$page_title = "Manajemen Pemasukan Lain";

// Include fungsi
require_once 'includes/functions.php';

// Cek apakah user adalah admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Proses tambah pemasukan
if (isset($_POST['tambah'])) {
    $tanggal = $_POST['tanggal'];
    $kategori = $_POST['kategori'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];
    
    $query = "INSERT INTO pemasukan_lain (tanggal, kategori, keterangan, jumlah, id_user) 
              VALUES ('$tanggal', '$kategori', '$keterangan', '$jumlah', '{$_SESSION['user_id']}')";
    
    if (mysqli_query($koneksi, $query)) {
        // Catat log laba
        catatLogLaba($koneksi, $tanggal, $jumlah, 'pemasukan', $_SESSION['user_id']);
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data pemasukan berhasil ditambahkan',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location = 'index.php?page=pemasukan';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Data pemasukan gagal ditambahkan',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
}

// Proses hapus pemasukan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Cek apakah pemasukan sudah digunakan dalam kas harian
    $query = "SELECT COUNT(*) as total FROM kas_harian WHERE pemasukan_lain > 0";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['total'] > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Pemasukan tidak dapat dihapus karena sudah digunakan dalam kas harian',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    } else {
        $query = "DELETE FROM pemasukan_lain WHERE id_pemasukan_lain = '$id'";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data pemasukan berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location = 'index.php?page=pemasukan';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Data pemasukan gagal dihapus',
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>";
        }
    }
}

// Query untuk mengambil data pemasukan
$query = "SELECT p.*, u.nama_user 
          FROM pemasukan_lain p 
          LEFT JOIN user u ON p.id_user = u.id_user 
          ORDER BY p.tanggal DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen Pemasukan Lain</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus"></i> Tambah Pemasukan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
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
                            <td><?php echo $row['kategori']; ?></td>
                            <td><?php echo $row['keterangan']; ?></td>
                            <td>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                            <td><?php echo $row['nama_user']; ?></td>
                            <td>
                                <a href="?page=pemasukan&hapus=<?php echo $row['id_pemasukan_lain']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus pemasukan ini?')">
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Pemasukan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" class="form-control" name="kategori" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div> 