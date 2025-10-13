<?php
// request_pengurus.php
session_start();

// Debug mode - uncomment untuk debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Include koneksi database
include_once '../koneksi.php';

// Log untuk debugging
error_log("Request pengurus script accessed - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Session data: " . print_r($_SESSION, true));

// Cek apakah user login dan memiliki role umum
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'umum') {
    error_log("User not authorized - redirecting to login");
    $_SESSION['error_message'] = "Anda harus login sebagai user umum untuk mengakses fitur ini.";
    header("Location: /authentification/login.php");
    exit();
}

// Cek apakah request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Not POST request - redirecting to index");
    header("Location: /index.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);

// Cek apakah user sudah pernah request sebelumnya
$check_existing = mysqli_query($conn, "SELECT request_pengurus FROM users WHERE email='$email'");
if (!$check_existing) {
    error_log("Database error when checking existing request: " . mysqli_error($conn));
    $_SESSION['error_message'] = "Error: Tidak dapat mengecek status request. " . mysqli_error($conn);
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/index.php'));
    exit();
}

if (mysqli_num_rows($check_existing) === 0) {
    error_log("User not found in database: " . $email);
    $_SESSION['error_message'] = "Error: User tidak ditemukan dalam database.";
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/index.php'));
    exit();
}

$user_data = mysqli_fetch_assoc($check_existing);
if ($user_data['request_pengurus'] == 1) {
    error_log("User already has pending request");
    $_SESSION['error_message'] = "Anda sudah memiliki request yang sedang pending.";
    header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/index.php'));
    exit();
}

// Update request_pengurus menjadi 1
$update_query = mysqli_query($conn, "UPDATE users SET request_pengurus = 1 WHERE email='$email'");

if ($update_query) {
    error_log("Request pengurus successfully updated for user: " . $email);
    $_SESSION['success_message'] = "Request pengurus berhasil dikirim! Menunggu persetujuan admin.";
} else {
    error_log("Database error when updating request: " . mysqli_error($conn));
    $_SESSION['error_message'] = "Error: Gagal mengirim request. " . mysqli_error($conn);
}

// Redirect kembali ke halaman sebelumnya atau ke index
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/index.php';
header("Location: " . $redirect_url);
exit();
?>