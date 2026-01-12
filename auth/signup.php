<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Sistem Pakar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="/pakar/css/auth-style.css">
</head>

<body>
    <div class="auth-page">
        <div class="auth-card">
            <h2>Sign Up</h2>

            <?php if (isset($_GET['error'])): ?>
                <div class="auth-alert error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="/pakar/proses/proses_signup.php" method="POST" id="signup-form">
                <div class="input-group">
                    <label for="username">Enter Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter Username" required>
                </div>
                <div class="input-group">
                    <label for="password">Enter Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter Password" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Your Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Enter Password" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                    <small id="password-mismatch-error" class="password-error"></small>
                </div>
                <!-- Teks tombol diubah -->
                <button type="submit" class="btn-gradient">Sign Up</button>
            </form>
            <div class="auth-switch">
                <!-- Teks link diubah -->
                Sudah punya akun? <a href="/pakar/auth/login.php">Login disini</a>
            </div>
        </div>
    </div>

    <!-- Memanggil file JavaScript eksternal untuk validasi -->
    <script src="/pakar/js/confirm.js"></script>
</body>

</html>