<?php
session_start(); // Selalu mulai session di paling atas

// ==========================================================
// === KODE PENJAGA (ADMIN GUARD) ===
// ==========================================================
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?error=Akses ditolak. Anda bukan admin.");
    exit(); 
}

// === JIKA LOLOS, LANJUTKAN MEMUAT HALAMAN ===
require_once '../includes/db_connect.php';

// Ambil semua gejala dari database untuk ditampilkan
try {
    $query = "SELECT * FROM gejala ORDER BY idgejala ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $daftar_gejala = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error mengambil data gejala: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Kelola Gejala</title>
    <!-- Bootstrap CSS untuk layout cepat -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (untuk ikon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <!-- File CSS Admin KHUSUS (Pastikan path ini benar) -->
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>

    <!-- Sidebar Navigasi Admin -->
    <div class="admin-sidebar">
        <h3 class="sidebar-title">Admin Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="admin_gejala.php">
                    <i class="fas fa-tasks"></i> Kelola Gejala
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_penyakit.php">
                    <i class="fas fa-virus"></i> Kelola Penyakit
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="admin_aturan.php">
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
                <a class="nav-link" href="/pakar/index.php" target="_blank">
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
            <h1 class="admin-title">Manajemen Gejala</h1>

            <!-- Menampilkan Pesan Sukses/Error -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <!-- Kartu untuk Menambah Gejala Baru (ID PENTING) -->
            <div class="card mb-4" id="form-tambah-gejala">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i> Tambah Gejala Publik Baru
                </div>
                <div class="card-body">
                    <form action="../proses/proses_tambah_gejala.php" method="POST">
                        <div class="mb-3">
                            <label for="nmgejala" class="form-label">Nama Gejala</label>
                            <input type="text" class="form-control" id="nmgejala" name="nmgejala" placeholder="Contoh: Mata bengkak di pagi hari" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Gejala
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Edit Gejala (Tersembunyi) (ID PENTING) -->
            <div class="card mb-4" id="form-edit-gejala" style="display: none; background-color: #fffbe6;">
                <div class="card-header text-dark">
                    <i class="fas fa-edit"></i> Edit Gejala
                </div>
                <div class="card-body">
                    <form action="../proses/proses_edit_gejala.php" method="POST">
                        <input type="hidden" id="idgejala_edit" name="idgejala_edit">
                        <div class="mb-3">
                            <label for="nmgejala_edit" class="form-label">Nama Gejala</label>
                            <input type="text" class="form-control" id="nmgejala_edit" name="nmgejala_edit" required>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Gejala
                        </button>
                        <!-- Tombol Batal (ID PENTING) -->
                        <button type="button" id="btn-batal-edit-gejala" class="btn btn-secondary">
                            Batal
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kartu untuk Daftar Gejala yang Ada -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list-ul"></i> Daftar Gejala Saat Ini
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">ID Gejala</th>
                                    <th scope="col">Nama Gejala</th>
                                    <th scope="col">Dibuat Oleh (ID User)</th>
                                    <th scope="col" style="width: 15%;">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($daftar_gejala)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data gejala.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($daftar_gejala as $gejala): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($gejala['idgejala']) ?></td>
                                        <td><?= htmlspecialchars($gejala['nmgejala']) ?></td>
                                        <td>
                                            <?php 
                                            // Menampilkan ID User atau 'Publik'
                                            if ($gejala['id_user'] == NULL) {
                                                echo '<span class="badge bg-info">Publik (Admin)</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary">User: ' . htmlspecialchars($gejala['id_user']) . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <!-- Tombol Edit (CLASS & ATRIBUT DATA PENTING) -->
                                            <button class="btn btn-warning btn-sm btn-edit-gejala"
                                                data-id="<?= $gejala['idgejala'] ?>"
                                                data-nama="<?= htmlspecialchars($gejala['nmgejala']) ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                                <a href="../gejala/hapus_gejala.php?id=<?= $gejala['idgejala'] ?>" class="btn btn-danger btn-sm btn-delete-gejala">
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

    <!-- Memanggil JS Bootstrap dan JS Kustom Admin (Pastikan path ini benar) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/admin-script.js"></script>
</body>
</html>