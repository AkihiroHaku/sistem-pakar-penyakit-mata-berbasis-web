<?php
session_start();
require_once '../includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /pakar/index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi input dari formulir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['idgejala_edit']) || !filter_var($_POST['idgejala_edit'], FILTER_VALIDATE_INT)) {
        header("Location: ../admin/admin_gejala.php?error=ID gejala tidak valid.");
        exit();
    }

    if (!isset($_POST['nmgejala_edit']) || empty(trim($_POST['nmgejala_edit']))) {
        header("Location: ../admin/admin_gejala.php?error=Nama gejala tidak boleh kosong.");
        exit();
    }

    $id_gejala = $_POST['idgejala_edit'];
    $nama_gejala_baru = trim($_POST['nmgejala_edit']);

    try {
        // 3. Cek apakah gejala lain sudah ada dengan nama yang sama (kecuali gejala ini sendiri)
        $sql_check = "SELECT idgejala FROM gejala WHERE nmgejala = ? AND idgejala != ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$nama_gejala_baru, $id_gejala]);

        if ($stmt_check->fetch()) {
            header("Location: ../admin/admin_gejala.php?error=Gejala dengan nama '" . htmlspecialchars($nama_gejala_baru) . "' sudah ada.");
            exit();
        }

        // 4. Update gejala
        $sql_update = "UPDATE gejala SET nmgejala = ? WHERE idgejala = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$nama_gejala_baru, $id_gejala]);

        // 5. Kembalikan ke halaman admin dengan pesan sukses
        header("Location: ../admin/admin_gejala.php?success=Gejala (ID: $id_gejala) berhasil diperbarui menjadi '" . htmlspecialchars($nama_gejala_baru) . "'.");
        exit();

    } catch (PDOException $e) {
        // Jika terjadi error, kirim pesan error
        header("Location: ../admin/admin_gejala.php?error=Error database: " . $e->getMessage());
        exit();
    }
} else {
    // Jika file diakses langsung, alihkan
    header("Location: /pakar/index.php");
    exit();
}
?>

// proses/proses_edit_gejala.php
