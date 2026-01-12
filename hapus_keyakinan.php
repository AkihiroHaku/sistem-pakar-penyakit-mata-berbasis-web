<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi ID
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: admin/admin_keyakinan.php?error=ID tidak valid.");
    exit();
}
$id = $_GET['id'];

try {
    // 3. Hapus data
    $stmt = $conn->prepare("DELETE FROM cf_keyakinan WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin/admin_keyakinan.php?success=Nilai keyakinan berhasil dihapus.");
    exit();
} catch (PDOException $e) {
    header("Location: admin/admin_keyakinan.php?error=Error database: " . $e->getMessage());
    exit();
}
?>