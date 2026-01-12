<?php
session_start();
require_once '../includes/db_connect.php';

// Mulai blok try...catch LEBIH AWAL
try {
    // 1. Validasi Input
    if (!isset($_POST['gejala']) || !is_array($_POST['gejala'])) {
        header("Location: index.php?error=no_symptoms_selected");
        exit();
    }

    // 2. Filter gejala yang dipilih pengguna (nilai > 0)
    $gejala_user = $_POST['gejala'];
    $gejala_dipilih = array_filter($gejala_user, function($cf_user) {
        return (float)$cf_user > 0;
    });

    // 3. Validasi Ulang: Pastikan ada gejala yang dipilih
    if (empty($gejala_dipilih)) {
        header("Location: index.php?error=no_symptoms_selected");
        exit();
    }

    // Ambil hanya ID dari gejala yang dipilih
    $gejala_dipilih_ids = array_keys($gejala_dipilih);
    $user_is_logged_in = isset($_SESSION['user_id']);

    // 4. Ambil Aturan dari Database (Sesuai database Anda)
    $placeholders = implode(',', array_fill(0, count($gejala_dipilih_ids), '?'));
    
    $sql_rules = "SELECT 
                    b.idpenyakit,
                    d.idgejala, 
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
    $gejala_terpilih_detail_all = $stmt_gejala->fetchAll(PDO::FETCH_ASSOC);

    // Kita juga perlu menyimpan nilai CF User yang dipilih
    $gejala_terpilih_detail = [];
    foreach($gejala_terpilih_detail_all as $gejala) {
        $gejala['cf_user'] = (float)$gejala_dipilih[$gejala['idgejala']];
        $gejala_terpilih_detail[] = $gejala;
    }


    // 5. Proses Kalkulasi Certainty Factor (CF)
    $cf_penyakit = [];
    foreach ($aturan as $rule) {
        $id_penyakit = $rule['idpenyakit'];
        $id_gejala = $rule['idgejala'];
        $cf_pakar = (float) $rule['cfpakar'];
        
        $cf_user = (float) $gejala_dipilih[$id_gejala]; 
        $cf_he = $cf_pakar * $cf_user;

        if ($cf_he == 0) continue; 

        // INI ADALAH LOGIKA COMBINE YANG MENGATASI BUG ANDA
        if (!isset($cf_penyakit[$id_penyakit])) {
            $cf_penyakit[$id_penyakit] = $cf_he;
        } else {
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

    // 6. Urutkan Hasil dan Ambil Penyakit Teratas
    if (empty($cf_penyakit)) {
        header("Location: index.php?error=no_rules_matched");
        exit();
    }

    arsort($cf_penyakit);
    $id_penyakit_teratas = key($cf_penyakit);
    $persentase_cf_teratas = current($cf_penyakit) * 100; 

    // 7. Ambil Detail Penyakit Teratas (Menggunakan 'id' yang benar)
    $sql_penyakit = "SELECT * FROM penyakit WHERE id = ?";
    $stmt_penyakit = $conn->prepare($sql_penyakit);
    $stmt_penyakit->execute([$id_penyakit_teratas]);
    $penyakit_teratas_detail = $stmt_penyakit->fetch(PDO::FETCH_ASSOC);

    if (!$penyakit_teratas_detail) {
        throw new Exception("Detail penyakit untuk ID $id_penyakit_teratas tidak ditemukan.");
    }
    
    // 8. Simpan Hasil ke Riwayat (jika login)
    if ($user_is_logged_in) {
        $conn->beginTransaction();
        
        $sql_insert_konsultasi = "INSERT INTO konsultasi (id_user, id_penyakit_hasil, persentase_hasil) VALUES (?, ?, ?)";
        $stmt_konsultasi = $conn->prepare($sql_insert_konsultasi);
        $stmt_konsultasi->execute([
            $_SESSION['user_id'],
            $id_penyakit_teratas,
            $persentase_cf_teratas 
        ]);
        
        $id_konsultasi_baru = $conn->lastInsertId();
        
        // Menggunakan 'id_konsultasi' yang benar sesuai .sql
        $sql_insert_detail = "INSERT INTO detail_konsultasi (id_konsultasi, idgejala, cf_user) VALUES (?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_insert_detail);
        foreach ($gejala_dipilih as $id_gejala => $cf_user) {
            $stmt_detail->execute([$id_konsultasi_baru, $id_gejala, (float)$cf_user]);
        }
        
        $conn->commit();
    }

    // 9. Simpan Semua Hasil ke Session untuk ditampilkan
    $_SESSION['hasil_cf'] = $cf_penyakit; 
    $_SESSION['penyakit_teratas'] = $penyakit_teratas_detail;
    $_SESSION['gejala_terpilih'] = $gejala_terpilih_detail; 

    // 10. Arahkan ke Halaman Hasil
    header("Location: /pakar/analisis.php");
    exit();

} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    die("Error dalam pemrosesan analisis: " . $e->getMessage());
} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>