<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin: Pastikan hanya admin yang bisa menjalankan ini
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi input dari formulir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['nmgejala']) || empty(trim($_POST['nmgejala']))) {
        header("Location: admin_gejala.php?error=Nama gejala tidak boleh kosong.");
        exit();
    }

    $nama_gejala_baru = trim($_POST['nmgejala']);

    try {
        // 3. Cek apakah gejala (publik) sudah ada
        // Kita cek yang id_user-nya NULL (gejala publik/admin)
        $sql_check = "SELECT idgejala FROM gejala WHERE nmgejala = ? AND id_user IS NULL";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$nama_gejala_baru]);

        if ($stmt_check->fetch()) {
            header("Location: admin_gejala.php?error=Gejala publik '" . htmlspecialchars($nama_gejala_baru) . "' sudah ada.");
            exit();
        }

        // 4. Masukkan gejala baru sebagai gejala PUBLIK (id_user = NULL)
        $sql_insert = "INSERT INTO gejala (nmgejala, id_user) VALUES (?, NULL)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([$nama_gejala_baru]);

        // 5. Kembalikan ke halaman admin dengan pesan sukses
        header("Location: admin_gejala.php?success=Gejala publik '" . htmlspecialchars($nama_gejala_baru) . "' berhasil ditambahkan.");
        exit();

    } catch (PDOException $e) {
        // Jika terjadi error, kirim pesan error yang lebih spesifik
        header("Location: admin_gejala.php?error=Error database: " . $e->getMessage());
        exit();
    }
} else {
    // Jika file diakses langsung, alihkan
    header("Location: index.php");
    exit();
}
?>