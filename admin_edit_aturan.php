<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Validasi ID Aturan dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: admin_aturan.php?error=ID aturan tidak valid.");
    exit();
}
$id_aturan_edit = $_GET['id'];

try {
    // 3. Ambil data aturan yang ada (Penyakit & CF)
    $sql_rule = "SELECT b.idpenyakit, d.cfpakar 
                 FROM basis_aturan b
                 JOIN detail_basis_aturan d ON b.idaturan = d.idaturan
                 WHERE b.idaturan = ? 
                 LIMIT 1";
    $stmt_rule = $conn->prepare($sql_rule);
    $stmt_rule->execute([$id_aturan_edit]);
    $aturan = $stmt_rule->fetch(PDO::FETCH_ASSOC);

    if (!$aturan) {
        header("Location: admin_aturan.php?error=Aturan tidak ditemukan.");
        exit();
    }

    // 4. Ambil semua gejala yang sudah tercentang untuk aturan ini
    $sql_gejala_terpilih = "SELECT idgejala FROM detail_basis_aturan WHERE idaturan = ?";
    $stmt_gejala_terpilih = $conn->prepare($sql_gejala_terpilih);
    $stmt_gejala_terpilih->execute([$id_aturan_edit]);
    $gejala_terpilih_ids = $stmt_gejala_terpilih->fetchAll(PDO::FETCH_COLUMN, 0);

    // 5. Ambil semua data master (Penyakit & Gejala) untuk formulir
    $stmt_penyakit = $conn->prepare("SELECT id, kode_penyakit, penyakit FROM penyakit ORDER BY kode_penyakit ASC");
    $stmt_penyakit->execute();
    $daftar_penyakit = $stmt_penyakit->fetchAll(PDO::FETCH_ASSOC);

    $stmt_gejala = $conn->prepare("SELECT idgejala, nmgejala FROM gejala ORDER BY idgejala ASC");
    $stmt_gejala->execute();
    $daftar_gejala = $stmt_gejala->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Edit Aturan #<?= $id_aturan_edit ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        .gejala-select-container { height: 300px; overflow-y: auto; border: 1px solid #ccc; border-radius: 6px; padding: 10px; }
        .gejala-select-container .form-check { margin-bottom: 10px; }
    </style>
</head>
<body>

    <!-- Sidebar Navigasi Admin -->
    <div class="admin-sidebar">
        <h3 class="sidebar-title">Admin Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="admin_gejala.php"><i class="fas fa-tasks"></i> Kelola Gejala</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_penyakit.php"><i class="fas fa-virus"></i> Kelola Penyakit</a></li>
            <li class="nav-item"><a class="nav-link active" href="admin_aturan.php"><i class="fas fa-network-wired"></i> Kelola Aturan</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_keyakinan.php"><i class="fas fa-percent"></i> Kelola Keyakinan</a></li>
            <li class="nav-item-divider"></li>
            <li class="nav-item"><a class="nav-link" href="index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Situs Publik</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
        </ul>
    </div>

    <!-- Konten Utama Halaman Admin -->
    <div class="admin-content">
        <div class="container-fluid">
            <h1 class="admin-title">Edit Aturan #<?= $id_aturan_edit ?></h1>

            <!-- Form Edit Aturan -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-edit"></i> Mengubah Aturan
                </div>
                <div class="card-body">
                    <form action="proses_edit_aturan.php" method="POST">
                        <!-- Kirim ID Aturan yang sedang diedit -->
                        <input type="hidden" name="idaturan" value="<?= $id_aturan_edit ?>">
                        
                        <div class="row">
                            <!-- Kolom Penyakit -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="idpenyakit" class="form-label"><b>MAKA:</b> Penyakit (Kesimpulan)</label>
                                    <select class="form-select" id="idpenyakit" name="idpenyakit" required>
                                        <option value="">-- Pilih Penyakit --</option>
                                        <?php foreach ($daftar_penyakit as $penyakit): ?>
                                            <option value="<?= $penyakit['id'] ?>" 
                                                <?= ($penyakit['id'] == $aturan['idpenyakit']) ? 'selected' : '' ?>>
                                                (<?= htmlspecialchars($penyakit['kode_penyakit']) ?>) <?= htmlspecialchars($penyakit['penyakit']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="cfpakar" class="form-label"><b>NILAI CF:</b> Bobot/Keyakinan Pakar</label>
                                    <input type="number" step="0.01" min="0" max="1" class="form-control" id="cfpakar" name="cfpakar" 
                                        value="<?= htmlspecialchars($aturan['cfpakar']) ?>" required>
                                    <small class="form-text text-muted">Nilai desimal antara 0.01 dan 1.0.</small>
                                </div>
                            </div>
                            <!-- Kolom Gejala -->
                            <div class="col-md-8">
                                <label class="form-label"><b>JIKA:</b> Gejala-Gejala (Syarat)</label>
                                <div class="gejala-select-container">
                                    <?php foreach ($daftar_gejala as $gejala): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="idgejala[]" value="<?= $gejala['idgejala'] ?>" id="gejala_<?= $gejala['idgejala'] ?>"
                                                <?= (in_array($gejala['idgejala'], $gejala_terpilih_ids)) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="gejala_<?= $gejala['idgejala'] ?>">
                                                (ID: <?= $gejala['idgejala'] ?>) <?= htmlspecialchars($gejala['nmgejala']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning mt-3">
                            <i class="fas fa-save"></i> Update Aturan
                        </button>
                        <a href="admin_aturan.php" class="btn btn-secondary mt-3">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- .admin-content -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-script.js"></script>
</body>
</html>