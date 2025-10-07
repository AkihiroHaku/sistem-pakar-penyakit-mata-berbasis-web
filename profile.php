<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}

// Informasi pengguna (contoh)
$user_id = $_SESSION['user_id'];
$username = "contoh_username"; // Ganti dengan data dari database
$email = "contoh@email.com"; // Ganti dengan data dari database
$profile_picture = "uploads/default_profile.png"; // Ganti dengan data dari database atau default

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Sistem Pakar</title>
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>Profil Pengguna</h1>
        </div>
        <div class="profile-content">
            <div class="profile-picture">
                <img src="<?php echo $profile_picture; ?>" alt="Foto Profil">
            </div>
            <div class="profile-info">
                <h2><?php echo $username; ?></h2>
                <p><strong>ID Pengguna:</strong> <?php echo $user_id; ?></p>
                <p><strong>Email:</strong> <?php echo $email; ?></p>
                <a href="edit_profile.php" class="edit-profile-button">Edit Profil</a>
                <a href="logout.php" class="logout-button">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>