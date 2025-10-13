<?php
session_start();
include '../../config.php';
include '../../koneksi.php';

// Hanya admin atau pengurus yang boleh menghapus
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header('Location: ../../authentification/login.php');
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
    if (!empty($data['foto']) && file_exists('../../upload/info_sekolah/' . $data['foto'])) {
        unlink('../../upload/info_sekolah/' . $data['foto']);
    }

    // Hapus dari database
    if(mysqli_query($conn, "DELETE FROM info_sekolah_inklusi WHERE id = $id")) {
        $_SESSION['flash_message'] = 'success_delete';
    } else {
        $_SESSION['flash_message'] = 'error_delete';
    }
}

header('Location: informasi_sekolah_inklusi.php');
exit;
?>
