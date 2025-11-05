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
    if (empty($_POST['kode_penyakit']) || empty($_POST['nmpenyakit']) || empty($_POST['keterangan']) || empty($_POST['solusi'])) {
        header("Location: admin_penyakit.php?error=Semua field wajib diisi.");
        exit();
    }

    $kode_penyakit = trim($_POST['kode_penyakit']);
    $nmpenyakit = trim($_POST['nmpenyakit']);
    $keterangan = trim($_POST['keterangan']);
    $solusi = trim($_POST['solusi']);

    try {
        // 3. Cek duplikat kode penyakit
        $sql_check = "SELECT id FROM penyakit WHERE kode_penyakit = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$kode_penyakit]);

        if ($stmt_check->fetch()) {
            header("Location: admin_penyakit.php?error=Kode penyakit '" . htmlspecialchars($kode_penyakit) . "' sudah digunakan.");
            exit();
        }

        // 4. Masukkan penyakit baru
        $sql_insert = "INSERT INTO penyakit (kode_penyakit, penyakit, keterangan, solusi) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([$kode_penyakit, $nmpenyakit, $keterangan, $solusi]);

        header("Location: admin_penyakit.php?success=Penyakit '" . htmlspecialchars($nmpenyakit) . "' berhasil ditambahkan.");
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