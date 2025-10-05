<?php
session_start();
require_once 'includes/db_connect.php'; 

// Cek status login pengguna
$user_is_logged_in = isset($_SESSION['user_id']); 
$username = $user_is_logged_in ? $_SESSION['username'] : '';

// Ambil data gejala dari database
try {
    $query = "SELECT idgejala, nmgejala FROM gejala ORDER BY idgejala ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $daftar_gejala = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: Tidak bisa mengambil data gejala. " . $e->getMessage());
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
            <span class="logo">LOGO</span>
        </div>
        <a href="index.php" class="sidebar-link active"><i class="fas fa-plus-circle"></i> NEW ANALISIS</a>
        
        <div class="history-section">
            <span class="history-title">Riwayat Analisis</span>
            <?php if ($user_is_logged_in): ?>
                <!-- Tampilkan riwayat jika user login -->
                <a href="#" class="sidebar-link">1. Hasil: Katarak (95%)</a>
                <a href="#" class="sidebar-link">2. Hasil: Glaukoma (88%)</a>
                <a href="#" class="sidebar-link">3. Hasil: Konjungtivitis (92%)</a>
            <?php else: ?>
                <p class="history-login-prompt">Masuk untuk melihat riwayat analisis Anda.</p>
            <?php endif; ?>
        </div>

        <div class="sidebar-footer">
            <?php if ($user_is_logged_in): ?>
                <a href="profile.php" class="sidebar-link user-profile"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></a>
                <a href="logout.php" class="sidebar-link logout-link"><i class="fas fa-sign-out-alt"></i> Keluar</a>
            <?php else: ?>
                <a href="login.php" class="sidebar-link auth-link"><i class="fas fa-sign-in-alt"></i> Masuk / Daftar</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content-wrapper">
        <header class="main-header">
            <div class="header-content">
                <button id="menu-button" class="menu-button">
                    &#9776; <!-- Ikon hamburger -->
                </button>
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

    <script src="js/script.js"></script>
</body>
</html>

