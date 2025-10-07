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

            <?php if (isset($_GET['error'])): ?>
                <div class="auth-alert error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="proses_signup.php" method="POST" id="signup-form">
                <div class="input-group">
                    <label for="username">Enter Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter Username" required>
                </div>
                <div class="input-group">
                    <label for="password">Enter Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Your Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Your Password" required>
                    <small id="password-mismatch-error" class="password-error"></small>
                </div>
                <!-- Teks tombol diubah -->
                <button type="submit" class="btn-gradient">Sign Up</button>
            </form>
            <div class="auth-switch">
                <!-- Teks link diubah -->
                Sudah punya akun? <a href="login.php">Login disini</a>
            </div>
        </div>
    </div>

    <!-- Memanggil file JavaScript eksternal untuk validasi -->
    <script src="js/script.js"></script>
</body>

</html>