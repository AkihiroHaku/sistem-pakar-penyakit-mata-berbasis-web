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
    if (empty($_POST['idpenyakit']) || empty($_POST['idgejala']) || !isset($_POST['cfpakar'])) {
        header("Location: admin_aturan.php?error=Penyakit, minimal satu gejala, dan nilai CF wajib diisi.");
        exit();
    }

    $id_penyakit = $_POST['idpenyakit'];
    $gejala_terpilih = $_POST['idgejala']; // Ini adalah array
    $cfpakar = (float) $_POST['cfpakar'];

    if (!is_array($gejala_terpilih) || count($gejala_terpilih) == 0) {
        header("Location: admin_aturan.php?error=Anda harus memilih minimal satu gejala.");
        exit();
    }
    if ($cfpakar <= 0 || $cfpakar > 1) {
        header("Location: admin_aturan.php?error=Nilai CF Pakar harus antara 0.01 dan 1.0.");
        exit();
    }

    try {
        // 3. Mulai Transaksi Database
        $conn->beginTransaction();

        // 4. Masukkan ke tabel 'basis_aturan' (tabel induk)
        $sql_basis = "INSERT INTO basis_aturan (idpenyakit) VALUES (?)";
        $stmt_basis = $conn->prepare($sql_basis);
        $stmt_basis->execute([$id_penyakit]);

        // 5. Ambil ID aturan baru yang baru saja dibuat
        $id_aturan_baru = $conn->lastInsertId();

        // 6. Masukkan semua gejala ke 'detail_basis_aturan' (tabel anak)
        $sql_detail = "INSERT INTO detail_basis_aturan (idaturan, idgejala, cfpakar) VALUES (?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);
        
        foreach ($gejala_terpilih as $id_gejala) {
            $stmt_detail->execute([$id_aturan_baru, $id_gejala, $cfpakar]);
        }

        // 7. Jika semua berhasil, konfirmasi transaksi
        $conn->commit();

        header("Location: admin_aturan.php?success=Aturan baru (ID: $id_aturan_baru) berhasil ditambahkan.");
        exit();

    } catch (PDOException $e) {
        // 8. Jika ada error, batalkan semua perubahan
        $conn->rollBack();
        header("Location: admin_aturan.php?error=Error database: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: admin_aturan.php");
    exit();
}
?>