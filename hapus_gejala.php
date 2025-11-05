<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi ID Gejala dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: admin_gejala.php?error=ID gejala tidak valid.");
    exit();
}

$id_gejala_hapus = $_GET['id'];

try {
    // Mulai transaksi
    $conn->beginTransaction();

    // 3. Hapus semua referensi gejala ini dari tabel aturan
    // Ini penting agar tidak ada aturan yang "menggantung"
    $sql_delete_aturan = "DELETE FROM detail_basis_aturan WHERE idgejala = ?";
    $stmt_aturan = $conn->prepare($sql_delete_aturan);
    $stmt_aturan->execute([$id_gejala_hapus]);

    // 4. Hapus gejala itu sendiri dari tabel gejala
    $sql_delete_gejala = "DELETE FROM gejala WHERE idgejala = ?";
    $stmt_gejala = $conn->prepare($sql_delete_gejala);
    $stmt_gejala->execute([$id_gejala_hapus]);

    // 5. Konfirmasi transaksi
    $conn->commit();

    // 6. Kembalikan ke halaman admin dengan pesan sukses
    header("Location: admin_gejala.php?success=Gejala (ID: $id_gejala_hapus) dan semua aturan terkait berhasil dihapus.");
    exit();

} catch (PDOException $e) {
    // Jika terjadi error, batalkan semua perubahan
    $conn->rollBack();
    die("Error database saat menghapus: " . $e->getMessage());
}
?>