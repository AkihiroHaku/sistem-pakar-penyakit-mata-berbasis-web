<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi ID Aturan
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: admin/admin_aturan.php?error=ID aturan tidak valid.");
    exit();
}

$id_aturan_hapus = $_GET['id'];

try {
    // Mulai transaksi
    $conn->beginTransaction();

    // 3. Hapus semua gejala terkait dari tabel 'detail_basis_aturan'
    $sql_delete_details = "DELETE FROM detail_basis_aturan WHERE idaturan = ?";
    $stmt_details = $conn->prepare($sql_delete_details);
    $stmt_details->execute([$id_aturan_hapus]);

    // 4. Hapus aturan utamanya dari 'basis_aturan'
    $sql_delete_basis = "DELETE FROM basis_aturan WHERE idaturan = ?";
    $stmt_basis = $conn->prepare($sql_delete_basis);
    $stmt_basis->execute([$id_aturan_hapus]);

    // 5. Konfirmasi transaksi
    $conn->commit();

    header("Location: admin/admin_aturan.php?success=Aturan (ID: $id_aturan_hapus) dan semua detailnya berhasil dihapus.");
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    header("Location: admin/admin_aturan.php?error=Error database: " . $e->getMessage());
    exit();
}
?>