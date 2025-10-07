<?php
session_start();
require_once 'includes/db_connect.php'; 

// Cek status login pengguna
$user_is_logged_in = isset($_SESSION['user_id']); 
$username = $user_is_logged_in ? $_SESSION['username'] : '';

// ======================================================
// === AMBIL RIWAYAT ANALISIS JIKA PENGGUNA SUDAH LOGIN ===
// ======================================================
$riwayat_analisis = []; // Siapkan array kosong untuk menampung riwayat
if ($user_is_logged_in) {
    try {
        // Query untuk mengambil 5 riwayat terakhir dari pengguna yang sedang login
        $sql_history = "SELECT k.persentase_hasil, p.nmpenyakit 
                        FROM konsultasi AS k
                        JOIN penyakit AS p ON k.id_penyakit_hasil = p.idpenyakit
                        WHERE k.id_user = ? 
                        ORDER BY k.tanggal_analisis DESC 
                        LIMIT 5"; 
        
        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->execute([$_SESSION['user_id']]);
        $riwayat_analisis = $stmt_history->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Jika terjadi error saat mengambil riwayat, biarkan saja
    }
}

// ===============================================
// === AMBIL SEMUA DATA GEJALA DARI DATABASE ===
// ===============================================
try {
    $query = "SELECT idgejala, nmgejala FROM gejala ORDER BY idgejala ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $daftar_gejala = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error Kritis: Tidak bisa mengambil data gejala dari database. " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Penyakit Mata</title>
    <!-- Font Awesome (untuk ikon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Sidebar Menu -->
    <div id="sidebar-menu" class="sidebar">
        <a href="javascript:void(0)" class="close-btn" id="close-btn">&times;</a>
        <div class="sidebar-header">
            <img src="assets/images/logomata.jpg" alt="Logo" class="logo-image">
        </div>
        <a href="index.php" class="sidebar-link active"><i class="fas fa-plus-circle"></i> NEW ANALISIS</a>
        
        <div class="history-section">
            <span class="history-title">Riwayat Analisis</span>
            <?php if ($user_is_logged_in): ?>
                <?php if (!empty($riwayat_analisis)): ?>
                    <!-- Jika ada riwayat, tampilkan di sini -->
                    <?php foreach ($riwayat_analisis as $index => $riwayat): ?>
                        <a href="#" class="sidebar-link">
                            <?= $index + 1 ?>. <?= htmlspecialchars($riwayat['nmpenyakit']) ?> (<?= number_format($riwayat['persentase_hasil'], 0) ?>%)
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="history-login-prompt">Belum ada riwayat analisis.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="history-login-prompt">Masuk untuk melihat riwayat analisis Anda.</p>
            <?php endif; ?>
        </div>

        <div class="sidebar-footer">
            <?php if ($user_is_logged_in): ?>
                <!-- Tombol ini akan membuka modal -->
                <a href="#" class="sidebar-link user-profile" id="open-settings-modal"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></a>
                <a href="logout.php" class="sidebar-link logout-link"><i class="fas fa-sign-out-alt"></i> Keluar</a>
            <?php else: ?>
                <a href="login.php" class="sidebar-link auth-link"><i class="fas fa-sign-in-alt"></i> Masuk / Daftar</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div id="main-content-wrapper">
        <header class="main-header">
            <div class="header-content">
                <button id="menu-button" class="menu-button">&#9776;</button>
                <div class="header-text">
                    <h1>Analisis Penyakit Mata Anda</h1>
                    <p>Alat bantu cerdas untuk menganalisis kemungkinan penyakit mata berdasarkan gejala</p>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="card">
                <h2>Pilih gejala yang anda alami</h2>
                <form action="proses_analisis.php" method="POST">
                    <div class="symptoms-list">
                        <?php if (count($daftar_gejala) > 0): ?>
                            <?php foreach ($daftar_gejala as $gejala): ?>
                                <div class="symptom-item">
                                    <label for="gejala<?= $gejala['idgejala'] ?>"><?= htmlspecialchars($gejala['nmgejala']) ?></label>
                                    <input type="checkbox" id="gejala<?= $gejala['idgejala'] ?>" name="gejala[]" value="<?= $gejala['idgejala'] ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Tidak ada data gejala yang tersedia.</p>
                        <?php endif; ?>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="btn-gradient">Analisis Sekarang</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- ============================================== -->
    <!-- === STRUKTUR HTML UNTUK JENDELA MODAL PENGATURAN === -->
    <!-- ============================================== -->
    <div id="settings-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-cog"></i> Setting</h3>
                <button id="modal-close-btn" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-sidebar">
                    <a href="#" class="modal-tab-link active" data-tab="general"><i class="fas fa-sliders-h"></i> General</a>
                    <a href="#" class="modal-tab-link" data-tab="profile"><i class="fas fa-user"></i> Profile</a>
                    <a href="#" class="modal-tab-link" data-tab="about"><i class="fas fa-info-circle"></i> About</a>
                </div>
                <div class="modal-main-content">
                    <!-- Konten Tab General -->
                    <div id="general" class="modal-tab-content active">
                        <h4>General Settings</h4>
                        <p>Pengaturan umum seperti tema dan bahasa akan ditampilkan di sini.</p>
                    </div>
                    <!-- Konten Tab Profile -->
                    <div id="profile" class="modal-tab-content">
                        <h4>Profile Information</h4>
                        <p>Informasi profil pengguna seperti nama, email, dan opsi log out akan ada di sini.</p>
                    </div>
                    <!-- Konten Tab About -->
                    <div id="about" class="modal-tab-content">
                        <h4>About This Application</h4>
                        <p>Ini adalah sistem pakar untuk mendiagnosis penyakit mata berdasarkan gejala yang dipilih pengguna.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>

