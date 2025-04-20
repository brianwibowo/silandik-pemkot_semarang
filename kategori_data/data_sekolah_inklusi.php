<?php 
include '../partials/head.php'; 
include '../koneksi.php';
?>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">
            <img src="/logo_dinas.png" alt="Logo" width="50" height="40"> SILANDIK
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/authentification/login.php">Login</a></li>
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
                    <h1 class="mt-4">Data Sekolah Inklusi</h1>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-table me-1"></i>
                                Daftar Sekolah Inklusi
                            </div>
                            <a href="tambah_sekolah_inklusi.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> 
                                Tambah Data Sekolah
                            </a>
                        </div>

                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Logo</th>
                                        <th>Nama Sekolah</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>No.</th>
                                        <th>Logo</th>
                                        <th>Nama Sekolah</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "SELECT * FROM data_sekolah_inklusi");

                                    if (!$query) {
                                        die("Query error: " . mysqli_error($conn));
                                    }

                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td>
                                                <img src="../upload/<?= htmlspecialchars($row['logo_sekolah']); ?>" alt="Logo" width="50">
                                            </td>
                                            <td><?= htmlspecialchars($row['nama_sekolah']); ?></td>
                                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                                            <td>
                                                <a href="edit_sekolah_inklusi.php?id=<?= htmlspecialchars($row['id']); ?>" 
                                                    class="btn btn-warning btn-sm" 
                                                    onclick="return confirm('Yakin ingin mengubah data ini?')">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_sekolah_inklusi.php?id=<?= htmlspecialchars($row['id']); ?>" 
                                                    class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
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

    <?php include '../partials/footer.php'; ?>
</body>
</html>
