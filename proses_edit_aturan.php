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
    if (empty($_POST['idaturan']) || empty($_POST['idpenyakit']) || empty($_POST['idgejala']) || !isset($_POST['cfpakar'])) {
        header("Location: admin_aturan.php?error=Data tidak lengkap.");
        exit();
    }

    $id_aturan = $_POST['idaturan'];
    $id_penyakit = $_POST['idpenyakit'];
    $gejala_terpilih = $_POST['idgejala']; // Ini adalah array
    $cfpakar = (float) $_POST['cfpakar'];

    if (!is_array($gejala_terpilih) || count($gejala_terpilih) == 0) {
        header("Location: admin_edit_aturan.php?id=$id_aturan&error=Anda harus memilih minimal satu gejala.");
        exit();
    }
    if ($cfpakar <= 0 || $cfpakar > 1) {
        header("Location: admin_edit_aturan.php?id=$id_aturan&error=Nilai CF Pakar harus antara 0.01 dan 1.0.");
        exit();
    }

    try {
        // 3. Mulai Transaksi Database
        $conn->beginTransaction();

        // 4. Update tabel 'basis_aturan' (tabel induk)
        $sql_basis = "UPDATE basis_aturan SET idpenyakit = ? WHERE idaturan = ?";
        $stmt_basis = $conn->prepare($sql_basis);
        $stmt_basis->execute([$id_penyakit, $id_aturan]);

        // 5. Hapus SEMUA gejala lama dari 'detail_basis_aturan' untuk aturan ini
        $sql_delete_details = "DELETE FROM detail_basis_aturan WHERE idaturan = ?";
        $stmt_delete = $conn->prepare($sql_delete_details);
        $stmt_delete->execute([$id_aturan]);

        // 6. Masukkan kembali semua gejala yang baru (dan nilai CF baru)
        $sql_detail = "INSERT INTO detail_basis_aturan (idaturan, idgejala, cfpakar) VALUES (?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);
        
        foreach ($gejala_terpilih as $id_gejala) {
            $stmt_detail->execute([$id_aturan, $id_gejala, $cfpakar]);
        }

        // 7. Jika semua berhasil, konfirmasi transaksi
        $conn->commit();

        header("Location: admin_aturan.php?success=Aturan (ID: $id_aturan) berhasil diperbarui.");
        exit();

    } catch (PDOException $e) {
        // 8. Jika ada error, batalkan semua perubahan
        $conn->rollBack();
        header("Location: admin_edit_aturan.php?id=$id_aturan&error=Error database: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: admin_aturan.php");
    exit();
}
?>