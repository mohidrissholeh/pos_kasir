<?php
require_once 'config/database.php';

// Cek struktur tabel user
$query = "DESCRIBE user";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Error: " . mysqli_error($koneksi));
}

echo "<h2>Struktur Tabel User:</h2>";
echo "<pre>";
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
echo "</pre>";

// Cek isi tabel user
$query = "SELECT * FROM user";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Error: " . mysqli_error($koneksi));
}

echo "<h2>Isi Tabel User:</h2>";
echo "<pre>";
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
echo "</pre>";
?> 