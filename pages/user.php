<?php
// Set judul halaman
$page_title = "Manajemen User";

// Cek apakah user adalah admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: /pos_kasir/");
    exit();
}

// Proses tambah/edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_user = $_POST['nama_user'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status_user = $_POST['status_user'];
    
    if (empty($_POST['id_user'])) {
        // Tambah user baru
        $password = md5($password);
        $query = "INSERT INTO user (nama_user, username, password, role, status_user, created_at) 
                  VALUES ('$nama_user', '$username', '$password', '$role', '$status_user', NOW())";
    } else {
        // Edit user
        $id_user = $_POST['id_user'];
        if (!empty($password)) {
            $password = md5($password);
            $query = "UPDATE user SET 
                      nama_user = '$nama_user',
                      username = '$username',
                      password = '$password',
                      role = '$role',
                      status_user = '$status_user',
                      updated_at = NOW()
                      WHERE id_user = '$id_user'";
        } else {
            $query = "UPDATE user SET 
                      nama_user = '$nama_user',
                      username = '$username',
                      role = '$role',
                      status_user = '$status_user',
                      updated_at = NOW()
                      WHERE id_user = '$id_user'";
        }
    }
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data user berhasil disimpan!'); window.location.href = '/pos_kasir/user';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data user: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Proses hapus user
if (isset($_GET['delete'])) {
    $id_user = $_GET['delete'];
    $query = "DELETE FROM user WHERE id_user = '$id_user'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('User berhasil dihapus!'); window.location.href = '/pos_kasir/user';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Query untuk mengambil data user
$query = "SELECT * FROM user";
$result = mysqli_query($koneksi, $query);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen User</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#userModal">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>Noaaa</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
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
                                <td><?php echo $row['nama_user']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo ucfirst($row['role']); ?></td>
                                <td>
                                    <?php if ($row['status_user'] == 'y'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" 
                                            onclick="editUser(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deleteUser(<?php echo $row['id_user']; ?>)">
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

<!-- Modal User -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Form User</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="id_user">
                    <div class="form-group">
                        <label>Nama User</label>
                        <input type="text" name="nama_user" id="nama_user" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status_user" id="status_user" class="form-control" required>
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

<script>
function editUser(user) {
    $('#id_user').val(user.id_user);
    $('#nama_user').val(user.nama_user);
    $('#username').val(user.username);
    $('#password').val('');
    $('#role').val(user.role);
    $('#status_user').val(user.status_user);
    $('#userModal').modal('show');
}

function deleteUser(id) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        window.location.href = '/pos_kasir/user?delete=' + id;
    }
}
</script> 