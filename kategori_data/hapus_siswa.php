<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $query = "DELETE FROM data_siswa WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: /silandik-semarang/kategori_data/data_siswa.php?success=true");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "ID tidak ditemukan!";
}
?>
