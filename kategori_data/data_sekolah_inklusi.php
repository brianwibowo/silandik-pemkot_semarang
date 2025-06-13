<?php
session_start();
include '../config.php';
include '../partials/head.php';
include '../koneksi.php';
?>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="<?= $base_url ?>admin/index.php">
            <img src="<?= $base_url ?>assets/logo_dinas.png" alt="Logo" width="50" height="40"> SILANDIK
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
                    <li><a class="dropdown-item" href="<?= $base_url ?>authentification/logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <?php include '../sidebar.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Data Sekolah Inklusi</h1>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-table me-1"></i>
                                Daftar Sekolah Inklusi
                            </div>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                                <a href="<?= $base_url ?>kategori_data/tambah_sekolah_inklusi.php" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i>
                                    Tambah Data Sekolah
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatablesSimple" class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No.</th>
                                            <th>Logo/Gambar</th>
                                            <th>Nama Sekolah</th>
                                            <th>Deskripsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = mysqli_query($conn, "SELECT * FROM data_sekolah_inklusi ORDER BY nama_sekolah ASC");
                                        if (!$query) {
                                            die("Query error: " . mysqli_error($conn));
                                        }

                                        while ($row = mysqli_fetch_assoc($query)) {
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                                                        <img src="<?= $base_url ?>upload/<?= htmlspecialchars($row['logo_sekolah']); ?>" alt="Logo" class="img-fluid" style="max-height: 140px; object-fit: contain;">
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($row['nama_sekolah']); ?></td>
                                                <td>
                                                    <div style="max-height: 150px; overflow-y: auto;">
                                                        <?= nl2br(htmlspecialchars($row['deskripsi'])); ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($_SESSION['role'] === 'admin') : ?>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <a href="<?= $base_url ?>kategori_data/edit_sekolah_inklusi.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('Yakin ingin mengubah data ini?')">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="<?= $base_url ?>kategori_data/hapus_sekolah_inklusi.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>
