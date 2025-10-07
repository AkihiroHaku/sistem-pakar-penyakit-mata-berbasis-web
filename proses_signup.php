<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Validasi Input Dasar
if (!isset($_POST['username'], $_POST['password'], $_POST['confirm_password'])) {
    // Mengarahkan kembali dengan pesan error jika ada field yang tidak dikirim
    header("Location: signup.php?error=Semua field harus diisi");
    exit();
}

// 2. Ambil data dari form dan bersihkan (email dihapus)
$username = trim($_POST['username']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// 3. Validasi Lanjutan (email dihapus)
if (empty($username) || empty($password) || empty($confirm_password)) {
    header("Location: signup.php?error=Semua field harus diisi");
    exit();
}

if ($password !== $confirm_password) {
    header("Location: signup.php?error=Password tidak cocok");
    exit();
}

if (strlen($password) < 6) {
    header("Location: signup.php?error=Password minimal 6 karakter");
    exit();
}

// 4. Cek apakah username sudah ada di database (pengecekan email dihapus)
try {
    $sql_check = "SELECT id FROM users WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$username]);
    if ($stmt_check->fetch()) {
        // Jika username ditemukan, kirim pesan error
        header("Location: signup.php?error=Username sudah terdaftar");
        exit();
    }
} catch (PDOException $e) {
    // Tangani error database dengan pesan generik
    die("Error saat memeriksa data pengguna: " . $e->getMessage());
}

// 5. Hash Password untuk keamanan
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// 6. Masukkan data baru ke database (query INSERT tanpa email)
try {
    $sql_insert = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->execute([$username, $password_hash]);

    // 7. Arahkan ke halaman login dengan pesan sukses
    header("Location: login.php?success=Pendaftaran berhasil! Silakan login.");
    exit();

} catch (PDOException $e) {
    // Tangani error saat proses pendaftaran
    die("Error saat mendaftarkan pengguna: " . $e->getMessage());
}
?>

