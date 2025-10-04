<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Penyakit Mata</title>
    <!-- Menghubungkan ke file CSS untuk styling -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div id="analysis-page">
        <!-- Header dengan tombol menu -->
        <header class="main-header">
            <div class="header-content">
                <button id="menu-button" class="menu-button">
                    &#9776; <!-- Ini adalah karakter HTML untuk ikon hamburger -->
                </button>
                <div class="header-text">
                    <h1>Analisis Penyakit Mata Anda</h1>
                    <p>Alat bantu cerdas untuk menganalisis kemungkinan penyakit mata berdasarkan gejala</p>
                </div>
            </div>
            <!-- Menu dropdown yang akan muncul saat tombol diklik -->
            <div id="mobile-menu" class="mobile-menu">
                <a href="login.php">Login</a>
            </div>
        </header>

        <!-- Konten utama untuk memilih gejala -->
        <main class="main-content">
            <div class="card">
                <h2>Pilih gejala yang anda alami</h2>
                <form action="hasil_analisis.php" method="POST">
                    <div class="symptoms-list">
                        <!-- Contoh Gejala -->
                        <div class="symptom-item">
                            <label for="gejala1">Pandangan Mata Samar/Kabur</label>
                            <input type="checkbox" id="gejala1" name="gejala[]" value="1">
                        </div>
                        <div class="symptom-item">
                            <label for="gejala2">Terlihat lapisan kuning atau coklat pada mata</label>
                            <input type="checkbox" id="gejala2" name="gejala[]" value="2">
                        </div>
                        <div class="symptom-item">
                            <label for="gejala3">Pandangan mata nampak berwarna kekuningan</label>
                            <input type="checkbox" id="gejala3" name="gejala[]" value="3">
                        </div>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="btn-gradient">Analisis Sekarang</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Menghubungkan ke file JavaScript untuk fungsionalitas menu -->
    <script src="js/script.js"></script>
</body>
</html>

