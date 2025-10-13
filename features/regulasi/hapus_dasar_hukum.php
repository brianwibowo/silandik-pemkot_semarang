<?php
session_start();
include '../../config.php';
include '../../koneksi.php';

// Verifikasi bahwa user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki akses untuk menghapus regulasi.";
    header("Location: dasar_hukum.php");
    exit;
}

// Verifikasi bahwa ID ada dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID regulasi tidak valid.";
    header("Location: dasar_hukum.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    // Mulai transaksi
    mysqli_begin_transaction($conn);

    // Ambil informasi file sebelum menghapus data
    $query = mysqli_prepare($conn, "SELECT draft_hukum, nomor_regulasi FROM dasar_hukum WHERE id = ?");
    mysqli_stmt_bind_param($query, "i", $id);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        throw new Exception("Regulasi tidak ditemukan.");
    }

    // Hapus file PDF jika ada
    if (!empty($data['draft_hukum'])) {
        $filePath = "../../pdfs/" . basename($data['draft_hukum']);
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                throw new Exception("Gagal menghapus file PDF.");
            }
        }
    }

    // Hapus data dari database
    $stmt = mysqli_prepare($conn, "DELETE FROM dasar_hukum WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal menghapus data dari database.");
    }

    // Commit transaksi
    mysqli_commit($conn);
    
    $_SESSION['success_message'] = "Regulasi " . htmlspecialchars($data['nomor_regulasi']) . " berhasil dihapus.";

} catch (Exception $e) {
    // Rollback jika terjadi error
    mysqli_rollback($conn);
    $_SESSION['error_message'] = $e->getMessage();
} finally {
    if (isset($stmt)) mysqli_stmt_close($stmt);
    if (isset($query)) mysqli_stmt_close($query);
}

header("Location: dasar_hukum.php");
exit;
?>