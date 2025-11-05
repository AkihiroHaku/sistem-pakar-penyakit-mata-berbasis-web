<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Proteksi Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak");
    exit();
}

// 2. Ambil semua data penyakit untuk ditampilkan di tabel
try {
    $query = "SELECT * FROM penyakit ORDER BY id ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $daftar_penyakit = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error mengambil data penyakit: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Kelola Penyakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/admin-style.css">
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
                <a class="nav-link active" href="admin_penyakit.php">
                    <i class="fas fa-virus"></i> Kelola Penyakit
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="admin_aturan.php">
                    <i class="fas fa-network-wired"></i> Kelola Aturan
                </a>
            </li>
            <li class="nav-item-divider"></li>
            <li class="nav-item">
                <a class="nav-link" href="index.php" target="_blank">
                    <i class="fas fa-globe"></i> Lihat Situs Publik
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </li>
        </ul>
    </div>

    <!-- Konten Utama Halaman Admin -->
    <div class="admin-content">
        <div class="container-fluid">
            <h1 class="admin-title">Manajemen Penyakit</h1>

            <!-- Menampilkan Pesan Sukses/Error -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <!-- Form Tambah Penyakit -->
            <div class="card mb-4" id="form-tambah-penyakit">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i> Tambah Penyakit Baru
                </div>
                <div class="card-body">
                    <form action="proses_tambah_penyakit.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="kode_penyakit" class="form-label">Kode Penyakit</label>
                                <input type="text" class="form-control" id="kode_penyakit" name="kode_penyakit" placeholder="Contoh: P021" required>
                            </div>
                            <div class="col-md-10">
                                <label for="nmpenyakit" class="form-label">Nama Penyakit</label>
                                <input type="text" class="form-control" id="nmpenyakit" name="nmpenyakit" placeholder="Contoh: Mata Kering" required>
                            </div>
                            <div class="col-12">
                                <label for="keterangan" class="form-label">Keterangan/Deskripsi</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label for="solusi" class="form-label">Solusi</label>
                                <textarea class="form-control" id="solusi" name="solusi" rows="3" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save"></i> Simpan Penyakit Baru
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Edit Penyakit (Tersembunyi) -->
            <div class="card mb-4" id="form-edit-penyakit" style="display: none; background-color: #fffbe6;">
                <div class="card-header text-dark">
                    <i class="fas fa-edit"></i> Edit Penyakit
                </div>
                <div class="card-body">
                    <form action="proses_edit_penyakit.php" method="POST">
                        <!-- ID Penyakit yang sedang di-edit akan disimpan di sini -->
                        <input type="hidden" id="id_penyakit_edit" name="id_penyakit_edit">
                        
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="kode_penyakit_edit" class="form-label">Kode Penyakit</label>
                                <input type="text" class="form-control" id="kode_penyakit_edit" name="kode_penyakit_edit" required>
                            </div>
                            <div class="col-md-10">
                                <label for="nmpenyakit_edit" class="form-label">Nama Penyakit</label>
                                <input type="text" class="form-control" id="nmpenyakit_edit" name="nmpenyakit_edit" required>
                            </div>
                            <div class="col-12">
                                <label for="keterangan_edit" class_exists="form-label">Keterangan/Deskripsi</label>
                                <textarea class="form-control" id="keterangan_edit" name="keterangan_edit" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label for="solusi_edit" class="form-label">Solusi</label>
                                <textarea class="form-control" id="solusi_edit" name="solusi_edit" rows="3" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning mt-3">
                            <i class="fas fa-save"></i> Update Data
                        </button>
                        <button type="button" id="btn-batal-edit" class="btn btn-secondary mt-3">
                            Batal
                        </button>
                    </form>
                </div>
            </div>

            <!-- Daftar Penyakit -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list-ul"></i> Daftar Penyakit Saat Ini
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Nama Penyakit</th>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftar_penyakit)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data penyakit.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($daftar_penyakit as $penyakit): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($penyakit['id']) ?></td>
                                        <td><?= htmlspecialchars($penyakit['kode_penyakit']) ?></td>
                                        <td><?= htmlspecialchars($penyakit['penyakit']) ?></td>
                                        <td><?= htmlspecialchars(substr($penyakit['keterangan'], 0, 75)) ?>...</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btn-edit-penyakit"
                                                data-id="<?= $penyakit['id'] ?>"
                                                data-kode="<?= htmlspecialchars($penyakit['kode_penyakit']) ?>"
                                                data-nama="<?= htmlspecialchars($penyakit['penyakit']) ?>"
                                                data-keterangan="<?= htmlspecialchars($penyakit['keterangan']) ?>"
                                                data-solusi="<?= htmlspecialchars($penyakit['solusi']) ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="hapus_penyakit.php?id=<?= $penyakit['id'] ?>" class="btn btn-danger btn-sm btn-delete-penyakit">
                                                <i class="fas fa-trash"></i> Hapus
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