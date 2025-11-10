<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// Ambil semua data keyakinan
try {
    $stmt = $conn->prepare("SELECT * FROM cf_keyakinan ORDER BY nilai ASC");
    $stmt->execute();
    $daftar_keyakinan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error mengambil data keyakinan: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Kelola Keyakinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>

    <!-- Sidebar Navigasi Admin -->
    <div class="admin-sidebar">
        <h3 class="sidebar-title">Admin Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="admin_gejala.php"><i class="fas fa-tasks"></i> Kelola Gejala</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_penyakit.php"><i class="fas fa-virus"></i> Kelola Penyakit</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_aturan.php"><i class="fas fa-network-wired"></i> Kelola Aturan</a></li>
            <li class="nav-item"><a class="nav-link active" href="admin_keyakinan.php"><i class="fas fa-percent"></i> Kelola Keyakinan</a></li>
            <li class="nav-item-divider"></li>
            <li class="nav-item"><a class="nav-link" href="index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Situs Publik</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
        </ul>
    </div>

    <!-- Konten Utama Halaman Admin -->
    <div class="admin-content">
        <div class="container-fluid">
            <h1 class="admin-title">Manajemen Nilai Keyakinan (CF User)</h1>
            <p class="text-muted">Nilai-nilai ini akan muncul di dropdown pada halaman analisis utama.</p>

            <!-- Menampilkan Pesan Sukses/Error -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <!-- Form Tambah/Edit -->
            <div class="card mb-4" id="form-tambah-keyakinan">
                <div class="card-header"><i class="fas fa-plus-circle"></i> Tambah Nilai Keyakinan Baru</div>
                <div class="card-body">
                    <form action="proses_tambah_keyakinan.php" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="label" class="form-label">Label Tampilan</label>
                                <input type="text" class="form-control" id="label" name="label" placeholder="Contoh: Setengah Yakin" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nilai" class="form-label">Nilai CF (0.01 - 1.00)</label>
                                <input type="number" step="0.01" min="0.01" max="1.00" class="form-control" id="nilai" name="nilai" placeholder="Contoh: 0.5" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Simpan</button>
                    </form>
                </div>
            </div>

            <!-- Form Edit (Tersembunyi) -->
            <div class="card mb-4" id="form-edit-keyakinan" style="display: none; background-color: #fffbe6;">
                <div class="card-header text-dark"><i class="fas fa-edit"></i> Edit Nilai Keyakinan</div>
                <div class="card-body">
                    <form action="proses_edit_keyakinan.php" method="POST">
                        <input type="hidden" id="id_keyakinan_edit" name="id_keyakinan_edit">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="label_edit" class="form-label">Label Tampilan</label>
                                <input type="text" class="form-control" id="label_edit" name="label_edit" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nilai_edit" class="form-label">Nilai CF (0.01 - 1.00)</label>
                                <input type="number" step="0.01" min="0.01" max="1.00" class="form-control" id="nilai_edit" name="nilai_edit" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning mt-3"><i class="fas fa-save"></i> Update</button>
                        <button type="button" id="btn-batal-edit-keyakinan" class="btn btn-secondary mt-3">Batal</button>
                    </form>
                </div>
            </div>

            <!-- Daftar Nilai Keyakinan -->
            <div class="card">
                <div class="card-header"><i class="fas fa-list-ul"></i> Daftar Nilai Keyakinan Saat Ini</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Label Tampilan</th>
                                    <th scope="col">Nilai CF</th>
                                    <th scope="col">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daftar_keyakinan as $keyakinan): ?>
                                    <?php if ($keyakinan['nilai'] == 0.00) continue; // Jangan tampilkan "Tidak Mengalami" ?>
                                    <tr>
                                        <td><?= htmlspecialchars($keyakinan['label']) ?></td>
                                        <td><?= htmlspecialchars(number_format($keyakinan['nilai'], 2)) ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btn-edit-keyakinan"
                                                data-id="<?= $keyakinan['id'] ?>"
                                                data-label="<?= htmlspecialchars($keyakinan['label']) ?>"
                                                data-nilai="<?= htmlspecialchars($keyakinan['nilai']) ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="hapus_keyakinan.php?id=<?= $keyakinan['id'] ?>" class="btn btn-danger btn-sm btn-delete-keyakinan">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-script.js"></script>
</body>
</html>