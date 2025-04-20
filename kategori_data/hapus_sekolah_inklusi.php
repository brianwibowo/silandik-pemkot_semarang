<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM data_sekolah_inklusi WHERE no = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: data_sekolah_inklusi.php");
        exit();
    } else {
        echo "Gagal menghapus data.";
    }
} else {
    echo "ID tidak ditemukan.";
}
?>
