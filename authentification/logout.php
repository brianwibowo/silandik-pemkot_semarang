<?php
session_start();

// Hapus semua data session
session_unset();

// Hancurkan session
session_destroy();

// Regenerate session ID untuk keamanan ekstra
session_regenerate_id(true);

// Redirect ke login dengan pesan sukses
header("Location: login.php?logout=success");
exit;
?>