<?php
include 'koneksi.php';
session_start();

// PROTEKSI KASIR: Jika belum login, tendang ke halaman login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Fitur Pencarian
$keyword = "";
$where_clause = "";
if (isset($_GET['cari'])) {
    $keyword = $_GET['cari'];
    $where_clause = "WHERE nama_pelanggan LIKE '%$keyword%' OR status_pesanan LIKE '%$keyword%'";
}

// Ambil Riwayat Transaksi
$query = "SELECT transaksi.*, layanan.nama_layanan FROM transaksi 
          LEFT JOIN layanan ON transaksi.id_layanan = layanan.id_layanan 
          $where_clause ORDER BY id_transaksi DESC";
$tampil = mysqli_query($koneksi, $query);

// Laporan Pendapatan Hari Ini
$hari_ini = date('Y-m-d');
$omset_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE(tanggal_transaksi) = '$hari_ini'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laundry OS — Cashier Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-main: #f8fafc; --sidebar-bg: #0f172a; --sidebar-width: 280px; --text-main: #1e293b; --primary: #4f46e5; --border-color: #e2e8f0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; background-color: var(--bg-main); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }
        
        .sidebar { width: var(--sidebar-width); background: var(--sidebar-bg); color: #f8fafc; padding: 30px 24px; box-sizing: border-box; transition: all 0.4s ease; display: flex; flex-direction: column; gap: 20px; box-shadow: 4px 0 24px rgba(15,23,42,0.08); z-index: 10; }
        .sidebar.hide { margin-left: calc(-1 * var(--sidebar-width)); }
        .sidebar h3 { margin: 0; font-size: 15px; font-weight: 700; letter-spacing: 1.5px; color: #94a3b8; text-transform: uppercase; padding-bottom: 12px; border-bottom: 1px solid #1e293b; }
        
        .user-tag { font-size: 12px; background: #1e293b; padding: 6px 12px; border-radius: 8px; color: #38bdf8; font-weight: 600; text-transform: uppercase; text-align: center; margin-bottom: 5px; }

        .mini-box { background: #1e293b; padding: 16px; border-radius: 12px; border: 1px solid #334155; }
        .mini-box h4 { margin: 0; color: #94a3b8; font-size: 12px; font-weight: 500; text-transform: uppercase; }
        .mini-box p { margin: 8px 0 0 0; font-size: 20px; font-weight: 700; color: #38bdf8; }

        .sidebar a { color: #94a3b8; text-decoration: none; font-size: 14px; padding: 12px; border-radius: 8px; display: block; transition: 0.2s; text-align: center;}
        .sidebar a:hover { background: #1e293b; color: #fff; }

        .main-content { flex: 1; padding: 32px; overflow-y: auto; box-sizing: border-box; }
        .header-action { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        
        .btn-toggle { background: white; color: var(--text-main); border: 1px solid var(--border-color); padding: 10px 16px; cursor: pointer; border-radius: 10px; font-size: 14px; font-weight: 500; }
        .btn { padding: 8px 16px; text-decoration: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .btn-add { background: var(--primary); padding: 14px; justify-content: center; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); color: white !important; font-weight: 700;}
        .btn-add:hover { background: #4338ca; }
        .btn-print { background: white; color: #475569; border: 1px solid var(--border-color); }
        
        .search-container input { padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 10px; font-size: 14px; width: 240px; }
        .search-container button { padding: 10px 18px; font-size: 14px; font-weight: 600; cursor: pointer; background: #1e293b; color: white; border: none; border-radius: 10px; }

        .table-container { background: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { background-color: #f8fafc; color: #64748b; font-weight: 600; padding: 16px 20px; border-bottom: 1px solid var(--border-color); font-size: 13px; text-transform: uppercase; }
        td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        tr:hover td { background-color: #f8fafc; }
        
        .status-badge { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px; display: inline-block; }
        .Proses { background: #fef3c7; color: #d97706; }
        .Selesai { background: #d1fae5; color: #059669; }
        .Diambil { background: #f1f5f9; color: #475569; }
        select { padding: 6px 10px; font-size: 13px; border-radius: 8px; border: 1px solid var(--border-color); background: white;}
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <h3>Laundry OS</h3>
    <div class="user-tag">🔑 Operator: <?= $_SESSION['username']; ?></div>

    <div class="mini-box">
        <h4>Pendapatan Hari Ini</h4>
        <p>Rp <?= number_format($omset_hari['total'] ?? 0); ?></p>
    </div>

    <a href="tambah.php" class="btn btn-add">➕ Transaksi Baru</a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="admin.php" style="margin-top: auto; background: #312e81; color: #fff; font-weight: 600;">📊 Menu Management</a>
    <?php else: ?>
        <a href="logout.php" style="margin-top: auto; background: #334155; color: #fff;">Logout (Keluar) 🚪</a>
    <?php endif; ?>
</div>

<div class="main-content">
    <div class="header-action">
        <button class="btn-toggle" onclick="toggleSidebar()">☰ Menu Panel</button>
        <div class="search-container">
            <form action="" method="GET" style="display: flex; gap: 8px;">
                <input type="text" name="cari" placeholder="Cari nama pelanggan..." value="<?= htmlspecialchars($keyword); ?>">
                <button type="submit">Cari</button>
                <?php if($keyword != ""): ?> 
                    <a href="index.php" style="align-self:center; color:#64748b; text-decoration:none; font-size:13px; margin-left:5px;">Reset</a> 
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID Nota</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Berat</th>
                    <th>Total</th>
                    <th>Waktu Masuk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($tampil) == 0) : ?>
                    <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 32px;">Belum ada data transaksi tersimpan.</td></tr>
                <?php else : ?>
                    <?php while ($row = mysqli_fetch_assoc($tampil)) : ?>
                        <tr>
                            <td><span style="font-weight:700; color:var(--primary);">#LN-<?= $row['id_transaksi']; ?></span></td>
                            <td style="font-weight:500; color:#0f172a;"><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                            <td><?= htmlspecialchars($row['nama_layanan'] ?? 'Paket Dihapus'); ?></td>
                            <td><b><?= $row['berat_qty']; ?></b> <span style="color:#94a3b8; font-size:12px;">Kg</span></td>
                            <td style="font-weight:700; color:#0f172a;">Rp <?= number_format($row['total_bayar']); ?></td>
                            <td style="color:#64748b; font-size:13px;"><?= date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                            <td><span class="status-badge <?= $row['status_pesanan']; ?>"><?= $row['status_pesanan']; ?></span></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <form action="update_status.php" method="POST" style="margin:0;">
                                        <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi']; ?>">
                                        <select name="status_pesanan" onchange="this.form.submit()">
                                            <option value="Proses" <?= $row['status_pesanan']=='Proses'?'selected':''; ?>>Proses</option>
                                            <option value="Selesai" <?= $row['status_pesanan']=='Selesai'?'selected':''; ?>>Selesai</option>
                                            <option value="Diambil" <?= $row['status_pesanan']=='Diambil'?'selected':''; ?>>Diambil</option>
                                        </select>
                                    </form>
                                    <a href="cetak.php?id=<?= $row['id_transaksi']; ?>" target="_blank" class="btn btn-print">🖨️ Struk</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleSidebar() { document.getElementById("sidebar").classList.toggle("hide"); }
</script>
</body>
</html>