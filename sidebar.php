<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Main Pages</div>

                <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="<?= $base_url ?>index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Beranda
                </a>

                <!-- Regulasi -->
                <a class="nav-link collapsed <?= ($current_page == 'dasar_hukum.php') ? 'active' : '' ?>" data-bs-toggle="collapse" data-bs-target="#collapseRegulasi" aria-expanded="false" aria-controls="collapseRegulasi">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Regulasi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($current_page == 'dasar_hukum.php') ? 'show' : '' ?>" id="collapseRegulasi" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= ($current_page == 'dasar_hukum.php') ? 'active' : '' ?>" href="<?= $base_url ?>regulasi/dasar_hukum.php">Dasar Hukum</a>
                    </nav>
                </div>

                <!-- Sekolah Inklusi -->
                <a class="nav-link collapsed <?= in_array($current_page, ['data_sekolah_inklusi.php', 'data_siswa.php']) ? 'active' : '' ?>" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Sekolah Inklusi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= in_array($current_page, ['data_sekolah_inklusi.php', 'data_siswa.php']) ? 'show' : '' ?>" id="collapsePages" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= ($current_page == 'data_sekolah_inklusi.php') ? 'active' : '' ?>" href="<?= $base_url ?>kategori_data/data_sekolah_inklusi.php">Data Sekolah Inklusi</a>
                        <a class="nav-link <?= ($current_page == 'data_siswa.php') ? 'active' : '' ?>" href="<?= $base_url ?>kategori_data/data_siswa.php">Data Siswa</a>
                    </nav>
                </div>

                <!-- Informasi Sekolah Inklusi -->
                <a class="nav-link collapsed <?= ($current_page == 'informasi_sekolah_inklusi.php') ? 'active' : '' ?>" data-bs-toggle="collapse" data-bs-target="#collapseInformasi" aria-expanded="false" aria-controls="collapseInformasi">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Informasi Sekolah Inklusi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($current_page == 'informasi_sekolah_inklusi.php') ? 'show' : '' ?>" id="collapseInformasi" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= ($current_page == 'informasi_sekolah_inklusi.php') ? 'active' : '' ?>" href="<?= $base_url ?>info_sekolah_inklusi/informasi_sekolah_inklusi.php">Informasi Sekolah Inklusi</a>
                    </nav>
                </div>

                <!-- Dokumen Kurikulum -->
                <a class="nav-link collapsed <?= ($current_page == 'dokumen_kurikulum_inklusi.php') ? 'active' : '' ?>" data-bs-toggle="collapse" data-bs-target="#collapseDokumen" aria-expanded="false" aria-controls="collapseDokumen">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Contoh Dokumen Adaptasi Kurikulum Inklusi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($current_page == 'dokumen_kurikulum_inklusi.php') ? 'show' : '' ?>" id="collapseDokumen" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= ($current_page == 'dokumen_kurikulum_inklusi.php') ? 'active' : '' ?>" href="<?= $base_url ?>dokumen_kurikulum/dokumen_kurikulum_inklusi.php">Dokumen Kurikulum Inklusi</a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bagian login/logout -->
        <div class="text-center p-3">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                <span class="fw-bold text-success">Halo, Admin!</span><br>
                <a href="<?= $base_url ?>authentification/logout.php" class="btn btn-danger w-100 mt-2">Logout</a>
            <?php else : ?>
                <a href="<?= $base_url ?>authentification/login.php" class="btn btn-success w-100">Login Admin &rarr;</a>
            <?php endif; ?>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small">
                Developed by: <a href="https://instagram.com/sultan.studiooo" target="_blank" style="text-decoration: none;">Sultan Studio</a>
            </div>
        </div>
    </nav>
</div>
