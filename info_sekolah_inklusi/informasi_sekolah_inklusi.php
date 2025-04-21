<?php
session_start();
?>
<?php include '../partials/head.php'; ?>
<?php include '../koneksi.php'; ?>

<?php
$data = mysqli_query($conn, "SELECT * FROM info_sekolah_inklusi WHERE id = 1");
$draft = mysqli_fetch_assoc($data);
$namaFile = isset($draft['draft_sekolah']) ? $draft['draft_sekolah'] : null;
?>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php"><img src="/silandik-semarang/logo_dinas.png" alt="Logo" width="50" height="40">SILANDIK</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </nav>

    <div id="layoutSidenav">
        <?php include '../sidebar.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Informasi Sekolah Inklusi</h1>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-gavel me-1"></i>Informasi Sekolah Inklusi</span>
                            <div>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                                    <a href="/silandik-semarang/info_sekolah_inklusi/edit_informasi.php" class="btn btn-sm btn-warning me-2">
                                        <i class="fas fa-edit"></i> Ubah Draft Sekolah Inklusi
                                    </a>
                                <?php endif; ?>
                                <?php if ($namaFile) : ?>
                                    <a href="/silandik-semarang/pdfs/<?= $namaFile ?>" class="btn btn-sm btn-primary" download>
                                        <i class="fas fa-download"></i> Unduh PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body" style="height: 600px; overflow: auto;">
                            <?php if ($namaFile) : ?>
                                <embed src="/silandik-semarang/pdfs/<?= $namaFile ?>" type="application/pdf" width="100%" height="100%" />
                            <?php else : ?>
                                <p class="text-danger">Belum ada draft kurikulum yang diunggah.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>

</html>