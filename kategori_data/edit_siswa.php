<?php include '../partials/head.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include '../koneksi.php'; ?>
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
                    <li><hr class="dropdown-divider" /></li>
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
                    <h1 class="mt-4">Edit Data Siswa</h1>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user-edit me-1"></i>
                            Form Edit Data Siswa
                        </div>
                        <div class="card-body">

                            <?php
                            $id = $_GET['id'];
                            $query = mysqli_query($conn, "SELECT * FROM data_siswa WHERE id=$id");
                            $data = mysqli_fetch_assoc($query);

                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                $nama = $_POST['nama'];
                                $asal_sekolah = $_POST['asal_sekolah'];
                                $kelas = $_POST['kelas'];
                                $alamat = $_POST['alamat'];

                                mysqli_query($conn, "UPDATE data_siswa SET nama='$nama', asal_sekolah='$asal_sekolah', kelas='$kelas', alamat='$alamat' WHERE id=$id");
                                echo "
                            <script>
                                Swal.fire({
                                    title: 'Sukses!',
                                    text: 'Data berhasil diubah',
                                    icon: 'success',
                                    confirmButtonColor: '#198754',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href = '/silandik-semarang/kategori_data/data_siswa.php';
                                });
                            </script>";

                                header("Location: /silandik-semarang/kategori_data/data_siswa.php");
                            }
                            ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="nama" class="form-control" value="<?= $data['nama'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Asal Sekolah</label>
                                    <input type="text" name="asal_sekolah" class="form-control" value="<?= $data['asal_sekolah'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kelas</label>
                                    <input type="text" name="kelas" class="form-control" value="<?= $data['kelas'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" required><?= $data['alamat'] ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                                <a href="/silandik-semarang/kategori_data/data_siswa.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                            </form>

                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>
