<?php
include 'koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login — Laundry OS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; display: flex; height: 100vh; align-items: center; justify-content: center; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 16px; border: 1px solid #e2e8f0; width: 100%; max-width: 360px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 13px; color: #475569; }
        input { width: 100%; padding: 10px 14px; box-sizing: border-box; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        input:focus { outline: none; border-color: #4f46e5; }
        button { background: #4f46e5; color: white; padding: 12px; border: none; width: 100%; border-radius: 8px; font-weight: 700; cursor: pointer; margin-top: 10px; }
        .error { color: #dc2626; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; }
    </style>
</head>
<body>

<div class="card">
    <h2 style="margin: 0 0 5px 0; font-size: 22px; text-align: center;">Laundry OS</h2>
    <p style="margin: 0 0 24px 0; font-size: 14px; color: #64748b; text-align: center;">Masuk ke sistem kasir</p>
    
    <?php if(isset($error)): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Masukkan username...">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" name="login">Masuk Sistem</button>
    </form>
</div>

</body>
</html>