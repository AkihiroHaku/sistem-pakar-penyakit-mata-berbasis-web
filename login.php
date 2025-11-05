<?php
session_start();

// Jika pengguna sudah login, arahkan ke halaman utama
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pakar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="css/auth-style.css">
</head>

<body>
    <div class="auth-page">
        <div class="auth-card">
            <h2>Log In</h2>

            <!-- Bagian untuk menampilkan pesan error atau sukses -->
            <?php if (isset($_GET['error'])): ?>
                <div class="auth-alert error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="auth-alert success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <!-- Formulir diperbaiki -->
            <form action="proses_login.php" method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <!-- Menambahkan atribut 'name' -->
                    <input type="text" id="username" name="username" placeholder="Enter Username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <!-- Menambahkan atribut 'name' -->
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter Password" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>
                <button type="submit" class="btn-gradient">Login</button>
            </form>
            <div class="auth-switch">
                Belum punya akun? <a href="signup.php">Daftar disini</a>
            </div>
        </div>
    </div>
    <!-- Memanggil file JavaScript eksternal untuk validasi -->
    <script src="js/confirm.js"></script>
</body>
</html>