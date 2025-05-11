<?php
// Set judul halaman
$page_title = "Manajemen Diskon";

// Cek apakah user adalah admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: /pos_kasir/");
    exit();
}

// Proses tambah/edit diskon
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_diskon = $_POST['nama_diskon'];
    $jenis_diskon = $_POST['jenis_diskon'];
    $nilai_diskon = $_POST['nilai_diskon'];
    $tipe_diskon = $_POST['tipe_diskon'];
    $kondisi_diskon = $_POST['kondisi_diskon'];
    $nilai_kondisi = $_POST['nilai_kondisi'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_diskon = $_POST['status_diskon'];
    
    if (empty($_POST['id_diskon'])) {
        // Tambah diskon baru
        $query = "INSERT INTO diskon (nama_diskon, jenis_diskon, nilai_diskon, tipe_diskon, kondisi_diskon, nilai_kondisi, tanggal_mulai, tanggal_selesai, status_diskon) 
                  VALUES ('$nama_diskon', '$jenis_diskon', '$nilai_diskon', '$tipe_diskon', '$kondisi_diskon', '$nilai_kondisi', '$tanggal_mulai', '$tanggal_selesai', '$status_diskon')";
        
        if (mysqli_query($koneksi, $query)) {
            $id_diskon = mysqli_insert_id($koneksi);
            
            // Simpan detail diskon jika ada produk atau kategori yang dipilih
            if ($kondisi_diskon === 'produk' && !empty($_POST['produk'])) {
                foreach ($_POST['produk'] as $id_produk) {
                    $query = "INSERT INTO diskon_detail (id_diskon, id_produk) VALUES ('$id_diskon', '$id_produk')";
                    mysqli_query($koneksi, $query);
                }
            } elseif ($kondisi_diskon === 'kategori' && !empty($_POST['kategori'])) {
                foreach ($_POST['kategori'] as $id_kategori) {
                    $query = "INSERT INTO diskon_detail (id_diskon, id_kategori) VALUES ('$id_diskon', '$id_kategori')";
                    mysqli_query($koneksi, $query);
                }
            }
            
            echo "<script>alert('Data diskon berhasil disimpan!'); window.location.href = '/pos_kasir/diskon';</script>";
        }
    } else {
        // Edit diskon
        $id_diskon = $_POST['id_diskon'];
        $query = "UPDATE diskon SET 
                  nama_diskon = '$nama_diskon',
                  jenis_diskon = '$jenis_diskon',
                  nilai_diskon = '$nilai_diskon',
                  tipe_diskon = '$tipe_diskon',
                  kondisi_diskon = '$kondisi_diskon',
                  nilai_kondisi = '$nilai_kondisi',
                  tanggal_mulai = '$tanggal_mulai',
                  tanggal_selesai = '$tanggal_selesai',
                  status_diskon = '$status_diskon'
                  WHERE id_diskon = '$id_diskon'";
        
        if (mysqli_query($koneksi, $query)) {
            // Hapus detail diskon lama
            $query = "DELETE FROM diskon_detail WHERE id_diskon = '$id_diskon'";
            mysqli_query($koneksi, $query);
            
            // Simpan detail diskon baru
            if ($kondisi_diskon === 'produk' && !empty($_POST['produk'])) {
                foreach ($_POST['produk'] as $id_produk) {
                    $query = "INSERT INTO diskon_detail (id_diskon, id_produk) VALUES ('$id_diskon', '$id_produk')";
                    mysqli_query($koneksi, $query);
                }
            } elseif ($kondisi_diskon === 'kategori' && !empty($_POST['kategori'])) {
                foreach ($_POST['kategori'] as $id_kategori) {
                    $query = "INSERT INTO diskon_detail (id_diskon, id_kategori) VALUES ('$id_diskon', '$id_kategori')";
                    mysqli_query($koneksi, $query);
                }
            }
            
            echo "<script>alert('Data diskon berhasil disimpan!'); window.location.href = '/pos_kasir/diskon';</script>";
        }
    }
}

// Proses hapus diskon
if (isset($_GET['delete'])) {
    $id_diskon = $_GET['delete'];
    
    // Cek apakah diskon sudah digunakan dalam transaksi
    $query = "SELECT COUNT(*) as total FROM transaksi_diskon WHERE id_diskon = '$id_diskon'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['total'] > 0) {
        echo "<script>alert('Diskon tidak dapat dihapus karena sudah digunakan dalam transaksi!'); window.location.href = '/pos_kasir/diskon';</script>";
        exit();
    }
    
    // Hapus detail diskon terlebih dahulu
    $query = "DELETE FROM diskon_detail WHERE id_diskon = '$id_diskon'";
    mysqli_query($koneksi, $query);
    
    // Hapus diskon
    $query = "DELETE FROM diskon WHERE id_diskon = '$id_diskon'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Diskon berhasil dihapus!'); window.location.href = '/pos_kasir/diskon';</script>";
    } else {
        echo "<script>alert('Gagal menghapus diskon!'); window.location.href = '/pos_kasir/diskon';</script>";
    }
}

