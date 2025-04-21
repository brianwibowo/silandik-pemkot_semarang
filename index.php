<?php
session_start();
?>
<?php include 'partials/head.php'; ?>
<?php include 'koneksi.php'; ?>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php"><img src="/silandik-semarang/logo_dinas.png" alt="Logo" width="50" height="40">SILANDIK</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <?php include 'sidebar.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Beranda</h1>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-gavel me-1"></i> Draft Hukum</span>
                            <?php
                            include 'koneksi.php';
                            $data = mysqli_query($conn, "SELECT * FROM dasar_hukum WHERE id = 1");
                            $draft = mysqli_fetch_assoc($data);
                            $namaFile = isset($draft['draft_hukum']) ? $draft['draft_hukum'] : null;
                            ?>
                            <?php if ($namaFile) : ?>
                                <a href="/silandik-semarang/pdfs/<?= $namaFile ?>" class="btn btn-sm btn-success" download>
                                    <i class="fas fa-download"></i> Unduh Draft
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if ($namaFile) : ?>
                                <embed src="/silandik-semarang/pdfs/<?= $namaFile ?>" type="application/pdf" width="100%" height="400px" />
                            <?php else : ?>
                                <p class="text-danger">Belum ada draft hukum yang tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i> Data Siswa
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>Asal Sekolah</th>
                                        <th>Kelas</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>Asal Sekolah</th>
                                        <th>Kelas</th>
                                        <th>Alamat</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "SELECT * FROM data_siswa");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($row['nama']); ?></td>
                                            <td><?= htmlspecialchars($row['asal_sekolah']); ?></td>
                                            <td><?= htmlspecialchars($row['kelas']); ?></td>
                                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include 'partials/footer.php'; ?>
</body>

</html>