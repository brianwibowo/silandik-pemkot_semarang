<?php
session_start();
include '../config.php';
include '../koneksi.php';

// Hanya admin atau pengurus yang boleh menghapus
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: informasi_sekolah_inklusi.php');
    exit;
}

// Ambil data untuk menghapus gambar jika ada
$result = mysqli_query($conn, "SELECT foto FROM info_sekolah_inklusi WHERE id = $id");
$data = mysqli_fetch_assoc($result);

if ($data) {
    // Hapus gambar dari folder jika ada
    if (!empty($data['foto']) && file_exists('../uploads/' . $data['foto'])) {
        unlink('../uploads/' . $data['foto']);
    }

    // Hapus dari database
    mysqli_query($conn, "DELETE FROM info_sekolah_inklusi WHERE id = $id");
}

header('Location: informasi_sekolah_inklusi.php');
exit;
?>
