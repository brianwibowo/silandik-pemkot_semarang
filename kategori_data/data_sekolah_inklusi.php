<?php
include '../partials/head.php';
include '../koneksi.php';
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
                    <h1 class="mt-4">Data Sekolah Inklusi</h1>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-table me-1"></i>
                                Daftar Sekolah Inklusi
                            </div>
                            <a href="/silandik-semarang/kategori_data/tambah_sekolah_inklusi.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i>
                                Tambah Data Sekolah
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatablesSimple" class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th width="25%">Logo</th>
                                            <th width="20%">Nama Sekolah</th>
                                            <th width="35%">Deskripsi</th>
                                            <th width="15%">Aksi</th>
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
                                                        <img src="../upload/<?= htmlspecialchars($row['logo_sekolah']); ?>" 
                                                             alt="Logo <?= htmlspecialchars($row['nama_sekolah']); ?>" 
                                                             class="img-fluid" 
                                                             style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                                    </div>
                                                </td>
                                                <td class="align-middle"><?= htmlspecialchars($row['nama_sekolah']); ?></td>
                                                <td class="align-middle">
                                                    <div style="max-height: 150px; overflow-y: auto;">
                                                        <?= nl2br(htmlspecialchars($row['deskripsi'])); ?>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="edit_sekolah_inklusi.php?id=<?= $row['id']; ?>" 
                                                           class="btn btn-warning btn-sm" 
                                                           title="Edit"
                                                           onclick="return confirm('Yakin ingin mengubah data ini?')">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="hapus_sekolah_inklusi.php?id=<?= $row['id']; ?>" 
                                                           class="btn btn-danger btn-sm" 
                                                           title="Hapus"
                                                           onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
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

    <style>
        /* Custom CSS untuk tabel */
        .table th {
            vertical-align: middle;
            text-align: center;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        /* Hover effect untuk baris tabel */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        /* Scrollbar untuk deskripsi */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</body>
</html>