<?php
session_start();
require_once '../includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi ID Penyakit
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: ../admin/admin_penyakit.php?error=ID penyakit tidak valid.");
    exit();
}

$id_penyakit_hapus = $_GET['id'];

try {
    // Mulai transaksi
    $conn->beginTransaction();

    // 3. Hapus semua referensi penyakit ini dari tabel aturan
    // (Cari dulu ID aturan yang terkait dengan penyakit ini)
    $sql_find_aturan = "SELECT idaturan FROM basis_aturan WHERE idpenyakit = ?";
    $stmt_find = $conn->prepare($sql_find_aturan);
    $stmt_find->execute([$id_penyakit_hapus]);
    $aturan_ids = $stmt_find->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($aturan_ids)) {
        // Hapus dari detail_basis_aturan
        $placeholders = implode(',', array_fill(0, count($aturan_ids), '?'));
        $sql_delete_details = "DELETE FROM detail_basis_aturan WHERE idaturan IN ($placeholders)";
        $stmt_delete_details = $conn->prepare($sql_delete_details);
        $stmt_delete_details->execute($aturan_ids);

        // Hapus dari basis_aturan
        $sql_delete_basis = "DELETE FROM basis_aturan WHERE idpenyakit = ?";
        $stmt_delete_basis = $conn->prepare($sql_delete_basis);
        $stmt_delete_basis->execute([$id_penyakit_hapus]);
    }
    
    // 4. Hapus dari riwayat konsultasi (opsional, bisa juga di-set NULL)
    $sql_delete_konsultasi = "DELETE FROM konsultasi WHERE id_penyakit_hasil = ?";
    $stmt_konsultasi = $conn->prepare($sql_delete_konsultasi);
    $stmt_konsultasi->execute([$id_penyakit_hapus]);

    // 5. Hapus penyakit itu sendiri
    $sql_delete_penyakit = "DELETE FROM penyakit WHERE id = ?";
    $stmt_penyakit = $conn->prepare($sql_delete_penyakit);
    $stmt_penyakit->execute([$id_penyakit_hapus]);

    // 6. Konfirmasi transaksi
    $conn->commit();

    header("Location: ../admin/admin_penyakit.php?success=Penyakit (ID: $id_penyakit_hapus) dan semua data terkait berhasil dihapus.");
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    header("Location: ../admin/admin_penyakit.php?error=Error database: " . $e->getMessage());
    exit();
}
?>