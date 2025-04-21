<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM data_sekolah_inklusi WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: /silandik-semarang/kategori_data/data_sekolah_inklusi.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "ID tidak ditemukan.";
}
?>
