<?php
$host = "localhost";
$user = "u655368359_root";
$pass = "Ddilan1990123";
$db   = "u655368359_silandik";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>