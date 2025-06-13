<?php
session_start();
$_SESSION['role'] = 'umum'; // Ganti role ke umum
$_SESSION['logout_success'] = true; // Tambah flag untuk alert
header("Location: ../index.php");
exit;
?>