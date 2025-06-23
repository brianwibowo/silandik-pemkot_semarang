<?php
session_start();
include '../config.php';
include '../partials/head.php';
include '../koneksi.php';

$id = $_GET['id'] ?? 0;

// Ambil data sekolah
$querySekolah = mysqli_query($conn, "SELECT * FROM data_sekolah_inklusi WHERE id = $id");
$sekolah = mysqli_fetch_assoc($querySekolah);

// Ambil data rekap
$queryRekap = mysqli_query($conn, "SELECT * FROM rekap WHERE sekolah_id = $id");
$rekap = mysqli_fetch_assoc($queryRekap);

// Ambil data prasarana
$queryPrasarana = mysqli_query($conn, "SELECT * FROM prasarana WHERE sekolah_id = $id");
$prasarana = mysqli_fetch_all($queryPrasarana, MYSQLI_ASSOC);

// Ambil data galeri
$queryGaleri = mysqli_query($conn, "SELECT * FROM galeri WHERE sekolah_id = $id");
$galeri = mysqli_fetch_all($queryGaleri, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sekolah - <?= $sekolah['nama_sekolah'] ?? 'Sekolah'; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Tambahkan CSS untuk sidebar yang mungkin hilang -->
    <link href="<?= $base_url ?>css/styles.css" rel="stylesheet" />
</head>

<body class="sb-nav-fixed">
    <!-- Navbar - Gunakan struktur yang sama dengan index -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="../index.php">
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
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                        <li><a class="dropdown-item" href="../authentification/logout.php">Logout</a></li>
                    <?php else : ?>
                        <li><a class="dropdown-item" href="../authentification/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <?php include '../sidebar.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <!-- Tombol Back -->
                    <div class="">
                        <h1 class="">Detail Sekolah Inklusi</h1>
                        <a href="../kategori_data/data_sekolah_inklusi.php" class="btn btn-secondary" style="margin-bottom: 20px ;">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>

                    <div class="row g-4">
                        <!-- Kolom Kiri -->
                        <div class="col-lg-4">
                            <!-- Logo Section yang diperbaiki -->
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <?php if (!empty($sekolah['logo_sekolah']) && file_exists("../upload/" . $sekolah['logo_sekolah'])): ?>
                                            <img src="../upload/<?= $sekolah['logo_sekolah']; ?>"
                                                alt="Logo <?= $sekolah['nama_sekolah']; ?>"
                                                class="img-fluid rounded-circle"
                                                style="width: 150px; height: 150px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                style="width: 150px; height: 150px;">
                                                <i class="fas fa-school fa-3x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="card-title"><?= $sekolah['nama_sekolah'] ?? 'Nama Sekolah'; ?></h4>
                                </div>
                            </div>

                            <!-- Galeri Sekolah -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-images me-2"></i> Galeri Sekolah
                                    </div>
                                    <div>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                            <a href="tambah_galeri.php?id=<?= $sekolah['id']; ?>"
                                                class="btn btn-sm btn-light text-success rounded-pill me-2">
                                                <i class="fas fa-plus"></i> Tambah
                                            </a>
                                            <a href="edit_galeri.php?id=<?= $sekolah['id']; ?>"
                                                class="btn btn-sm btn-light text-success rounded-pill">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if ($galeri && count($galeri) > 0): ?>
                                        <div class="row g-2">
                                            <?php foreach ($galeri as $foto): ?>
                                                <div class="col-12 mb-2">
                                                    <img src="../upload/galeri/<?= $foto['path_gambar']; ?>"
                                                        alt="<?= $foto['judul'] ?? 'Galeri'; ?>"
                                                        class="img-fluid rounded"
                                                        style="width: 100%; height: 200px; object-fit: cover;">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada galeri tersedia</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Lokasi Sekolah -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-map-marker-alt me-2"></i> Lokasi Sekolah
                                    </div>
                                    <div>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                            <a href="edit_lokasi.php?id=<?= $sekolah['id']; ?>"
                                                class="btn btn-sm btn-light text-success rounded-pill">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-4">
                                        <i class="fas fa-map fa-3x text-muted mb-3"></i>
                                        <p><strong>Peta Lokasi Sekolah</strong></p>
                                        <small class="text-muted">Coming Soon</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-lg-8">
                            <!-- Informasi Sekolah -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-info-circle me-2"></i> Informasi Sekolah
                                    </div>
                                    <div>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                            <a href="edit_sekolah_inklusi.php?id=<?= $sekolah['id']; ?>"
                                                class="btn btn-sm btn-light text-success rounded-pill">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 30%;"><i class="fas fa-id-card me-2 text-primary"></i>NPSN</td>
                                            <td><?= $sekolah['npsn'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-university me-2 text-primary"></i>Nama Sekolah</td>
                                            <td><?= $sekolah['nama_sekolah'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-map-pin me-2 text-primary"></i>Alamat</td>
                                            <td><?= $sekolah['alamat'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-user-tie me-2 text-primary"></i>Kepala Sekolah</td>
                                            <td><?= $sekolah['kepala_sekolah'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-globe me-2 text-primary"></i>Website</td>
                                            <td>
                                                <?php if (!empty($sekolah['website'])): ?>
                                                    <a href="<?= $sekolah['website']; ?>"
                                                        target="_blank" class="text-decoration-none"><?= $sekolah['website']; ?></a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Rekap Sekolah -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-chart-bar me-2"></i> Rekap Sekolah
                                    </div>
                                    <div>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                            <a href="tambah_rekap.php?id=<?= $sekolah['id']; ?>"
                                                class="btn btn-sm btn-light text-success rounded-pill me-2">
                                                <i class="fas fa-plus"></i> Tambah
                                            </a>
                                            <a href="edit_rekap.php?id=<?= $sekolah['id']; ?>"
                                                class="btn btn-sm btn-light text-success rounded-pill">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4 col-sm-12 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                                                    <h3 class="text-primary"><?= $rekap['jumlah_pegawai'] ?? '0'; ?></h3>
                                                    <span class="text-muted">Pegawai</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <i class="fas fa-user-graduate fa-3x text-success mb-3"></i>
                                                    <h3 class="text-success"><?= $rekap['jumlah_siswa'] ?? '0'; ?></h3>
                                                    <span class="text-muted">Siswa</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <i class="fas fa-users fa-3x text-warning mb-3"></i>
                                                    <h3 class="text-warning"><?= $rekap['jumlah_rombel'] ?? '0'; ?></h3>
                                                    <span class="text-muted">Rombel</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Prasarana Sekolah -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-building me-2"></i> Prasarana Sekolah
                                    </div>
                                    <div>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                            <a href="tambah_prasarana.php?id=<?= $sekolah['id']; ?>"
                                                class="btn btn-sm btn-light text-success rounded-pill me-2">
                                                <i class="fas fa-plus"></i> Tambah
                                            </a>
                                            <a href="edit_prasarana.php?id=<?= $sekolah['id']; ?>"
                                                class="btn btn-sm btn-light text-success rounded-pill">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if ($prasarana && count($prasarana) > 0): ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($prasarana as $item): ?>
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-building text-primary me-2"></i>
                                                        <strong><?= $item['jenis_prasarana'] ?? 'Prasarana'; ?></strong>
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill">
                                                        <?= $item['jumlah'] ?? '0'; ?> Unit
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada data prasarana tersedia</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <?php include '../partials/footer.php'; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base_url ?>js/scripts.js"></script>
    
    <!-- Script untuk sidebar toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const body = document.body;
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    body.classList.toggle('sb-sidenav-toggled');
                });
            }
        });
    </script>
</body>

</html>