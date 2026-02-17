<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ecrm_rasya"; // Sesuaikan dengan nama database kamu

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set error mode ke exception agar mudah di-debug
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>