// Query untuk mengambil data diskon
$query = "SELECT * FROM diskon ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);

// Query untuk mengambil data produk
$query_produk = "SELECT * FROM produk WHERE status_produk = 'y' ORDER BY nama_produk ASC";
$result_produk = mysqli_query($koneksi, $query_produk);

// Query untuk mengambil data kategori
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen Diskon</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#diskonModal">
                        <i class="fas fa-plus"></i> Tambah Diskon
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Diskon</th>
                                <th>Jenis</th>
                                <th>Nilai</th>
                                <th>Tipe</th>
                                <th>Kondisi</th>
                                <th>Periode</th>
                                <th>Status</th>
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
                                <td><?php echo $row['nama_diskon']; ?></td>
                                <td><?php echo ucfirst($row['jenis_diskon']); ?></td>
                                <td>
                                    <?php 
                                    if ($row['jenis_diskon'] === 'persentase') {
                                        echo $row['nilai_diskon'] . '%';
                                    } else {
                                        echo 'Rp ' . number_format($row['nilai_diskon'], 0, ',', '.');
                                    }
                                    ?>
                                </td>
                                <td><?php echo ucfirst($row['tipe_diskon']); ?></td>
                                <td>
                                    <?php 
                                    if ($row['kondisi_diskon'] === 'produk') {
                                        echo 'Produk: ' . $row['nilai_kondisi'];
                                    } elseif ($row['kondisi_diskon'] === 'kategori') {
                                        echo 'Kategori: ' . $row['nilai_kondisi'];
                                    } elseif ($row['kondisi_diskon'] === 'pembayaran') {
                                        echo 'Pembayaran: ' . $row['nilai_kondisi'];
                                    } elseif ($row['kondisi_diskon'] === 'total_belanja') {
                                        echo 'Min. Belanja: Rp ' . number_format($row['nilai_kondisi'], 0, ',', '.');
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($row['tanggal_mulai'] && $row['tanggal_selesai']) {
                                        echo date('d/m/Y', strtotime($row['tanggal_mulai'])) . ' - ' . 
                                             date('d/m/Y', strtotime($row['tanggal_selesai']));
                                    } else {
                                        echo 'Selamanya';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($row['status_diskon'] === 'aktif'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" 
                                            onclick="editDiskon(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deleteDiskon(<?php echo $row['id_diskon']; ?>)">
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

<!-- Modal Diskon -->
<div class="modal fade" id="diskonModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Form Diskon</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_diskon" id="id_diskon">
                    
                    <div class="form-group">
                        <label>Nama Diskon</label>
                        <input type="text" name="nama_diskon" id="nama_diskon" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Diskon</label>
                                <select name="jenis_diskon" id="jenis_diskon" class="form-control" required>
                                    <option value="persentase">Persentase (%)</option>
                                    <option value="nominal">Nominal (Rp)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Diskon</label>
                                <input type="number" name="nilai_diskon" id="nilai_diskon" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe Diskon</label>
                                <select name="tipe_diskon" id="tipe_diskon" class="form-control" required onchange="toggleKondisi()">
                                    <option value="manual">Manual</option>
                                    <option value="otomatis">Otomatis</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status_diskon" id="status_diskon" class="form-control" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Periode Diskon - Selalu Tampil -->
                    <div class="form-group">
                        <label>Periode Diskon</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control">
                            </div>
                        </div>
                        <small class="form-text text-muted">Kosongkan jika diskon berlaku selamanya</small>
                    </div>
                    
                    <div id="kondisiGroup" style="display: none;">
                        <div class="form-group">
                            <label>Kondisi Diskon</label>
                            <select name="kondisi_diskon" id="kondisi_diskon" class="form-control" onchange="toggleNilaiKondisi()">
                                <option value="">Pilih Kondisi</option>
                                <option value="produk">Produk Tertentu</option>
                                <option value="kategori">Kategori Tertentu</option>
                                <option value="pembayaran">Metode Pembayaran</option>
                                <option value="total_belanja">Total Belanja</option>
                            </select>
                        </div>
                        
                        <div id="produkGroup" style="display: none;">
                            <div class="form-group">
                                <label>Pilih Produk</label>
                                <select name="produk[]" id="produk" class="form-control select2" multiple>
                                    <?php while ($produk = mysqli_fetch_assoc($result_produk)): ?>
                                        <option value="<?php echo $produk['id_produk']; ?>">
                                            <?php echo $produk['nama_produk']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div id="kategoriGroup" style="display: none;">
                            <div class="form-group">
                                <label>Pilih Kategori</label>
                                <select name="kategori[]" id="kategori" class="form-control select2" multiple>
                                    <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                                        <option value="<?php echo $kategori['id_kategori']; ?>">
                                            <?php echo $kategori['nama_kategori']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div id="pembayaranGroup" style="display: none;">
                            <div class="form-group">
                                <label>Metode Pembayaran</label>
                                <select name="nilai_kondisi" id="nilai_kondisi_pembayaran" class="form-control">
                                    <option value="cash">Tunai</option>
                                    <option value="qris">QRIS</option>
                                    <option value="transfer">Transfer Bank</option>
                                </select>
                            </div>
                        </div>
                        
                        <div id="totalBelanjaGroup" style="display: none;">
                            <div class="form-group">
                                <label>Minimal Total Belanja</label>
                                <input type="number" name="nilai_kondisi" id="nilai_kondisi_total" class="form-control">
                            </div>
                        </div>
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
// Inisialisasi Select2
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });
});

