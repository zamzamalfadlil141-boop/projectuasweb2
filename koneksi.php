<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "laundry_db";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengunci zona waktu agar pendapatan harian akurat
date_default_timezone_set('Asia/Jakarta');
mysqli_query($koneksi, "SET time_zone = '+07:00'");
?>