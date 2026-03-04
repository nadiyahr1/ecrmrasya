<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Pelanggan') {
    header("Location: profil.php"); 
    exit;
} else {
    header("Location: ../index.php");
    exit;
}