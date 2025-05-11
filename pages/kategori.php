<?php
// Set judul halaman
$page_title = "Manajemen Kategori";

// Cek apakah user adalah admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: /pos_kasir/");
    exit();
}

// Proses tambah/edit kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = $_POST['nama_kategori'];
    
    if (empty($_POST['id_kategori'])) {
        // Tambah kategori baru
        $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
    } else {
        // Edit kategori
        $id_kategori = $_POST['id_kategori'];
        $query = "UPDATE kategori SET nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_kategori'";
    }
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data kategori berhasil disimpan!'); window.location.href = '/pos_kasir/kategori';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data kategori: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Proses hapus kategori
if (isset($_GET['delete'])) {
    $id_kategori = $_GET['delete'];
    $query = "DELETE FROM kategori WHERE id_kategori = '$id_kategori'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Kategori berhasil dihapus!'); window.location.href = '/pos_kasir/kategori';</script>";
    } else {
        echo "<script>alert('Gagal menghapus kategori: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Query untuk mengambil data kategori
$query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result = mysqli_query($koneksi, $query);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen Kategori</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#kategoriModal">
                        <i class="fas fa-plus"></i> Tambah Kategori
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Dibuat</th>
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
                                <td><?php echo $row['nama_kategori']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" 
                                            onclick="editKategori(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deleteKategori(<?php echo $row['id_kategori']; ?>)">
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

<!-- Modal Kategori -->
<div class="modal fade" id="kategoriModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Form Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kategori" id="id_kategori">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required>
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
function editKategori(kategori) {
    $('#id_kategori').val(kategori.id_kategori);
    $('#nama_kategori').val(kategori.nama_kategori);
    $('#kategoriModal').modal('show');
}

function deleteKategori(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
        window.location.href = '/pos_kasir/kategori?delete=' + id;
    }
}
</script> 