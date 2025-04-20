<!-- sidebar.php -->
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Main Pages</div>
                <a class="nav-link" href="/silandik-semarang/index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Beranda
                </a>
                <!-- Regulasi -->
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#collapseRegulasi" aria-expanded="false" aria-controls="collapseRegulasi">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Regulasi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseRegulasi" aria-labelledby="headingRegulasi" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="/silandik-semarang/regulasi/dasar_hukum.php">Dasar Hukum</a>
                    </nav>
                </div>
                <!-- Sekolah Inklusi -->
                <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Sekolah Inklusi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="/silandik-semarang/kategori_data/data_sekolah_inklusi.php">Data Sekolah Inklusi</a>
                        <a class="nav-link" href="/silandik-semarang/kategori_data/data_siswa.php">Data Siswa</a>
                    </nav>
                </div>
                <div class="sb-sidenav-menu-heading">Addons</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInformasi" aria-expanded="false" aria-controls="collapseInformasi">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Informasi Sekolah Inklusi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseInformasi" aria-labelledby="headingInformasi" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="/silandik-semarang/informasi_inklusi.pptx">Download</a>
                    </nav>
                </div>

                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseDokumen" aria-expanded="false" aria-controls="collapseDokumen">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Contoh Dokumen Adaptasi Kurikulum Inklusi
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseDokumen" aria-labelledby="headingDokumen" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="#">Dokumen</a>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Tombol Login Admin -->
        <div class="text-center p-3">
            <a href="/silandik-semarang/authentification/login.php" class="btn btn-success w-100">Login Admin &rarr;</a>
        </div>
        <!-- Footer -->
        <div class="sb-sidenav-footer">
            <div class="small">Developed by:</div>
            Sultan Studio
        </div>
    </nav>
</div>