<?php
session_start();
include '../config.php';
include '../koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header("Location: index.php"); exit;
}

$id = (int) $_GET ['id'];


//hapus forreign key dari data sekolah (id sekolah)
mysqli_query($conn, "DELETE FROM data_siswa WHERE sekolah_id =$id");
mysqli_query($conn, "DELETE FROM galeri WHERE sekolah_id =$id");
mysqli_query($conn, "DELETE FROM rekap WHERE sekolah_id =$id");
mysqli_query($conn, "DELETE FROM prasarana WHERE sekolah_id =$id");

//hapus data sekolalah dari data_sekolah_inklusi
mysqli_query($conn, "DELETE FROM data_sekolah_inklusi WHERE id =$id");


header("location: data_sekolah_inklusi.php");
exit;