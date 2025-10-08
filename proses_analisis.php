<?php
session_start();
require_once 'includes/db_connect.php';

// 1. Validasi Input
if (!isset($_POST['gejala']) || !is_array($_POST['gejala']) || empty($_POST['gejala'])) {
    header("Location: index.php?error=no_symptoms_selected");
    exit();
}

$gejala_dipilih_ids = $_POST['gejala'];
$user_is_logged_in = isset($_SESSION['user_id']);

try {
    // LANGKAH 2: Ambil Aturan dari Database menggunakan JOIN
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

    // LANGKAH 3: Proses Kalkulasi Certainty Factor (CF)
    $cf_penyakit = [];
    foreach ($aturan as $rule) {
        $id_penyakit = $rule['idpenyakit'];
        $cf_pakar = isset($rule['cfpakar']) ? (float) $rule['cfpakar'] : 0.0;
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

    // LANGKAH 4: Urutkan Hasil dan Ambil Penyakit Teratas
    if (empty($cf_penyakit)) {
        header("Location: index.php?error=no_rules_matched");
        exit();
    }

    arsort($cf_penyakit);
    $id_penyakit_teratas = key($cf_penyakit);
    $persentase_teratas = current($cf_penyakit) * 100;

    // LANGKAH 5: Ambil Detail Penyakit Teratas
    $sql_penyakit = "SELECT * FROM penyakit WHERE id = ?"; // Menggunakan kolom 'id' sesuai screenshot terakhir Anda
    $stmt_penyakit = $conn->prepare($sql_penyakit);
    $stmt_penyakit->execute([$id_penyakit_teratas]);
    $penyakit_teratas_detail = $stmt_penyakit->fetch(PDO::FETCH_ASSOC);

    if (!$penyakit_teratas_detail) {
        throw new Exception("Detail penyakit untuk ID $id_penyakit_teratas tidak ditemukan.");
    }
    
    // =====================================================================
    // === LANGKAH 6: SIMPAN HASIL KE RIWAYAT JIKA PENGGUNA SUDAH LOGIN ===
    // =====================================================================
    if ($user_is_logged_in) {
        // Mulai transaksi database untuk memastikan semua query berhasil
        $conn->beginTransaction();
        
        // a. Simpan ke tabel `konsultasi`
        $sql_insert_konsultasi = "INSERT INTO konsultasi (id_user, id_penyakit_hasil, persentase_hasil) VALUES (?, ?, ?)";
        $stmt_konsultasi = $conn->prepare($sql_insert_konsultasi);
        $stmt_konsultasi->execute([
            $_SESSION['user_id'],
            $id_penyakit_teratas,
            $persentase_teratas
        ]);
        
        // Ambil ID dari konsultasi yang baru saja disimpan
        $id_konsultasi_baru = $conn->lastInsertId();
        
        // b. Simpan setiap gejala yang dipilih ke tabel `detail_konsultasi`
        $sql_insert_detail = "INSERT INTO detail_konsultasi (idkonsul, idgejala) VALUES (?, ?)";
        $stmt_detail = $conn->prepare($sql_insert_detail);
        foreach ($gejala_dipilih_ids as $id_gejala) {
            $stmt_detail->execute([$id_konsultasi_baru, $id_gejala]);
        }
        
        // Selesaikan transaksi
        $conn->commit();
    }

    // LANGKAH 7: Simpan Semua Hasil ke Session untuk ditampilkan
    $_SESSION['hasil_cf'] = $cf_penyakit; 
    $_SESSION['penyakit_teratas'] = $penyakit_teratas_detail;
    $_SESSION['gejala_terpilih'] = $gejala_terpilih_detail;

    // LANGKAH 8: Arahkan ke Halaman Hasil
    header("Location: analisis.php");
    exit();

} catch (PDOException $e) {
    // Jika ada error database, batalkan transaksi
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    die("Error dalam pemrosesan analisis: " . $e->getMessage());
} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

