<?php
$host = 'db';
$username = 'posuser';
$password = 'pospass';
$database = 'posdb';

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}