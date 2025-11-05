<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi input dari formulir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id_penyakit_edit']) || empty($_POST['kode_penyakit_edit']) || empty($_POST['nmpenyakit_edit']) || empty($_POST['keterangan_edit']) || empty($_POST['solusi_edit'])) {
        header("Location: admin_penyakit.php?error=Semua field wajib diisi saat mengedit.");
        exit();
    }

    $id = $_POST['id_penyakit_edit'];
    $kode_penyakit = trim($_POST['kode_penyakit_edit']);
    $nmpenyakit = trim($_POST['nmpenyakit_edit']);
    $keterangan = trim($_POST['keterangan_edit']);
    $solusi = trim($_POST['solusi_edit']);

    try {
        // 3. Cek duplikat kode penyakit (pastikan tidak sama dengan ID lain)
        $sql_check = "SELECT id FROM penyakit WHERE kode_penyakit = ? AND id != ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$kode_penyakit, $id]);

        if ($stmt_check->fetch()) {
            header("Location: admin_penyakit.php?error=Kode penyakit '" . htmlspecialchars($kode_penyakit) . "' sudah digunakan oleh penyakit lain.");
            exit();
        }

        // 4. Update data penyakit
        $sql_update = "UPDATE penyakit SET kode_penyakit = ?, penyakit = ?, keterangan = ?, solusi = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$kode_penyakit, $nmpenyakit, $keterangan, $solusi, $id]);

        header("Location: admin_penyakit.php?success=Data penyakit (ID: $id) berhasil diperbarui.");
        exit();

    } catch (PDOException $e) {
        header("Location: admin_penyakit.php?error=Error database: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: admin_penyakit.php");
    exit();
}
?>