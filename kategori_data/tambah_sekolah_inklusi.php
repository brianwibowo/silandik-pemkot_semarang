<?php include '../partials/head.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include '../koneksi.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_sekolah = htmlspecialchars($_POST['nama_sekolah']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);

    $targetDir = __DIR__ . '/../upload/';
    $fileName = basename($_FILES["logo"]["name"]);
    $targetFilePath = $targetDir . $fileName;  
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    $allowTypes = ['jpg', 'jpeg', 'png'];
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
            $query = "INSERT INTO data_sekolah_inklusi (nama_sekolah, logo_sekolah, deskripsi) 
                      VALUES ('$nama_sekolah', '$fileName', '$deskripsi')";
            mysqli_query($conn, $query);

            echo "
            <script>
                Swal.fire({
                    title: 'Sukses!',
                    text: 'Data berhasil disimpan',
                    icon: 'success',
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '/silandik-semarang/kategori_data/data_sekolah_inklusi.php';
                });
            </script>";
            exit();
        } else {
            $error = "Gagal mengunggah logo sekolah.";
        }
    } else {
        $error = "Format file logo tidak didukung. Gunakan JPG, JPEG, atau PNG.";
    }
}
?>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="/silandik-semarang/index.php">
            <img src="/silandik-semarang/logo_dinas.png" alt="Logo" width="50" height="40">SILANDIK
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/silandik-semarang/authentification/login.php">Login</a></li>
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="#">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <?php include '../sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Tambah Data Sekolah Inklusi</h1>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-school me-1"></i>
                            Form Tambah Data Sekolah Inklusi
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error; ?></div>
                            <?php endif; ?>

                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
                                    <input type="text" name="nama_sekolah" class="form-control" id="nama_sekolah" required>
                                </div>

                                <div class="mb-3">
                                    <label for="logo" class="form-label">Logo Sekolah</label>
                                    <input type="file" name="logo" class="form-control" id="logo" accept="image/*" onchange="previewImage(event)" required>
                                    <div id="logoPreviewContainer" class="mt-3" style="display: none;">
                                        <h6>Preview Logo:</h6>
                                        <img id="logoPreview" src="" alt="Logo Preview" style="width: 150px; height: auto; border-radius: 5px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" id="deskripsi" rows="4" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button>
                                <a href="/silandik-semarang/kategori_data/data_sekolah_inklusi.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../partials/footer.php'; ?>

    <script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('logoPreview');
            const container = document.getElementById('logoPreviewContainer');
            output.src = reader.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
</body>
</html>
