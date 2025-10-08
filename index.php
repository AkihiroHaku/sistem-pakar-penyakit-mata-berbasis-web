<?php
session_start();
require_once 'includes/db_connect.php';

// Cek status login pengguna
$user_is_logged_in = isset($_SESSION['user_id']);
$username = $user_is_logged_in ? $_SESSION['username'] : '';

// Ambil riwayat analisis jika pengguna sudah login
$riwayat_analisis = [];
$history_error = null;

if ($user_is_logged_in) {
    try {
        // Query untuk mengambil 5 riwayat terakhir
        $sql_history = "SELECT k.persentase_hasil, p.penyakit 
                        FROM konsultasi AS k
                        JOIN penyakit AS p ON k.id_penyakit_hasil = p.id
                        WHERE k.id_user = ? 
                        ORDER BY k.tanggal_analisis DESC 
                        LIMIT 5";

        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->execute([$_SESSION['user_id']]);
        $riwayat_analisis = $stmt_history->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $history_error = "Error saat mengambil riwayat: " . $e->getMessage();
    }
}

// Ambil semua data gejala
try {
    $query = "SELECT idgejala, nmgejala FROM gejala ORDER BY idgejala ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $daftar_gejala = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error Kritis: Tidak bisa mengambil data gejala. " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Penyakit Mata</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
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
                <?php if ($history_error): ?>
                    <p class="history-login-prompt" style="color: red;"><?= htmlspecialchars($history_error) ?></p>
                <?php elseif (!empty($riwayat_analisis)): ?>
                    <?php foreach ($riwayat_analisis as $index => $riwayat): ?>
                        <a href="#" class="sidebar-link">
                            <?= $index + 1 ?>. <?= htmlspecialchars($riwayat['penyakit']) ?> (<?= number_format($riwayat['persentase_hasil'], 0) ?>%)
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="history-login-prompt">Belum ada riwayat analisis.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="history-login-prompt">Masuk untuk melihat riwayat analisis Anda.</p>
            <?php endif; ?>
        </div>

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
                    <a href="#" id="open-logout-modal"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </div>
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
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="gejala-search" placeholder="Cari nama gejala...">
                </div>
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

    <!-- Jendela Modal Pengaturan -->
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
                    <div id="general" class="modal-tab-content active">
                        <h4>General Settings</h4>
                        <p>Pengaturan umum.</p>
                    </div>
                    <div id="profile" class="modal-tab-content">
                        <h4>Profile Information</h4>
                        <p>Informasi profil pengguna.</p>
                    </div>
                    <div id="about" class="modal-tab-content">
                        <h4>About This Application</h4>
                        <p>Deskripsi aplikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <a href="logout.php" class="btn-danger">Ya, Keluar</a>
                    <button id="logout-cancel-btn" class="btn-secondary">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <footer class="text-center text-muted mt-4">@2025 Kelompok 4 | Sistem Pakar Penyakit Mata</footer>
    <script src="js/script.js"></script>
</body>

</html>