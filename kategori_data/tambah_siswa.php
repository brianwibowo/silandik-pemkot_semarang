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
                    <h1 class="mt-4">Tambah Data Siswa</h1>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user-plus me-1"></i>
                            Form Tambah Data Siswa
                        </div>
                        <div class="card-body">
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                $nama = $_POST['nama'];
                                $asal_sekolah = $_POST['asal_sekolah'];
                                $kelas = $_POST['kelas'];
                                $alamat = $_POST['alamat'];

                                $query = "INSERT INTO data_siswa (nama, asal_sekolah, kelas, alamat) 
                                          VALUES ('$nama', '$asal_sekolah', '$kelas', '$alamat')";
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
                                    window.location.href = '/silandik-semarang/kategori_data/data_siswa.php';
                                });
                            </script>";

                                header("Location: /silandik-semarang/kategori_data/data_siswa.php");
                            }
                            ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Asal Sekolah</label>
                                    <input type="text" name="asal_sekolah" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kelas</label>
                                    <input type="text" name="kelas" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button>
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