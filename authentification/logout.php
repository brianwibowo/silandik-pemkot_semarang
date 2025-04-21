<?php
session_start();
$_SESSION['role'] = 'umum'; // Ganti role ke umum
header("Location: /silandik-semarang/index.php");
exit;
?>
