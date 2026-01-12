<?php
session_start();
require_once '../includes/db_connect.php';

// Atur header untuk merespons sebagai JSON
header('Content-Type: application/json');

// Siapkan array untuk respons
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

// 1. Cek jika pengguna login
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Akses ditolak. Anda tidak login.';
    echo json_encode($response);
    exit();
}

// 2. Cek jika data dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['old_password'], $_POST['new_password'], $_POST['confirm_new_password'])) {
        $response['message'] = 'Semua field harus diisi.';
        echo json_encode($response);
        exit();
    }

    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    $user_id = $_SESSION['user_id'];

    // 3. Validasi input
    if (empty($old_password) || empty($new_password) || empty($confirm_new_password)) {
        $response['message'] = 'Semua field tidak boleh kosong.';
        echo json_encode($response);
        exit();
    }
    if (strlen($new_password) < 8) {
        $response['message'] = 'Password baru harus minimal 8 karakter.';
        echo json_encode($response);
        exit();
    }
    if ($new_password !== $confirm_new_password) {
        $response['message'] = 'Password baru dan konfirmasi tidak cocok.';
        echo json_encode($response);
        exit();
    }

    try {
        // 4. Verifikasi password lama
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($old_password, $user['password'])) {
            $response['message'] = 'Password lama yang Anda masukkan salah.';
            echo json_encode($response);
            exit();
        }

        // 5. Hash dan update password baru
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt_update->execute([$new_password_hash, $user_id]);

        // 6. Kirim respons sukses
        $response['status'] = 'success';
        $response['message'] = 'Password Anda telah berhasil diperbarui!';
        echo json_encode($response);
        exit();

    } catch (PDOException $e) {
        $response['message'] = 'Error database: ' . $e->getMessage();
        echo json_encode($response);
        exit();
    }

} else {
    $response['message'] = 'Metode request tidak valid.';
    echo json_encode($response);
    exit();
}
?>