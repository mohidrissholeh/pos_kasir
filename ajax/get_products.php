<?php
session_start();
require_once '../config/database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    die('Unauthorized');
}

$kategori_id = isset($_POST['kategori_id']) ? $_POST['kategori_id'] : 'all';

// Query untuk mengambil data produk
$query = "SELECT p.*, k.nama_kategori 
          FROM produk p 
          LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
          WHERE p.status_produk = 'y' AND p.stok > 0";

if ($kategori_id !== 'all') {
    $query .= " AND p.id_kategori = '$kategori_id'";
}

$query .= " ORDER BY p.nama_produk ASC";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die('Error: ' . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) == 0) {
    echo '<div class="col-12 text-center"><p>Tidak ada produk yang tersedia</p></div>';
} else {
    while ($row = mysqli_fetch_assoc($result)):
    ?>
    <div class="col-md-3 mb-3">
        <div class="card product-card" onclick="addToCart(<?php echo htmlspecialchars(json_encode($row)); ?>)">
            <?php if (!empty($row['gambar'])): ?>
                <img src="/pos_kasir/<?php echo $row['gambar']; ?>" class="product-image" alt="<?php echo $row['nama_produk']; ?>">
            <?php else: ?>
                <img src="/pos_kasir/dist/img/no-image.png" class="product-image" alt="No Image">
            <?php endif; ?>
            <div class="product-info">
                <div class="product-name"><?php echo $row['nama_produk']; ?></div>
                <div class="product-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
                <div class="product-stock">Stok: <?php echo $row['stok']; ?></div>
            </div>
        </div>
    </div>
    <?php 
    endwhile;
}
?> 