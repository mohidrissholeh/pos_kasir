<?php
// Fungsi untuk mencatat log laba
function catatLogLaba($koneksi, $tanggal, $laba_bersih, $id_sumber, $id_user) {
    $query = "INSERT INTO log_laba_harian (tanggal, laba_bersih, id_sumber, id_user) 
              VALUES ('$tanggal', '$laba_bersih', '$id_sumber', '$id_user')";
    return mysqli_query($koneksi, $query);
}

// Fungsi untuk mendapatkan total laba dari kas harian
function getTotalLabaKas($koneksi, $tanggal) {
    $query = "SELECT SUM(total_laba) as total FROM kas_harian WHERE tanggal = '$tanggal' AND status = 'selesai'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Fungsi untuk mendapatkan total laba dari modal
function getTotalLabaModal($koneksi, $tanggal) {
    $query = "SELECT SUM(jumlah) as total FROM modal WHERE tanggal = '$tanggal'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Fungsi untuk mendapatkan total laba dari pemasukan lain
function getTotalLabaPemasukan($koneksi, $tanggal) {
    $query = "SELECT SUM(jumlah) as total FROM pemasukan_lain WHERE tanggal = '$tanggal'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Fungsi untuk mendapatkan total laba dari pengeluaran
function getTotalLabaPengeluaran($koneksi, $tanggal) {
    $query = "SELECT SUM(jumlah) as total FROM pengeluaran WHERE tanggal = '$tanggal'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Fungsi untuk menghitung total laba harian
function hitungTotalLabaHarian($koneksi, $tanggal) {
    $laba_kas = getTotalLabaKas($koneksi, $tanggal);
    $laba_modal = getTotalLabaModal($koneksi, $tanggal);
    $laba_pemasukan = getTotalLabaPemasukan($koneksi, $tanggal);
    $laba_pengeluaran = getTotalLabaPengeluaran($koneksi, $tanggal);
    
    return $laba_kas + $laba_modal + $laba_pemasukan - $laba_pengeluaran;
} 