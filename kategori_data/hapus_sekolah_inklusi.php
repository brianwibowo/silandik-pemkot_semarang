<?php
session_start();
include '../config.php';
include '../koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header("Location: index.php"); exit;
}


$id = $_GET['id'] ?? 0;

$query = "DELETE FROM data_sekolah_inklusi WHERE id = $id";
if (mysqli_query($conn, $query)) {
    header("Location: data_sekolah_inklusi.php");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
