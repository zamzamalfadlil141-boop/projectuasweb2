<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['role'])) {
    exit;
}

if (isset($_POST['id_transaksi']) && isset($_POST['status_pesanan'])) {
    $id = $_POST['id_transaksi'];
    $status = $_POST['status_pesanan'];
    mysqli_query($koneksi, "UPDATE transaksi SET status_pesanan = '$status' WHERE id_transaksi = '$id'");
    header("Location: index.php");
}
?>