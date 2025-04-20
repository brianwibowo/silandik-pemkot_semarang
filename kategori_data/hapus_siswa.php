<?php
include '../koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM data_siswa WHERE id=$id");

header("Location: data_siswa.php");
?>
