<?php
include 'koneksi.php';
session_start();

// PROTEKSI KETAT: Jika tidak punya role admin, lempar balik ke index
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak! Halaman ini khusus untuk Owner/Admin.'); window.location='index.php';</script>";
    exit;
}

// Prosedur Tambah Layanan
if (isset($_POST['tambah_layanan'])) {
    $nama = $_POST['nama_layanan']; $harga = $_POST['harga_per_satuan']; $satuan = $_POST['satuan'];
    mysqli_query($koneksi, "INSERT INTO layanan (nama_layanan, harga_per_satuan, satuan) VALUES ('$nama', '$harga', '$satuan')");
    header("Location: admin.php");
}
// Prosedur Hapus Layanan
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM layanan WHERE id_layanan = '$id_hapus'");
    header("Location: admin.php");
}
// Prosedur Edit Layanan
if (isset($_POST['edit_layanan'])) {
    $id_edit = $_POST['id_layanan']; $nama = $_POST['nama_layanan']; $harga = $_POST['harga_per_satuan']; $satuan = $_POST['satuan'];
    mysqli_query($koneksi, "UPDATE layanan SET nama_layanan='$nama', harga_per_satuan='$harga', satuan='$satuan' WHERE id_layanan='$id_edit'");
    header("Location: admin.php");
}

// Pengambilan Data Finansial & Kerja untuk Keperluan Analisis
$total_transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi"))['total'] ?? 0;
$status_proses = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status_pesanan='Proses'"))['total'];
$status_selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status_pesanan='Selesai'"))['total'];
$status_diambil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status_pesanan='Diambil'"))['total'];
$layanan_all = mysqli_query($koneksi, "SELECT * FROM layanan");

$hari_ini = date('Y-m-d');
$bulan_ini = date('Y-m');
$omset_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE(tanggal_transaksi) = '$hari_ini'"));
$omset_bulan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE_FORMAT(tanggal_transaksi, '%Y-%m') = '$bulan_ini'"));