function toggleKondisi() {
    const tipeDiskon = $('#tipe_diskon').val();
    if (tipeDiskon === 'otomatis') {
        $('#kondisiGroup').show();
    } else {
        $('#kondisiGroup').hide();
        $('#produkGroup').hide();
        $('#kategoriGroup').hide();
        $('#pembayaranGroup').hide();
        $('#totalBelanjaGroup').hide();
    }
}

function toggleNilaiKondisi() {
    const kondisiDiskon = $('#kondisi_diskon').val();
    
    // Sembunyikan semua grup
    $('#produkGroup').hide();
    $('#kategoriGroup').hide();
    $('#pembayaranGroup').hide();
    $('#totalBelanjaGroup').hide();
    
    // Tampilkan grup yang sesuai
    if (kondisiDiskon === 'produk') {
        $('#produkGroup').show();
    } else if (kondisiDiskon === 'kategori') {
        $('#kategoriGroup').show();
    } else if (kondisiDiskon === 'pembayaran') {
        $('#pembayaranGroup').show();
    } else if (kondisiDiskon === 'total_belanja') {
        $('#totalBelanjaGroup').show();
    }
}

function editDiskon(diskon) {
    $('#id_diskon').val(diskon.id_diskon);
    $('#nama_diskon').val(diskon.nama_diskon);
    $('#jenis_diskon').val(diskon.jenis_diskon);
    $('#nilai_diskon').val(diskon.nilai_diskon);
    $('#tipe_diskon').val(diskon.tipe_diskon);
    $('#status_diskon').val(diskon.status_diskon);
    
    // Set tanggal mulai dan selesai
    if (diskon.tanggal_mulai) {
        $('#tanggal_mulai').val(diskon.tanggal_mulai);
    }
    if (diskon.tanggal_selesai) {
        $('#tanggal_selesai').val(diskon.tanggal_selesai);
    }
    
    if (diskon.tipe_diskon === 'otomatis') {
        $('#kondisiGroup').show();
        $('#kondisi_diskon').val(diskon.kondisi_diskon);
        
        if (diskon.kondisi_diskon === 'produk') {
            $('#produkGroup').show();
            // Load produk yang dipilih
            $.get('/pos_kasir/ajax/get_diskon_detail.php', {
                id_diskon: diskon.id_diskon,
                tipe: 'produk'
            }, function(response) {
                $('#produk').val(response).trigger('change');
            });
        } else if (diskon.kondisi_diskon === 'kategori') {
            $('#kategoriGroup').show();
            // Load kategori yang dipilih
            $.get('/pos_kasir/ajax/get_diskon_detail.php', {
                id_diskon: diskon.id_diskon,
                tipe: 'kategori'
            }, function(response) {
                $('#kategori').val(response).trigger('change');
            });
        } else if (diskon.kondisi_diskon === 'pembayaran') {
            $('#pembayaranGroup').show();
            $('#nilai_kondisi_pembayaran').val(diskon.nilai_kondisi);
        } else if (diskon.kondisi_diskon === 'total_belanja') {
            $('#totalBelanjaGroup').show();
            $('#nilai_kondisi_total').val(diskon.nilai_kondisi);
        }
    }
    
    $('#diskonModal').modal('show');
}

function deleteDiskon(id) {
    if (confirm('Apakah Anda yakin ingin menghapus diskon ini? Tindakan ini tidak dapat dibatalkan.')) {
        window.location.href = '/pos_kasir/diskon?delete=' + id;
    }
}
</script> 