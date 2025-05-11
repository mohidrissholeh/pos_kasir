<?php
session_start();
require_once 'config/database.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Proses tambah/edit user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_user = $_POST['nama_user'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $status_user = $_POST['status_user'];

    if (!empty($_POST['id_user'])) {
        // Update user
        $id_user = $_POST['id_user'];
        $query = "UPDATE user SET 
                  nama_user = '$nama_user',
                  username = '$username',
                  password = '$password',
                  role = '$role',
                  status_user = '$status_user'
                  WHERE id_user = '$id_user'";
    } else {
        // Tambah user baru
        $query = "INSERT INTO user (nama_user, username, password, role, status_user) 
                  VALUES ('$nama_user', '$username', '$password', '$role', '$status_user')";
    }

    // Debug: Tampilkan query dan data
    echo "<pre>";
    echo "Data yang dikirim:\n";
    print_r($_POST);
    echo "\nQuery yang dijalankan:\n";
    echo $query;
    echo "\n\nHasil query:\n";

    if (!mysqli_query($koneksi, $query)) {
        echo "Error: " . mysqli_error($koneksi);
    } else {
        echo "Query berhasil dijalankan";
        header("Location: user.php");
        exit();
    }
    echo "</pre>";
    exit();
}

// Hapus user
if (isset($_GET['delete'])) {
    $id_user = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM user WHERE id_user = '$id_user'");
    header("Location: user.php");
    exit();
}
?>

<!--- hanya bisa diakses oleh admin -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar User</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-user">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="table-user" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM user ORDER BY nama_user";
                    $result = mysqli_query($koneksi, $query);
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['nama_user'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['role'] . "</td>";
                        echo "<td>" . ($row['status_user'] == 'y' ? 'Aktif' : 'Tidak Aktif') . "</td>";
                        echo "<td>
                                            <button class='btn btn-info btn-sm btn-edit' 
                                                    data-id='" . $row['id_user'] . "'
                                                    data-nama='" . $row['nama_user'] . "'
                                                    data-username='" . $row['username'] . "'
                                                    data-role='" . $row['role'] . "'
                                                    data-status='" . $row['status_user'] . "'>
                                                <i class='fas fa-edit'></i>
                                            </button>
                                            <a href='?delete=" . $row['id_user'] . "' class='btn btn-danger btn-sm' 
                                               onclick='return confirm(\"Yakin ingin menghapus user ini?\")'>
                                                <i class='fas fa-trash'></i>
                                            </a>
                                          </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<!-- Modal User -->
<div class="modal fade" id="modal-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah/Edit User</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="id_user">
                    <div class="form-group">
                        <label>Nama User</label>
                        <input type="text" class="form-control" name="nama_user" id="nama_user" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" name="role" id="role" required>
                            <option value="kasir">Kasir</option>
                            <option value="spv">Supervisor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status_user" id="status_user" required>
                            <option value="y">Aktif</option>
                            <option value="n">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        $('#table-user').DataTable();

        // Edit user
        $('.btn-edit').click(function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const username = $(this).data('username');
            const role = $(this).data('role');
            const status = $(this).data('status');

            $('#id_user').val(id);
            $('#nama_user').val(nama);
            $('#username').val(username);
            $('#password').val('').removeAttr('required');
            $('#role').val(role);
            $('#status_user').val(status);

            $('#modal-user').modal('show');
        });
    });
</script>