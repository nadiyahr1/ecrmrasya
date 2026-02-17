<?php
session_start();
session_destroy(); // Menghapus semua data login
header("Location: index.php"); // Kembali ke halaman login
exit;