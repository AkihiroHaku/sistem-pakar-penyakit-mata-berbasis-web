<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Validasi Input (Kode Anda sudah benar)
if (!isset($_POST['gejala']) || !is_array($_POST['gejala']) || empty($_POST['gejala'])) {
    header("Location: index.php?error=no_symptoms_selected");
    exit();
}

$gejala_dipilih_ids = $_POST['gejala'];

try {
    // ====================================================================================
    // LANGKAH 2: Ambil Aturan dari Database (BAGIAN INI DIPERBAIKI TOTAL)
    // Kita menggunakan JOIN untuk menggabungkan dua tabel aturan menjadi satu.
    // Ini adalah cara yang benar untuk mendapatkan idpenyakit dan cfpakar
    // untuk setiap gejala yang cocok.
    // ====================================================================================
    
    $placeholders = implode(',', array_fill(0, count($gejala_dipilih_ids), '?'));
    
    $sql_rules = "SELECT 
                    b.idpenyakit, 
                    d.cfpakar 
                  FROM `detail_basis_aturan` AS d
                  JOIN `basis_aturan` AS b ON d.idaturan = b.idaturan
                  WHERE d.idgejala IN ($placeholders)";
                  
    $stmt_rules = $conn->prepare($sql_rules);
    $stmt_rules->execute($gejala_dipilih_ids);
    $aturan = $stmt_rules->fetchAll(PDO::FETCH_ASSOC);

    // Ambil detail gejala yang dipilih untuk ditampilkan di halaman hasil
    $sql_gejala = "SELECT idgejala, nmgejala FROM gejala WHERE idgejala IN ($placeholders)";
    $stmt_gejala = $conn->prepare($sql_gejala);
    $stmt_gejala->execute($gejala_dipilih_ids);
    $gejala_terpilih_detail = $stmt_gejala->fetchAll(PDO::FETCH_ASSOC);

    // 3. Proses Kalkulasi Certainty Factor (CF) - Logika Anda sudah benar, hanya nama kolom disesuaikan
    $cf_penyakit = [];
    foreach ($aturan as $rule) {
        $id_penyakit = $rule['idpenyakit']; // Nama kolom disesuaikan
        $cf_pakar = isset($rule['cfpakar']) ? (float) $rule['cfpakar'] : 0.0; // Nama kolom disesuaikan
        
        $cf_he = $cf_pakar * 1.0; // CF Pengguna diasumsikan 1 (pasti)

        if (!isset($cf_penyakit[$id_penyakit])) {
            $cf_penyakit[$id_penyakit] = $cf_he;
        } else {
            // Kombinasikan CF
            $cf_lama = $cf_penyakit[$id_penyakit];
            if ($cf_lama >= 0 && $cf_he >= 0) {
                $cf_penyakit[$id_penyakit] = $cf_lama + $cf_he * (1 - $cf_lama);
            } elseif ($cf_lama < 0 && $cf_he < 0) {
                $cf_penyakit[$id_penyakit] = $cf_lama + $cf_he * (1 + $cf_lama);
            } else {
                $cf_penyakit[$id_penyakit] = ($cf_lama + $cf_he) / (1 - min(abs($cf_lama), abs($cf_he)));
            }
        }
    }

    // 4. Urutkan Hasil dan Ambil Penyakit Teratas
    if (empty($cf_penyakit)) {
        header("Location: index.php?error=no_rules_matched");
        exit();
    }

    arsort($cf_penyakit);
    $id_penyakit_teratas = key($cf_penyakit);

    // =============================================================================
    // LANGKAH 5: Ambil Detail Penyakit Teratas (Nama kolom diperbaiki)
    // =============================================================================
    $sql_penyakit = "SELECT * FROM penyakit WHERE id = ?";
    $stmt_penyakit = $conn->prepare($sql_penyakit);
    $stmt_penyakit->execute([$id_penyakit_teratas]);
    $penyakit_teratas_detail = $stmt_penyakit->fetch(PDO::FETCH_ASSOC);

    if (!$penyakit_teratas_detail) {
        throw new Exception("Detail penyakit untuk ID $id_penyakit_teratas tidak ditemukan.");
    }

    // 6. Simpan Semua Hasil ke Session
    $_SESSION['hasil_cf'] = $cf_penyakit; 
    $_SESSION['penyakit_teratas'] = $penyakit_teratas_detail;
    $_SESSION['gejala_terpilih'] = $gejala_terpilih_detail;

    // ===================================================================
    // LANGKAH 7: Arahkan ke Halaman Hasil yang Benar
    // ===================================================================
    header("Location: analisis.php");
    exit();

} catch (PDOException $e) {
    die("Error dalam pemrosesan analisis: " . $e->getMessage());
} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

