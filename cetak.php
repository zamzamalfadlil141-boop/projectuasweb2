<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['role'])) {
    exit;
}

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT transaksi.*, layanan.nama_layanan FROM transaksi LEFT JOIN layanan ON transaksi.id_layanan = layanan.id_layanan WHERE id_transaksi = '$id'"));
if (!$data) { die("Nota tidak ditemukan."); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Nota #LN-<?= $data['id_transaksi']; ?></title>
    <style>
        body { font-family: 'Courier New', monospace; width: 280px; margin: 10px auto; font-size: 13px; line-height: 1.2; }
        .text-center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 8px 0; }
        .flex { display: flex; justify-content: space-between; }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <strong>PREMIUM LAUNDRY OS</strong><br>
        Sistem Kasir Digital Terintegrasi
    </div>
    <div class="line"></div>
    <div>Nota : #LN-<?= $data['id_transaksi']; ?></div>
    <div>Waktu: <?= date('d/m/Y H:i', strtotime($data['tanggal_transaksi'])); ?></div>
    <div>Nama : <?= htmlspecialchars($data['nama_pelanggan']); ?></div>
    <div class="line"></div>
    <div class="flex">
        <span><?= htmlspecialchars($data['nama_layanan']); ?></span>
        <span><?= $data['berat_qty']; ?> Kg</span>
    </div>
    <div class="line"></div>
    <div class="flex" style="font-weight: bold;">
        <span>TOTAL TAGIHAN</span>
        <span>Rp <?= number_format($data['total_bayar']); ?></span>
    </div>
    <div class="flex" style="font-size: 11px; margin-top: 4px;">
        <span>Status Transaksi</span>
        <span>[ <?= strtoupper($data['status_pesanan']); ?> ]</span>
    </div>
    <div class="line"></div>
    <div class="text-center" style="font-size: 11px;">Terima kasih atas kunjungan Anda!</div>
</body>
</html>