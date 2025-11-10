<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['label']) || !isset($_POST['nilai'])) {
        header("Location: admin_keyakinan.php?error=Semua field wajib diisi.");
        exit();
    }
    $label = trim($_POST['label']);
    $nilai = (float)$_POST['nilai'];

    if ($nilai <= 0 || $nilai > 1) {
        header("Location: admin_keyakinan.php?error=Nilai CF harus antara 0.01 dan 1.0.");
        exit();
    }

    try {
        // 3. Masukkan data
        $stmt = $conn->prepare("INSERT INTO cf_keyakinan (label, nilai) VALUES (?, ?)");
        $stmt->execute([$label, $nilai]);
        header("Location: admin_keyakinan.php?success=Nilai keyakinan berhasil ditambahkan.");
        exit();
    } catch (PDOException $e) {
        header("Location: admin_keyakinan.php?error=Error database: " . $e->getMessage());
        exit();
    }
}
?>