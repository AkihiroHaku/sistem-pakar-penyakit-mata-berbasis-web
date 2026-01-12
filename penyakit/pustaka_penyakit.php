<?php
session_start();
require_once '../includes/db_connect.php'; 

// Cek status login (opsional, jika halaman ini untuk umum, hapus cek ini)
$user_is_logged_in = isset($_SESSION['user_id']); 
$username = $user_is_logged_in ? $_SESSION['username'] : '';

// Ambil semua data penyakit
try {
    $query = "SELECT * FROM penyakit ORDER BY penyakit ASC"; // Urutkan abjad
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $daftar_penyakit = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ensiklopedia Penyakit Mata</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="../css/pustaka.css">
    <link rel="stylesheet" href="../css/style.css">
    <!-- CSS Tambahan Khusus Halaman Ini -->
</head>
<body>

    <!-- Sidebar Menu (Sama seperti index.php) -->
 <div id="sidebar-menu" class="sidebar">
        <a href="javascript:void(0)" class="close-btn" id="close-btn">&times;</a>
        <div class="sidebar-header">
            <img src="../assets/images/logomata.jpg" alt="Logo" class="logo-image">
        </div>
        <a href="/pakar/index.php" class="sidebar-link active"><i class="fas fa-plus-circle"></i> NEW ANALISIS</a>
        
        <!-- FOOTER SIDEBAR BARU -->
        <div class="sidebar-footer">
            <?php if ($user_is_logged_in): ?>
                <div class="user-profile-display">
                    <i class="fas fa-user-circle profile-icon"></i>
                    <span class="username-text"><?= htmlspecialchars($username) ?></span>
                    <button id="profile-options-btn" class="profile-options-btn">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
                <!-- Dropdown Menu untuk Opsi Profil -->
                <div id="profile-dropdown" class="profile-dropdown">
                    <a href="#" id="open-settings-modal"><i class="fas fa-cog"></i> Pengaturan</a>
                    <?php
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                        echo '<a href="../admin/admin_gejala.php"><i class="fas fa-tasks"></i> Kelola Gejala</a>';
                        echo '<a href="../admin/admin_penyakit.php"><i class="fas fa-virus"></i> Kelola Penyakit</a>';
                        echo '<a href="../admin/admin_aturan.php"><i class="fas fa-network-wired"></i> Kelola Aturan</a>';
                        echo '<a href="../admin/admin_keyakinan.php"><i class="fas fa-percent"></i> Kelola Keyakinan</a>';
                    }
                    ?>
                    <a href="#" id="open-logout-modal"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </div>
            <?php else: ?>
                <a href="../auth/login.php" class="sidebar-link auth-link"><i class="fas fa-sign-in-alt"></i> Masuk / Daftar</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Main Content -->
    <div id="main-content-wrapper">
        <header class="main-header">
            <div class="header-content">
                <button id="menu-button" class="menu-button">&#9776;</button>
                <div class="header-text">
                    <h1>Ensiklopedia Mata</h1>
                    <p>Pelajari lebih lanjut tentang berbagai jenis penyakit mata</p>
                </div>
            </div>
        </header>

        <main class="pustaka-container">
            
            <!-- Pencarian Penyakit -->
            <div class="pustaka-header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-penyakit" placeholder="Cari nama penyakit...">
                </div>
            </div>

            <!-- Grid Kartu Penyakit -->
            <div class="pustaka-grid" id="penyakit-grid">
                <?php foreach ($daftar_penyakit as $p): ?>
                    <div class="disease-card" onclick="openDiseaseModal('<?= htmlspecialchars($p['id']) ?>')">
                        <div class="disease-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div>
                            <h3 class="disease-title"><?= htmlspecialchars($p['penyakit']) ?></h3>
                            <!-- Menggunakan substr untuk preview teks -->
                            <p class="disease-preview"><?= htmlspecialchars(substr($p['keterangan'], 0, 100)) ?>...</p>
                        </div>
                        <div class="read-more-btn">
                            Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                        </div>
                        
                        <!-- Data Tersembunyi untuk Modal -->
                        <div id="data-<?= htmlspecialchars($p['id']) ?>" style="display:none;">
                            <div class="data-nama"><?= htmlspecialchars($p['penyakit']) ?></div>
                            <div class="data-ket"><?= htmlspecialchars($p['keterangan']) ?></div>
                            <div class="data-solusi"><?= htmlspecialchars($p['solusi']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </main>
        
        <footer class="text-center text-muted mt-4" style="padding-bottom: 20px;">@2025 Kelompok 4 | Sistem Pakar Penyakit Mata</footer>
    </div>

    <!-- Modal Detail Penyakit -->
    <div id="disease-modal" class="modal-overlay">
        <div class="modal-content disease-modal-content">
            <div class="modal-header">
                <h3 id="modal-disease-title"><i class="fas fa-book-medical"></i> Nama Penyakit</h3>
                <button id="disease-modal-close-btn" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body" style="flex-direction: column; padding: 30px;">
                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Pengertian & Gejala Umum</h4>
                    <p id="modal-disease-desc">Deskripsi akan muncul di sini.</p>
                </div>
                <div class="detail-section">
                    <h4><i class="fas fa-user-md"></i> Solusi & Penanganan</h4>
                    <p id="modal-disease-solusi">Solusi akan muncul di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Settings & Logout (Copy dari index.php agar konsisten) -->
    <!-- ... (Masukkan kode modal settings dan logout di sini) ... -->
    
    <!-- Jendela Modal Logout -->
    <div id="logout-modal" class="modal-overlay">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h3><i class="fas fa-sign-out-alt"></i> Konfirmasi Logout</h3>
                <button id="logout-modal-close-btn" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body-centered">
                <p>Apakah Anda yakin ingin keluar?</p>
                <div class="logout-actions">
                    <a href="../auth/logout.php" class="btn-danger">Ya, Keluar</a>
                    <button id="logout-cancel-btn" class="btn-secondary">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
    
    <!-- Script Khusus Halaman Ini -->
</body>
</html>