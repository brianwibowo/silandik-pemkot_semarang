<?php
session_start();
require '../koneksi.php';
include '../config.php';

// Redirect jika sudah login
if (isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit;
}

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $role = 'umum'; // default role
    $request_pengurus = isset($_POST['request_pengurus']) ? 1 : 0;

    // Validasi
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif ($password !== $password2) {
        $error = "Konfirmasi password tidak sama!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek email sudah terdaftar dengan prepared statement
        $cek_stmt = mysqli_prepare($conn, "SELECT email FROM users WHERE email = ?");
        mysqli_stmt_bind_param($cek_stmt, "s", $email);
        mysqli_stmt_execute($cek_stmt);
        $cek_result = mysqli_stmt_get_result($cek_stmt);
        
        if (mysqli_num_rows($cek_result) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (email, password, role, request_pengurus) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssi", $email, $hash, $role, $request_pengurus);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = true;
            } else {
                $error = "Gagal mendaftar. Silakan coba lagi.";
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($cek_stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Register - Silandik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="text-center mb-3">
                    <img src="<?= $base_url ?>assets/logo_dinas.png" alt="Logo" width="60" height="40">
                </div>
                <div class="card shadow-lg">
                    <div class="card-header bg-success text-white text-center">
                        <h3>Register</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" />
                                <label for="inputEmail">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required minlength="6" />
                                <label for="inputPassword">Password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPassword2" name="password2" type="password" placeholder="Ulangi Password" required minlength="6" />
                                <label for="inputPassword2">Ulangi Password</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" id="showPassword" type="checkbox" />
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" id="request_pengurus" name="request_pengurus" type="checkbox" value="1" <?= isset($_POST['request_pengurus']) ? 'checked' : '' ?> />
                                <label class="form-check-label" for="request_pengurus">
                                    Ajukan sebagai Pengurus Sekolah (permintaan akan dikonfirmasi admin)
                                </label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-success">Register</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <small>Sudah punya akun? <a href="login.php">Login di sini</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('showPassword').addEventListener('change', function () {
            const pass1 = document.getElementById('inputPassword');
            const pass2 = document.getElementById('inputPassword2');
            const type = this.checked ? 'text' : 'password';
            pass1.type = type;
            pass2.type = type;
        });

        // Alert gagal
        <?php if (!empty($error)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Registrasi Gagal',
            text: '<?php echo addslashes($error); ?>',
            confirmButtonColor: '#198754'
        });
        <?php endif; ?>

        // Alert sukses + redirect
        <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Registrasi Berhasil!',
            text: 'Silakan login menggunakan akun Anda. Jika mengajukan sebagai pengurus sekolah, tunggu konfirmasi admin.',
            confirmButtonColor: '#198754',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = "login.php";
        });
        <?php endif; ?>
    </script>
</body>
</html>