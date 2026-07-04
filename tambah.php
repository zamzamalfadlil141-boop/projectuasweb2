<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$query_layanan = mysqli_query($koneksi, "SELECT * FROM layanan");
$layanan_array = [];
while ($row = mysqli_fetch_assoc($query_layanan)) { $layanan_array[] = $row; }

if (isset($_POST['submit'])) {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $id_layanan = $_POST['id_layanan'];
    $berat_qty = $_POST['berat_qty'];
    
    $q_harga = mysqli_query($koneksi, "SELECT harga_per_satuan FROM layanan WHERE id_layanan = '$id_layanan'");
    $h_layanan = mysqli_fetch_assoc($q_harga);
    $total_bayar = $berat_qty * $h_layanan['harga_per_satuan'];

    $insert = mysqli_query($koneksi, "INSERT INTO transaksi (nama_pelanggan, id_layanan, berat_qty, total_bayar) VALUES ('$nama_pelanggan', '$id_layanan', '$berat_qty', '$total_bayar')");
    if ($insert) { header("Location: index.php"); }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Baru — Laundry OS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 40px; background-color: #f8fafc; color: #1e293b; }
        .card { background: white; padding: 30px; border-radius: 16px; border: 1px solid #e2e8f0; max-width: 480px; margin: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 13px; color: #475569; }
        input, select { width: 100%; padding: 10px 14px; box-sizing: border-box; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 14px; }
        input:focus, select:focus { outline: none; border-color: #4f46e5; }
        button { background: #4f46e5; color: white; padding: 12px; border: none; width: 100%; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; margin-top: 10px; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="card">
    <h2 style="margin: 0 0 20px 0; font-size: 20px;">Input Pesanan Baru</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Pelanggan</label>
            <input type="text" name="nama_pelanggan" required placeholder="Masukkan nama...">
        </div>
        <div class="form-group">
            <label>Jenis Layanan</label>
            <select name="id_layanan" id="id_layanan" onchange="hitungTotal()" required>
                <option value="">-- Pilih Layanan --</option>
                <?php foreach ($layanan_array as $l) : ?>
                    <option value="<?= $l['id_layanan']; ?>" data-harga="<?= $l['harga_per_satuan']; ?>">
                        <?= $l['nama_layanan']; ?> (Rp <?= number_format($l['harga_per_satuan']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Berat / Jumlah (Kg)</label>
            <input type="number" step="0.1" name="berat_qty" id="berat_qty" oninput="hitungTotal()" required placeholder="0.0">
        </div>
        <div class="form-group">
            <label>Total Tagihan</label>
            <input type="text" id="total_bayar_display" readonly style="background: #f1f5f9; font-weight: 700; color: #0f172a;">
        </div>
        <button type="submit" name="submit">Simpan & Masukkan Antrean</button>
    </form>
    <a class="back-link" href="index.php">← Kembali ke Dashboard</a>
</div>

<script>
function hitungTotal() {
    var layanan = document.getElementById("id_layanan");
    var harga = layanan.options[layanan.selectedIndex].getAttribute("data-harga");
    var berat = document.getElementById("berat_qty").value;
    if (harga && berat) {
        document.getElementById("total_bayar_display").value = "Rp " + (parseFloat(harga) * parseFloat(berat)).toLocaleString('id-ID');
    } else { document.getElementById("total_bayar_display").value = ""; }
}
</script>
</body>
</html>