<?php
// Set judul halaman
$page_title = "Manajemen Produk";

// Cek apakah user adalah admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: /pos_kasir/");
    exit();
}

// Proses tambah/edit produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $id_kategori = $_POST['id_kategori'];
    $status_produk = $_POST['status_produk'];
    $keterangan = $_POST['keterangan'] ?? 'Update stok';
    
    // Upload gambar
    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Cek apakah file adalah gambar
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;
            }
        }
    }
    
    if (empty($_POST['id_produk'])) {
        // Tambah produk baru
        $query = "INSERT INTO produk (nama_produk, harga, stok, gambar, id_kategori, status_produk, created_at) 
                  VALUES ('$nama_produk', '$harga', '$stok', '$gambar', '$id_kategori', '$status_produk', NOW())";
        
        if (mysqli_query($koneksi, $query)) {
            $id_produk = mysqli_insert_id($koneksi);
            // Catat history stok awal
            $query = "INSERT INTO history_stok (id_produk, stok_sebelum, stok_sesudah, perubahan, keterangan, id_user) 
                      VALUES ('$id_produk', 0, '$stok', '$stok', 'Stok awal', '{$_SESSION['user_id']}')";
            mysqli_query($koneksi, $query);
            
            echo "<script>alert('Data produk berhasil disimpan!'); window.location.href = '/pos_kasir/produk';</script>";
        }
    } else {
        // Edit produk
        $id_produk = $_POST['id_produk'];
        
        // Ambil stok sebelum update
        $query = "SELECT stok FROM produk WHERE id_produk = '$id_produk'";
        $result = mysqli_query($koneksi, $query);
        $old_stok = mysqli_fetch_assoc($result)['stok'];
        
        $gambar_query = $gambar ? ", gambar = '$gambar'" : "";
        $query = "UPDATE produk SET 
                  nama_produk = '$nama_produk',
                  harga = '$harga',
                  stok = '$stok',
                  id_kategori = '$id_kategori',
                  status_produk = '$status_produk'
                  $gambar_query
                  WHERE id_produk = '$id_produk'";
        
        if (mysqli_query($koneksi, $query)) {
            // Catat history perubahan stok
            $perubahan = $stok - $old_stok;
            if ($perubahan != 0) {
                $query = "INSERT INTO history_stok (id_produk, stok_sebelum, stok_sesudah, perubahan, keterangan, id_user) 
                          VALUES ('$id_produk', '$old_stok', '$stok', '$perubahan', '$keterangan', '{$_SESSION['user_id']}')";
                mysqli_query($koneksi, $query);
            }
            
            echo "<script>alert('Data produk berhasil disimpan!'); window.location.href = '/pos_kasir/produk';</script>";
        }
    }
}

// Proses hapus produk
if (isset($_GET['delete'])) {
    $id_produk = $_GET['delete'];
    
    // Hapus gambar jika ada
    $query = "SELECT gambar FROM produk WHERE id_produk = '$id_produk'";
    $result = mysqli_query($koneksi, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['gambar']) && file_exists($row['gambar'])) {
            unlink($row['gambar']);
        }
    }
    
    $query = "DELETE FROM produk WHERE id_produk = '$id_produk'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location.href = '/pos_kasir/produk';</script>";
    } else {
        echo "<script>alert('Gagal menghapus produk: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Query untuk mengambil data produk
$query = "SELECT p.*, k.nama_kategori 
          FROM produk p 
          LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
          ORDER BY p.nama_produk ASC";
$result = mysqli_query($koneksi, $query);

// Query untuk mengambil data kategori
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen Produk</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#produkModal">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
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
                                <td>
                                    <?php if (!empty($row['gambar'])): ?>
                                        <img src="<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>" 
                                             style="max-width: 50px; max-height: 50px;">
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td><?php echo $row['nama_kategori'] ?? '-'; ?></td>
                                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $row['stok']; ?></td>
                                <td>
                                    <?php if ($row['status_produk'] == 'y'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" 
                                            onclick="editProduk(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deleteProduk(<?php echo $row['id_produk']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            onclick="showHistory(<?php echo $row['id_produk']; ?>)">
                                        <i class="fas fa-history"></i>
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

<!-- Modal Produk -->
<div class="modal fade" id="produkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Form Produk</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_produk" id="id_produk">
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" id="nama_produk" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" id="id_kategori" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                                <option value="<?php echo $kategori['id_kategori']; ?>">
                                    <?php echo $kategori['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" id="harga" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" id="stok" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Gambar</label>
                        <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status_produk" id="status_produk" class="form-control" required>
                            <option value="y">Aktif</option>
                            <option value="n">Tidak Aktif</option>
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

<!-- Modal History Stok -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History Stok</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Stok Sebelum</th>
                                <th>Perubahan</th>
                                <th>Stok Sesudah</th>
                                <th>Keterangan</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editProduk(produk) {
    $('#id_produk').val(produk.id_produk);
    $('#nama_produk').val(produk.nama_produk);
    $('#id_kategori').val(produk.id_kategori);
    $('#harga').val(produk.harga);
    $('#stok').val(produk.stok);
    $('#status_produk').val(produk.status_produk);
    $('#produkModal').modal('show');
}

function deleteProduk(id) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        window.location.href = '/pos_kasir/produk?delete=' + id;
    }
}

function showHistory(id_produk) {
    // Ambil data history stok
    $.ajax({
        url: '/pos_kasir/ajax/get_history_stok.php',
        type: 'GET',
        data: { id_produk: id_produk },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                let html = '';
                
                data.forEach(item => {
                    const perubahan = item.perubahan > 0 ? 
                        `<span class="text-success">+${item.perubahan}</span>` : 
                        `<span class="text-danger">${item.perubahan}</span>`;
                    
                    html += `
                        <tr>
                            <td>${item.created_at}</td>
                            <td>${item.stok_sebelum}</td>
                            <td>${perubahan}</td>
                            <td>${item.stok_sesudah}</td>
                            <td>${item.keterangan}</td>
                            <td>${item.nama_user}</td>
                        </tr>
                    `;
                });
                
                $('#historyTableBody').html(html);
                $('#historyModal').modal('show');
            } catch (e) {
                console.error('Error parsing response:', e);
                alert('Terjadi kesalahan saat mengambil data history');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data history');
        }
    });
}
</script> 