$layanan_edit = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $layanan_edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM layanan WHERE id_layanan='$id_edit'"));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin OS — Analytics</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-main: #f8fafc; --sidebar-bg: #0f172a; --text-main: #1e293b; --primary: #6366f1; --border-color: #e2e8f0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; background: var(--bg-main); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; }
        .sidebar h2 { font-size: 18px; margin: 0; }
        .sidebar a { color: #94a3b8; text-decoration: none; font-size: 14px; padding: 10px 12px; border-radius: 8px; display: block; }
        .sidebar a.active { background: #1e293b; color: #fff; font-weight: 600; }
        
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .grid-analisis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
        .card-analytic { background: white; padding: 16px; border-radius: 16px; border: 1px solid var(--border-color); }
        .card-analytic h3 { margin: 0; font-size: 11px; color: #64748b; text-transform: uppercase; }
        .card-analytic p { margin: 8px 0 0 0; font-size: 20px; font-weight: 700; color: #0f172a; }
        
        .chart-box { background: white; padding: 20px; border-radius: 16px; border: 1px solid var(--border-color); margin-bottom: 30px; }
        .bar-group { margin-bottom: 12px; }
        .bar-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; }
        .bar-bg { background: #e2e8f0; height: 10px; border-radius: 10px; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 10px; }

        .crud-container { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; }
        .form-panel, .table-panel { background: white; padding: 20px; border-radius: 16px; border: 1px solid var(--border-color); }
        .form-group { margin-bottom: 12px; }
        label { display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px; }
        input, select { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; box-sizing: border-box; font-family: inherit; }
        button.btn-submit { background: var(--primary); color: white; border: none; padding: 10px; width: 100%; border-radius: 8px; font-weight: 600; cursor: pointer; }
        
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #f8fafc; padding: 12px; color: #64748b; border-bottom: 1px solid var(--border-color); text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
        .btn-action { font-size: 12px; text-decoration: none; padding: 4px 8px; border-radius: 4px; font-weight:600; }
        .btn-edit { background: #eff6ff; color: #2563eb; }
        .btn-delete { background: #fef2f2; color: #dc2626; }
    </style>
</head>
<body>

<div class="sidebar">
    <div>
        <h2>Laundry OS</h2>
        <span style="font-size: 11px; background: #1e293b; padding: 4px 8px; border-radius: 12px; color: #38bdf8; font-weight:700;">OWNER PANEL</span>
    </div>
    <div style="margin-top: 20px; display:flex; flex-direction:column; gap:5px;">
        <a href="index.php">🖥️ Dashboard Kasir</a>
        <a href="admin.php" class="active">📊 Analisis & CRUD</a>
        <a href="logout.php" style="background: #334155; color: #fff; margin-top: 20px; text-align: center;">Keluar Sistem 🚪</a>
    </div>
</div>

<div class="main-content">
    <h2 style="margin: 0 0 20px 0; font-size: 22px;">Analisis Eksekutif & Manajemen Layanan</h2>

    <div class="grid-analisis">
        <div class="card-analytic"><h3>Pendapatan Hari Ini</h3><p style="color:#38bdf8;">Rp <?= number_format($omset_hari['total'] ?? 0); ?></p></div>
        <div class="card-analytic"><h3>Pendapatan Bulan Ini</h3><p style="color:#10b981;">Rp <?= number_format($omset_bulan['total'] ?? 0); ?></p></div>
        <div class="card-analytic"><h3>Total Omset Keseluruhan</h3><p>Rp <?= number_format($total_pendapatan); ?></p></div>
        <div class="card-analytic"><h3>Kuantitas Produksi</h3><p><?= $total_transaksi; ?> Pesanan</p></div>
    </div>

    <div class="chart-box">
        <h3 style="margin:0 0 15px 0; font-size:14px;">Beban Kerja & Status Antrean Cuci</h3>
        <?php 
            $p_proses = $total_transaksi > 0 ? ($status_proses / $total_transaksi) * 100 : 0;
            $p_selesai = $total_transaksi > 0 ? ($status_selesai / $total_transaksi) * 100 : 0;
            $p_diambil = $total_transaksi > 0 ? ($status_diambil / $total_transaksi) * 100 : 0;
        ?>
        <div class="bar-group">
            <div class="bar-label"><span>Proses Antrean Giling/Setrika</span><span><?= $status_proses; ?> Nota (<?= round($p_proses); ?>%)</span></div>
            <div class="bar-bg"><div class="bar-fill" style="width: <?= $p_proses; ?>%; background: #f59e0b;"></div></div>
        </div>
        <div class="bar-group">
            <div class="bar-label"><span>Selesai (Menunggu Diambil)</span><span><?= $status_selesai; ?> Nota (<?= round($p_selesai); ?>%)</span></div>
            <div class="bar-bg"><div class="bar-fill" style="width: <?= $p_selesai; ?>%; background: #10b981;"></div></div>
        </div>
        <div class="bar-group">
            <div class="bar-label"><span>Selesai Diambil</span><span><?= $status_diambil; ?> Nota (<?= round($p_diambil); ?>%)</span></div>
            <div class="bar-bg"><div class="bar-fill" style="width: <?= $p_diambil; ?>%; background: #64748b;"></div></div>
        </div>
    </div>

    <div class="crud-container">
        <div class="form-panel">
            <h3><?= $layanan_edit ? '⚙️ Edit Layanan' : '➕ Tambah Layanan' ?></h3>
            <form action="admin.php" method="POST">
                <?php if ($layanan_edit): ?><input type="hidden" name="id_layanan" value="<?= $layanan_edit['id_layanan']; ?>"><?php endif; ?>
                <div class="form-group">
                    <label>Nama Layanan</label>
                    <input type="text" name="nama_layanan" value="<?= $layanan_edit['nama_layanan'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Harga Tarif (Rp)</label>
                    <input type="number" name="harga_per_satuan" value="<?= $layanan_edit['harga_per_satuan'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Satuan</label>
                    <select name="satuan">
                        <option value="Kg" <?= (isset($layanan_edit) && $layanan_edit['satuan']=='Kg')?'selected':''; ?>>Kg</option>
                        <option value="Pcs" <?= (isset($layanan_edit) && $layanan_edit['satuan']=='Pcs')?'selected':''; ?>>Pcs</option>
                    </select>
                </div>
                <button type="submit" name="<?= $layanan_edit ? 'edit_layanan' : 'tambah_layanan' ?>" class="btn-submit">Simpan Paket</button>
            </form>
        </div>

        <div class="table-panel">
            <h3 style="margin: 0 0 15px 0;">Menu Layanan Aktif</h3>
            <table>
                <thead><tr><th>Nama Layanan</th><th>Tarif</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($layanan_all)): ?>
                        <tr>
                            <td style="font-weight:600;"><?= htmlspecialchars($row['nama_layanan']); ?></td>
                            <td>Rp <?= number_format($row['harga_per_satuan']); ?> / <?= $row['satuan']; ?></td>
                            <td>
                                <a href="admin.php?edit=<?= $row['id_layanan']; ?>" class="btn-action btn-edit">Edit</a>
                                <a href="admin.php?hapus=<?= $row['id_layanan']; ?>" class="btn-action btn-delete" onclick="return confirm('Hapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>