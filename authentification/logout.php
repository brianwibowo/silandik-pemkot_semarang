<?php
session_start();
$_SESSION['role'] = 'umum'; // Ganti role ke umum
header("Location: index.php");
exit;
?>
