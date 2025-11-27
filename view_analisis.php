<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Validasi ID dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: index.php?error=ID tidak valid");
    exit();
}
$id_konsultasi = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // 3. Ambil Data Konsultasi (Penyakit Hasil)
    // Pastikan data ini milik user yang sedang login
    $sql_konsultasi = "SELECT k.*, p.penyakit, p.keterangan, p.solusi 
                       FROM konsultasi k
                       JOIN penyakit p ON k.id_penyakit_hasil = p.id
                       WHERE k.idkonsultasi = ? AND k.id_user = ?";
    $stmt = $conn->prepare($sql_konsultasi);
    $stmt->execute([$id_konsultasi, $user_id]);
    $data_konsultasi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data_konsultasi) {
        header("Location: index.php?error=Riwayat tidak ditemukan.");
        exit();
    }

    // 4. Ambil Detail Gejala yang Dipilih saat itu
    // Kita perlu JOIN dengan tabel gejala untuk dapat namanya
    $sql_detail = "SELECT dk.cf_user, g.nmgejala 
                   FROM detail_konsultasi dk
                   JOIN gejala g ON dk.idgejala = g.idgejala
                   WHERE dk.id_konsultasi = ?";
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->execute([$id_konsultasi]);
    $gejala_terpilih = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Analisis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="css/hasil-style.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Header Sederhana -->
    <header class="main-header">
        <div class="header-content">
            <div class="header-text">
                <h1>Arsip Riwayat Analisis</h1>
                <p>Tanggal: <?= date('d F Y, H:i', strtotime($data_konsultasi['tanggal_analisis'])) ?></p>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="card diagnosis-card mx-auto">
            <div class="card-body p-4 p-md-5">
                <h2 class="card-title text-center">Hasil Diagnosis Tersimpan</h2>
                <div class="accordion" id="resultDetails">
                <!-- TOMBOL CETAK DI ATAS (HEADER) -->
                <button onclick="window.print()" class="btn btn-outline-primary print-header-btn no-print">
                    <i class="fas fa-print me-2"></i> Cetak PDF
                </button>
            </div>

            <div class="card-body p-4 p-md-5">
                <hr class="title-divider mx-auto mt-0">

                <div class="result-main text-center my-4">
                    <h3 class="disease-name mb-2"><?= htmlspecialchars($data_konsultasi['penyakit']) ?></h3>
                    <p class="match-percentage">(Tingkat Kecocokan: <strong><?= number_format($data_konsultasi['persentase_hasil'], 2) ?>%</strong>)</p>
                </div>
                
                <div class="accordion" id="resultDetails">
                    <!-- Detail Penyakit -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" data-print-title="Penjelasan & Solusi"> <!-- Tambahkan data-print-title -->
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                <i class="fas fa-notes-medical me-2"></i> Detail, Deskripsi, dan Solusi
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <strong>Deskripsi Penyakit:</strong>
                                <p><?= htmlspecialchars($data_konsultasi['keterangan']) ?></p>
                                <strong>Rekomendasi Solusi:</strong>
                                <p><?= nl2br(htmlspecialchars($data_konsultasi['solusi'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Gejala yang Dipilih -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" data-print-title="Gejala yang Dialami"> <!-- Tambahkan data-print-title -->
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                <i class="fas fa-clipboard-list me-2"></i> Gejala yang Anda Pilih Saat Itu
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush symptoms-list-result">
                                    <?php foreach ($gejala_terpilih as $gejala): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="symptom-name">
                                                <?= htmlspecialchars($gejala['nmgejala']) ?>
                                            </div>
                                            <div class="symptom-confidence">
                                                <span class="badge bg-primary rounded-pill confidence-badge">
                                                    <?= ($gejala['cf_user'] * 100) ?>% Yakin
                                                </span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg">Kembali ke Menu Utama</a>
                </div>

            </div>
        </div>
    </main>
    <footer class="text-center text-muted mt-4">@2025 Kelompok 4 | Sistem Pakar Penyakit Mata</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>