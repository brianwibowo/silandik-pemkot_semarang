<?php
session_start();
require '../koneksi.php';
// Anda tidak perlu 'include ../config.php;' jika koneksi sudah di-handle oleh koneksi.php

$error = "";
$showSuccess = false; // flag untuk trigger SweetAlert sukses

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Gunakan filter_input untuk keamanan tambahan
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        $error = "Format email tidak valid!";
    } else {
        // Gunakan prepared statements untuk mencegah SQL Injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Regenerasi session ID untuk keamanan
                session_regenerate_id(true);
                
                // Set session data
                $_SESSION['email'] = $user['email'];
                $_SESSION['username'] = $user['username']; // PENTING: Ditambahkan untuk navbar
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'pengurus' && isset($user['sekolah_id'])) {
                    $_SESSION['sekolah_id'] = $user['sekolah_id'];
                } else {
                    unset($_SESSION['sekolah_id']);
                }
                
                $showSuccess = true; // trigger alert sukses
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Email tidak terdaftar!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Silandik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa 0%, #f0f4f7 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .login-container {
            max-width: 900px;
            width: 100%;
        }

        .login-card {
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        .login-image-panel {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            text-align: center;
        }

        .login-image-panel h2 {
            font-weight: 700;
        }

        .login-image-panel p {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .login-image-panel img {
            max-width: 120px;
            margin-bottom: 1.5rem;
        }

        .login-form-panel {
            padding: 3rem;
            background: #ffffff;
        }

        .form-title {
            font-weight: 600;
            color: #333;
        }

        .form-subtitle {
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom .form-control {
            padding-left: 2.75rem;
            height: 50px;
            border-radius: 0.75rem;
            border: 1px solid #e0e0e0;
        }

        .input-group-custom .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
            border-color: #8a9ff0;
        }

        .input-group-custom .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #adb5bd;
            transition: color 0.2s;
        }

        .input-group-custom .form-control:focus~.input-icon {
            color: #4e73df;
        }

        .btn-login {
            background: linear-gradient(135deg, #4e73df, #224abe);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
        }

        .form-check-label {
            font-size: 0.875rem;
        }

        .footer-link {
            font-size: 0.875rem;
            color: #777;
        }

        .footer-link a {
            color: #4e73df;
            font-weight: 500;
            text-decoration: none;
        }
        .footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="row g-0">
                <div class="col-lg-6 d-none d-lg-flex login-image-panel">
                    <div>
                        <img src="../assets/logo_dinas.png" alt="Logo">
                        <h2>Selamat Datang di SILANDIK</h2>
                        <p>Sistem Layanan Pendidikan Inklusif Terintegrasi untuk masa depan yang lebih baik.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="login-form-panel">
                        <div class="text-center d-lg-none mb-4">
                             <img src="../assets/logo_dinas.png" alt="Logo" width="100">
                        </div>
                        <h3 class="form-title text-center">Login Akun</h3>
                        <p class="form-subtitle text-center">Silakan masukkan email dan password Anda.</p>

                        <form method="POST" action="">
                            <div class="input-group-custom mb-3">
                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="contoh@email.com" required />
                                <i class="fas fa-envelope input-icon"></i>
                            </div>

                            <div class="input-group-custom mb-3">
                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                <i class="fas fa-lock input-icon"></i>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" id="showPassword" type="checkbox" />
                                <label class="form-check-label" for="showPassword">Tampilkan Password</label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-login">Login</button>
                            </div>
                            
                            <div class="text-center">
                                <a href="../index.php" class="btn btn-link text-secondary text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
                                </a>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center footer-link">
                            Belum punya akun? <a href="register.php">Daftar di sini</a>
                        </div>
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

        // SweetAlert untuk notifikasi
        <?php if (!empty($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '<?php echo addslashes($error); ?>',
                confirmButtonColor: '#4e73df'
            });
        <?php endif; ?>

        <?php if ($showSuccess): ?>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                text: 'Selamat datang kembali! Anda akan diarahkan...',
                timer: 2000,
                showConfirmButton: false,
                allowOutsideClick: false
            }).then(() => {
                window.location.href = "../index.php";
            });
        <?php endif; ?>
    </script>
</body>
</html>