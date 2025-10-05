<?php
// includes/db_connect.php
$host = '127.0.0.1';   
$db_name = 'pakar';
$username = 'root';        
$password = '';             

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    // Set mode error PDO ke exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error dan hentikan skrip
    die("Koneksi ke database gagal: " . $e->getMessage());
}
?>
