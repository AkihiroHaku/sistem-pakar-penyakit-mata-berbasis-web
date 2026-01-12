<?php
// Pastikan tidak ada spasi, baris kosong, atau teks apapun sebelum tag <?php ini.

// 1. Mulai atau lanjutkan sesi yang sudah ada.
session_start();

// 2. Kosongkan semua variabel di dalam array $_SESSION.
$_SESSION = array();

// 3. Hancurkan cookie yang berkaitan dengan sesi ini.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hancurkan sesi itu sendiri di server.
session_destroy();

// 5. Arahkan pengguna kembali ke halaman utama.
// Perintah ini harus dijalankan sebelum ada output HTML apapun.
header("Location: /pakar/index.php");
exit(); // Hentikan eksekusi skrip setelah redirect.
?>
