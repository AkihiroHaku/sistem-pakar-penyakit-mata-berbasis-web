<?php
session_start(); // Mulai session di paling atas
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi sederhana
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Username dan password harus diisi");
        exit();
    }

    try {
        // 1. Cari pengguna DAN ambil 'role' mereka
        // PERUBAHAN DI SINI: Meminta 'role' secara spesifik (lebih baik dari 'SELECT *')
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Jika pengguna ditemukan dan password cocok
        if ($user && password_verify($password, $user['password'])) {
            // Regenerasi session ID untuk keamanan
            session_regenerate_id(true);

            // 3. Simpan informasi pengguna DAN 'role' ke dalam session
            // Ini adalah bagian "Sesi Cerdas"
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // <-- PERUBAHAN PENTING
            
            // 4. Arahkan ke halaman utama
            header("Location: index.php");
            exit();
        } else {
            // Jika username atau password salah
            header("Location: login.php?error=Username atau password salah");
            exit();
        }
    } catch (PDOException $e) {
        die("Error login: " . $e->getMessage());
    }
} else {
    header("Location: login.php");
    exit();
}
?>