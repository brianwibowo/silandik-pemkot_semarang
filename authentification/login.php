<?php
session_start();
require '../koneksi.php';
include '../config.php';

$success = false;
$error = "";
$showSuccess = false; // flag untuk trigger SweetAlert sukses

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Perbaikan: gunakan $user bukan $email untuk mengakses data dari database
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            // Set session sekolah_id jika role pengurus
            if ($user['role'] === 'pengurus' && isset($user['sekolah_id'])) {
                $_SESSION['sekolah_id'] = $user['sekolah_id'];
            } else {
                unset($_SESSION['sekolah_id']);
            }
            $showSuccess = true; // trigger alert
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Login - Silandik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="text-center mb-3">
                    <img src="../assets/logo_dinas.png" alt="Logo" width="180" height="120">
                </div>
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputemail" name="email" type="text" placeholder="email" required />
                                <label for="inputemail">email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                <label for="inputPassword">Password</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" id="showPassword" type="checkbox" />
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                            <div class="d-flex justify-content-center mb-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        <div class="d-flex justify-content-center">
                            <a href="../index.php" class="btn btn-outline-secondary mt-2">
                                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-center py-3">
                        <small>Belum punya akun? <a href="register.php">Daftar di sini</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('inputPassword');
            passwordInput.type = this.checked ? 'text' : 'password';
        });

        // Alert gagal
        <?php if (!empty($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '<?php echo $error; ?>',
                confirmButtonColor: '#198754'
            });
        <?php endif; ?>

        // Alert sukses + redirect
        <?php if ($showSuccess): ?>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                text: 'Selamat datang kembali!',
                confirmButtonColor: '#198754',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "../index.php";
            });
        <?php endif; ?>
    </script>
</body>

</html>