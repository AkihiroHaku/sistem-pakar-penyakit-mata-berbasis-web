<?php
// ==========================================================
// === KODE DEBUGGING: TAMBAHKAN INI DI PALING ATAS ===
// ==========================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ==========================================================

session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi: Pastikan pengguna login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php?error=Akses ditolak");
    exit();
}

// 2. Validasi ID Konsultasi dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: index.php?error=ID riwayat tidak valid.");
    exit();
}

$id_konsultasi_hapus = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // Mulai transaksi
    $conn->beginTransaction();

    // 3. Verifikasi Keamanan: Pastikan riwayat ini milik pengguna yang sedang login
    $sql_check = "SELECT id_user FROM konsultasi WHERE idkonsultasi = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$id_konsultasi_hapus]);
    $riwayat = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$riwayat || $riwayat['id_user'] != $user_id) {
        // Jika tidak ada, atau bukan milik user ini, batalkan.
        $conn->rollBack();
        header("Location: index.php?error=Gagal menghapus riwayat.");
        exit();
    }

    // 4. Hapus data dari 'detail_konsultasi' (sesuai .sql, kolomnya 'id_konsultasi')
    $sql_delete_details = "DELETE FROM detail_konsultasi WHERE id_konsultasi = ?";
    $stmt_delete_details = $conn->prepare($sql_delete_details);
    $stmt_delete_details->execute([$id_konsultasi_hapus]);
    
    // 5. Hapus data dari 'konsultasi' (sesuai .sql, kolomnya 'idkonsultasi')
    $sql_delete_konsultasi = "DELETE FROM konsultasi WHERE idkonsultasi = ? AND id_user = ?";
    $stmt_delete_konsultasi = $conn->prepare($sql_delete_konsultasi);
    $stmt_delete_konsultasi->execute([$id_konsultasi_hapus, $user_id]);
    
    // 6. Konfirmasi transaksi
    $conn->commit();

    // Arahkan kembali ke halaman utama
    header("Location: index.php");
    exit();

} catch (PDOException $e) {
    // Jika ada error, batalkan semua perubahan
    $conn->rollBack();
    // Tampilkan error database jika terjadi
    die("Error database saat menghapus riwayat: " . $e->getMessage());
}
?>