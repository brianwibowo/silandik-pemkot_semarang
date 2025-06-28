<?php
session_start();
require '../koneksi.php';
include '../config.php';

// Cek apakah user sudah login dan role user
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

$email = $_SESSION['email'];
$success = false;
$error = "";

// Hanya proses jika POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Cek apakah user sudah punya pending request
    $check_query = "SELECT request_pengurus FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if ($user['request_pengurus'] == 1) {
            $error = "Anda sudah memiliki permintaan yang sedang menunggu persetujuan admin.";
        } else {
            // Update request_pengurus menjadi 1
            $update_query = "UPDATE users SET request_pengurus = 1 WHERE email = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "s", $email);
            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['request_pengurus_success'] = true;
                header("Location: ../index.php");
                exit;
            } else {
                $error = "Gagal mengirim permintaan. Silakan coba lagi.";
            }
            mysqli_stmt_close($update_stmt);
        }
    } else {
        $error = "Data user tidak ditemukan.";
    }
    mysqli_stmt_close($stmt);
}

// Jika akses langsung GET, redirect ke index
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}
?>