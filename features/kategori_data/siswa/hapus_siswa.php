<?php
session_start();
include '../config.php';
// Pengurus juga boleh akses hapus
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header("Location: index.php");
    exit;
}
include '../koneksi.php';
?>

<?php

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $query = "DELETE FROM data_siswa WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: data_siswa.php?success=true");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "ID tidak ditemukan!";
}
?>