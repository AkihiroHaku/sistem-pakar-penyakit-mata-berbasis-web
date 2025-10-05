<?php
// cek eror
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pakar</title>
    <link rel="stylesheet" href="css/auth-style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <h2>Log In</h2>
            <form action="#" method="POST">
                <div class="input-group">
                    <label for="login-username">Username</label>
                    <input type="text" id="login-username" placeholder="Enter Username">
                </div>
                <div class="input-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" placeholder="Enter Password">
                </div>
                <button type="submit" class="btn-gradient">Login</button>
            </form>
            <p class="auth-switch">Belum punya akun? <a href="signup.php">Daftar disini</a></p>
        </div>
    </div>
</body>
</html>
