<?php
session_start();
require '../koneksi.php';

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

        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $showSuccess = true; // trigger alert
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Login - Silandik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/silandik-semarang/css/styles.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-white">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="text-center mb-3">
                    <img src="/silandik-semarang/assets/logo.png" alt="Logo Silandik" style="max-width: 100px;">
                </div>
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" required />
                                <label for="inputEmail">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                <label for="inputPassword">Password</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" id="showPassword" type="checkbox" />
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <small>Ingin mendaftar menjadi Admin? Hubungi operator Anda!</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('showPassword').addEventListener('change', function () {
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
