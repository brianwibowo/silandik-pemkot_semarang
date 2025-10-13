<?php
session_start();
include '../../koneksi.php';

// Penyesuaian: pengurus juga boleh hapus berita
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header('Location: ../../authentification/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Hapus gambar jika ada
$data = mysqli_query($conn, "SELECT gambar FROM berita WHERE id = $id");
$berita = mysqli_fetch_assoc($data);
if ($berita && !empty($berita['gambar']) && file_exists("../../upload/berita" . $berita['gambar'])) {
    unlink("../../upload/berita" . $berita['gambar']);
}

// Hapus data berita
mysqli_query($conn, "DELETE FROM berita WHERE id = $id");

header("Location: ../../index.php");
exit;