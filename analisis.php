<?php
session_start();
require_once 'includes/db_connect.php'; 

// Cek status login untuk digunakan nanti di tampilan
$user_is_logged_in = isset($_SESSION['user_id']);

// Redirect jika tidak ada data hasil di session
if (!isset($_SESSION['hasil_cf']) || !isset($_SESSION['penyakit_teratas'])) {
    header("Location: index.php?error=no_result"); 
    exit();
}

// Ambil data dari session
$hasil_cf = $_SESSION['hasil_cf'];
$gejala_terpilih = $_SESSION['gejala_terpilih'];
$penyakit_teratas = $_SESSION['penyakit_teratas'];

$persentase_teratas = current($hasil_cf);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisis - Sistem Pakar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/hasil-style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="header-text">
                <h1>Analisis Penyakit Mata Anda</h1>
                <p>Alat bantu cerdas untuk menganalisis kemungkinan penyakit mata berdasarkan gejala</p>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="card diagnosis-card mx-auto">
            <div class="card-body p-4 p-md-5">
                <h2 class="card-title text-center">Kemungkinan Diagnosis</h2>
                <hr class="title-divider mx-auto">

                <div class="result-main text-center my-4">
                    <h3 class="disease-name mb-2"><?= htmlspecialchars($penyakit_teratas['penyakit']) ?></h3>
                    <p class="match-percentage">(Tingkat Kecocokan: <strong><?= number_format($persentase_teratas * 100, 2) ?>%</strong>)</p>
                </div>
                
                <!-- ================================================================= -->
                <!-- === KONTEN DINAMIS BERDASARKAN STATUS LOGIN === -->
                <!-- ================================================================= -->
                <?php if ($user_is_logged_in): ?>
                    <!-- Tampilan untuk pengguna yang sudah login -->
                    <div class="alert alert-success text-center" role="alert">
                        <i class="fas fa-check-circle me-2"></i> Hasil analisis ini telah disimpan ke riwayat Anda.
                    </div>
                <?php else: ?>
                    <!-- Tampilan untuk pengguna tamu -->
                    <div class="alert alert-info text-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i> <a href="signup.php" class="alert-link">Daftar sekarang</a> untuk menyimpan riwayat analisis Anda!
                    </div>
                <?php endif; ?>
                <!-- ================================================================= -->

                <div class="accordion" id="resultDetails">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fas fa-notes-medical me-2"></i> Detail, Deskripsi, dan Solusi
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#resultDetails">
                            <div class="accordion-body">
                                <strong>Deskripsi Penyakit:</strong>
                                <p><?= htmlspecialchars($penyakit_teratas['keterangan']) ?></p>
                                <strong>Rekomendasi Solusi:</strong>
                                <p><?= nl2br(htmlspecialchars($penyakit_teratas['solusi'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="fas fa-clipboard-list me-2"></i> Gejala yang Anda Pilih
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#resultDetails">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush">
                                <?php foreach ($gejala_terpilih as $gejala): ?>
                                    <li class="list-group-item"><?= htmlspecialchars($gejala['nmgejala']) ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <?php if (count($hasil_cf) > 1): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fas fa-chart-pie me-2"></i> Lihat Kemungkinan Lainnya
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#resultDetails">
                            <div class="accordion-body">
                            <?php 
                                array_shift($hasil_cf);
                                foreach ($hasil_cf as $id_penyakit => $persentase): 
                                    $stmt_lain = $conn->prepare("SELECT penyakit FROM penyakit WHERE id = ?");
                                    $stmt_lain->execute([$id_penyakit]);
                                    $penyakit_lain = $stmt_lain->fetch(PDO::FETCH_ASSOC);
                                    $nama_penyakit_lain = $penyakit_lain ? $penyakit_lain['penyakit'] : 'Penyakit Tidak Dikenal';
                            ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span><?= htmlspecialchars($nama_penyakit_lain) ?></span>
                                        <span class="fw-semibold"><?= number_format($persentase * 100, 2) ?>%</span>
                                    </div>
                                    <div class="progress mt-1" style="height: 10px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?= $persentase * 100 ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="disclaimer mt-4">
                    <p class="mb-0"><strong>Disclaimer:</strong> Hasil ini adalah prediksi, bukan diagnosis medis resmi. Selalu konsultasikan dengan dokter profesional.</p>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg">Analisis Ulang</a>
                </div>
            </div>
        </div>
        <footer class="text-center text-muted mt-4">Kelompok 4</footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

