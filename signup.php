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
    <title>Sign Up - Sistem Pakar</title>
    <link rel="stylesheet" href="css/auth-style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <h2>Sign Up</h2>
            <form action="#" method="POST">
                <div class="input-group">
                    <label for="signup-username">Enter Username</label>
                    <input type="text" id="signup-username" placeholder="Enter Username">
                </div>
                <div class="input-group">
                    <label for="signup-password">Enter Password</label>
                    <input type="password" id="signup-password" placeholder="Enter Password">
                </div>
                 <div class="input-group">
                    <label for="signup-confirm-password">Confirm Your Password</label>
                    <input type="password" id="signup-confirm-password" placeholder="Confirm your Password">
                </div>
                <button type="submit" class="btn-gradient">Sign Up</button>
            </form>
             <p class="auth-switch">Sudah punya akun? <a href="login.php">Login disini</a></p>
        </div>
    </div>
</body>
</html>
