<?php
session_start();
include '../config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
include '../koneksi.php';
?>
<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM data_sekolah_inklusi WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: data_sekolah_inklusi.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "ID tidak ditemukan.";
}
?>
