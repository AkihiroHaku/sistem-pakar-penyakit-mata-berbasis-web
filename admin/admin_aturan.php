<?php
session_start();
require_once '../includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Ambil semua data master (Penyakit & Gejala) untuk formulir
try {
    // Ambil semua penyakit
    $stmt_penyakit = $conn->prepare("SELECT id, kode_penyakit, penyakit FROM penyakit ORDER BY kode_penyakit ASC");
    $stmt_penyakit->execute();
    $daftar_penyakit = $stmt_penyakit->fetchAll(PDO::FETCH_ASSOC);

    // Ambil semua gejala
    $stmt_gejala = $conn->prepare("SELECT idgejala, nmgejala FROM gejala ORDER BY idgejala ASC");
    $stmt_gejala->execute();
    $daftar_gejala = $stmt_gejala->fetchAll(PDO::FETCH_ASSOC);

    // 3. Ambil semua aturan yang ada untuk ditampilkan
    $sql_aturan = "SELECT b.idaturan, p.penyakit, p.kode_penyakit, 
                          (SELECT COUNT(*) FROM detail_basis_aturan d WHERE d.idaturan = b.idaturan) as jumlah_gejala
                   FROM basis_aturan AS b
                   JOIN penyakit AS p ON b.idpenyakit = p.id
                   ORDER BY b.idaturan ASC";
    $stmt_aturan = $conn->prepare($sql_aturan);
    $stmt_aturan->execute();
    $daftar_aturan = $stmt_aturan->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Kelola Aturan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin-style.css">
    <!-- Style tambahan untuk multi-select -->
    <style>
        .gejala-select-container {
            height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
        }
        .gejala-select-container .form-check {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigasi Admin -->
    <div class="admin-sidebar">
        <h3 class="sidebar-title">Admin Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin_gejala.php">
                    <i class="fas fa-tasks"></i> Kelola Gejala
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_penyakit.php">
                    <i class="fas fa-virus"></i> Kelola Penyakit
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link active" href="admin_aturan.php">
                    <i class="fas fa-network-wired"></i> Kelola Aturan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_keyakinan.php">
                    <i class="fas fa-percent"></i> Kelola Keyakinan
                </a>
            </li>
            <li class="nav-item-divider"></li>
            <li class="nav-item">
                <a class="nav-link" href="/pakar\index.php" target="_blank">
                    <i class="fas fa-globe"></i> Lihat Situs Publik
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </li>
        </ul>
    </div>

    <!-- Konten Utama Halaman Admin -->
    <div class="admin-content">
        <div class="container-fluid">
            <h1 class="admin-title">Manajemen Aturan (Rules)</h1>

            <!-- Menampilkan Pesan Sukses/Error -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <!-- Form Tambah Aturan Baru -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i> Buat Aturan Pakar Baru
                </div>
                <div class="card-body">
                    <form action="/pakar/admin/proses_tambah_aturan.php" method="POST">
                        <div class="row">
                            <!-- Kolom Penyakit -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="idpenyakit" class="form-label"><b>MAKA:</b> Penyakit (Kesimpulan)</label>
                                    <select class="form-select" id="idpenyakit" name="idpenyakit" required>
                                        <option value="" disabled selected>-- Pilih Penyakit --</option>
                                        <?php foreach ($daftar_penyakit as $penyakit): ?>
                                            <option value="<?= $penyakit['id'] ?>">
                                                (<?= htmlspecialchars($penyakit['kode_penyakit']) ?>) <?= htmlspecialchars($penyakit['penyakit']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="cfpakar" class="form-label"><b>NILAI CF:</b> Bobot/Keyakinan Pakar</label>
                                    <input type="number" step="0.01" min="0" max="1" class="form-control" id="cfpakar" name="cfpakar" placeholder="Contoh: 0.8" required>
                                    <small class="form-text text-muted">Nilai desimal antara 0.01 dan 1.0.</small>
                                </div>
                            </div>
                            <!-- Kolom Gejala -->
                            <div class="col-md-8">
                                <label class="form-label"><b>JIKA:</b> Gejala-Gejala (Syarat)</label>
                                <div class="gejala-select-container">
                                    <?php foreach ($daftar_gejala as $gejala): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="idgejala[]" value="<?= $gejala['idgejala'] ?>" id="gejala_<?= $gejala['idgejala'] ?>">
                                            <label class="form-check-label" for="gejala_<?= $gejala['idgejala'] ?>">
                                                (ID: <?= $gejala['idgejala'] ?>) <?= htmlspecialchars($gejala['nmgejala']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save"></i> Simpan Aturan Baru
                        </button>
                    </form>
                </div>
            </div>

            <!-- Daftar Aturan yang Ada -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list-ul"></i> Daftar Aturan Saat Ini
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">ID Aturan</th>
                                    <th scope="col">Nama Penyakit (Kesimpulan)</th>
                                    <th scope="col">Jumlah Gejala (Syarat)</th>
                                    <th scope="col">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftar_aturan)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data aturan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($daftar_aturan as $aturan): ?>
                                    <tr>
                                        <td>Aturan #<?= htmlspecialchars($aturan['idaturan']) ?></td>
                                        <td>(<?= htmlspecialchars($aturan['kode_penyakit']) ?>) <?= htmlspecialchars($aturan['penyakit']) ?></td>
                                        <td><?= htmlspecialchars($aturan['jumlah_gejala']) ?> gejala</td>
                                        <td>
                                            <!-- Tombol ini mengarah ke halaman edit baru -->
                                            <a href="admin_edit_aturan.php?id=<?= $aturan['idaturan'] ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit Aturan
                                            </a>
                                            <a href="hapus_aturan.php?id=<?= $aturan['idaturan'] ?>" class="btn btn-danger btn-sm btn-delete-aturan">
                                                <i class="fas fa-trash"></i> Hapus Aturan
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div> <!-- .admin-content -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-script.js"></script>
</body>
</html>