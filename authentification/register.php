<?php
session_start();
require '../koneksi.php';

// Redirect jika sudah login
if (isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit;
}

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Ambil dan sanitasi data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $request_pengurus = isset($_POST['request_pengurus']) ? 1 : 0;
    $role = 'umum';

    // 2. Validasi data
    if (empty($email)) {
        $error = "Email tidak boleh kosong!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Validasi format email yang benar
        $error = "Format email yang Anda masukkan tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal harus 6 karakter!";
    } elseif ($password !== $password2) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        // 3. Cek apakah email sudah terdaftar menggunakan prepared statements
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email ini sudah terdaftar. Silakan gunakan email lain.";
        } else {
            // 4. Hash password dan masukkan data ke database
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Menggunakan username dari bagian sebelum @ di email
            $username = explode('@', $email)[0];

            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role, request_pengurus) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssssi", $username, $email, $hash, $role, $request_pengurus);

            if ($insert_stmt->execute()) {
                $success = true;
            } else {
                $error = "Terjadi kesalahan pada server. Gagal mendaftar.";
            }
            $insert_stmt->close();
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
    <title>Register - Silandik</title>
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

        .register-container {
            max-width: 900px;
            width: 100%;
        }

        .register-card {
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        .register-image-panel {
            background: linear-gradient(135deg, #20bf6b, #0bb7af); /* Green Theme */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            text-align: center;
        }
        
        .register-image-panel h2 {
            font-weight: 700;
        }

        .register-image-panel p {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .register-image-panel img {
            max-width: 120px;
            margin-bottom: 1.5rem;
        }

        .register-form-panel {
            padding: 2.5rem; /* Slightly less padding for more fields */
            background: #ffffff;
        }

        .form-title {
            font-weight: 600;
            color: #333;
        }
        
        .form-subtitle {
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
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
            box-shadow: 0 0 0 0.25rem rgba(28, 200, 138, 0.25); /* Green Focus */
            border-color: #53d4a1;
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
            color: #1cc88a;
        }

        .btn-register {
            background: linear-gradient(135deg, #1cc88a, #13855c); /* Green Gradient */
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.4);
        }

        .form-check-label {
            font-size: 0.875rem;
            color: #555;
        }

        .footer-link {
            font-size: 0.875rem;
            color: #777;
        }
        
        .footer-link a {
            color: #1cc88a; /* Green Link */
            font-weight: 500;
            text-decoration: none;
        }
        
        .footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="card register-card">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="register-form-panel">
                        <div class="text-center d-lg-none mb-4">
                             <img src="../assets/logo_dinas.png" alt="Logo" width="100">
                        </div>
                        <h3 class="form-title text-center">Buat Akun Baru</h3>
                        <p class="form-subtitle text-center">Isi data di bawah untuk mendaftar.</p>

                        <form method="POST" action="">
                            <div class="input-group-custom mb-3">
                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="Alamat Email Anda" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" />
                                <i class="fas fa-envelope input-icon"></i>
                            </div>

                            <div class="input-group-custom mb-3">
                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Buat Password" required minlength="6" />
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            
                            <div class="input-group-custom mb-3">
                                <input class="form-control" id="inputPassword2" name="password2" type="password" placeholder="Konfirmasi Password" required minlength="6" />
                                <i class="fas fa-check-double input-icon"></i>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" id="showPassword" type="checkbox" />
                                <label class="form-check-label" for="showPassword">Tampilkan Password</label>
                            </div>
                            
                            <div class="form-check mb-4 p-3 border rounded bg-light">
                                <input class="form-check-input" type="checkbox" name="request_pengurus" id="request_pengurus" value="1" <?= isset($_POST['request_pengurus']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="request_pengurus">
                                    <strong>Ajukan sebagai Pengurus Sekolah</strong><br>
                                    <small class="text-muted">Centang jika Anda adalah perwakilan sekolah. Permintaan akan diverifikasi oleh Admin.</small>
                                </label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-register">Daftar Sekarang</button>
                            </div>
                        </form>

                        <hr class="my-3">

                        <div class="text-center footer-link">
                            Sudah punya akun? <a href="login.php">Login di sini</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 d-none d-lg-flex register-image-panel">
                    <div>
                        <img src="../assets/logo_dinas.png" alt="Logo">
                        <h2>Bergabung dengan SILANDIK</h2>
                        <p>Daftarkan diri Anda untuk mendapatkan akses penuh ke semua fitur layanan pendidikan inklusif.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility untuk kedua field
        document.getElementById('showPassword').addEventListener('change', function () {
            const pass1 = document.getElementById('inputPassword');
            const pass2 = document.getElementById('inputPassword2');
            const type = this.checked ? 'text' : 'password';
            pass1.type = type;
            pass2.type = type;
        });

        // SweetAlert untuk notifikasi
        <?php if (!empty($error)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Registrasi Gagal',
            text: '<?php echo addslashes($error); ?>',
            confirmButtonColor: '#1cc88a'
        });
        <?php endif; ?>

        <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Registrasi Berhasil!',
            html: 'Akun Anda telah dibuat. Silakan login untuk melanjutkan.<br><small>Jika Anda mendaftar sebagai pengurus, mohon tunggu konfirmasi dari admin.</small>',
            confirmButtonColor: '#1cc88a',
            confirmButtonText: 'Lanjut ke Halaman Login'
        }).then(() => {
            window.location.href = "login.php";
        });
        <?php endif; ?>
    </script>
</body>
</html>