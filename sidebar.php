<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
// Pastikan koneksi database sudah tersedia jika ingin ambil foto user
if (!isset($conn)) {
    @include_once 'koneksi.php';
}

// Cek status request pengurus untuk user yang login
$has_pending_request = false;
if (isset($_SESSION['email']) && isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    $user_email = $_SESSION['email'];
    $check_request = mysqli_query($conn, "SELECT request_pengurus FROM users WHERE email='$user_email'");
    if ($check_request && mysqli_num_rows($check_request) === 1) {
        $user_data = mysqli_fetch_assoc($check_request);
        $has_pending_request = ($user_data['request_pengurus'] == 1);
    }
}
?>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">
            <img src="/assets/logo_dinas.png" alt="Logo Dinas" width="40" height="32"> 
            SILANDIK
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto">
                <!-- Beranda -->
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="/index.php">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </li>
                
                <!-- Regulasi -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= ($current_page == 'dasar_hukum.php') ? 'active' : '' ?>" 
                       href="#" id="navbarDropdownRegulasi" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-gavel"></i> Regulasi
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownRegulasi">
                        <li>
                            <a class="dropdown-item <?= ($current_page == 'dasar_hukum.php') ? 'active' : '' ?>" 
                               href="/regulasi/dasar_hukum.php">
                                <i class="fas fa-balance-scale"></i> Dasar Hukum
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Sekolah Inklusi -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($current_page, ['data_sekolah_inklusi.php', 'edit_sekolah_inklusi.php', 'data_siswa.php']) ? 'active' : '' ?>" 
                       href="#" id="navbarDropdownSekolah" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-school"></i> Sekolah Inklusi
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownSekolah">
                        <li>
                            <a class="dropdown-item <?= in_array($current_page, ['data_sekolah_inklusi.php', 'edit_sekolah_inklusi.php']) ? 'active' : '' ?>" 
                               href="/kategori_data/data_sekolah_inklusi.php">
                                <i class="fas fa-building"></i> Data Sekolah Inklusi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= ($current_page == 'data_siswa.php') ? 'active' : '' ?>" 
                               href="/kategori_data/data_siswa.php">
                                <i class="fas fa-users"></i> Data Siswa
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Informasi Sekolah Inklusi -->
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'informasi_sekolah_inklusi.php') ? 'active' : '' ?>" 
                       href="/info_sekolah_inklusi/informasi_sekolah_inklusi.php">
                        <i class="fas fa-info-circle"></i> Informasi Sekolah Inklusi
                    </a>
                </li>
                
                <!-- Dokumen Kurikulum -->
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'dokumen_kurikulum_inklusi.php') ? 'active' : '' ?>" 
                       href="/dokumen_kurikulum/dokumen_kurikulum_inklusi.php">
                        <i class="fas fa-file-alt"></i> Dokumen Kurikulum Inklusi
                    </a>
                </li>
            </ul>
            
            <!-- Auth Section -->
            <div class="auth-section d-flex align-items-center gap-2">
                <!-- Admin Panel Access -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                    <a href="/authentification/admin.php" class="btn btn-warning">
                        <i class="fas fa-user-shield"></i> Admin Panel
                    </a>
                
                <!-- Request Pengurus Button untuk User -->
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user') : ?>
                    <?php if ($has_pending_request) : ?>
                        <!-- Jika sudah ada pending request -->
                        <span class="btn btn-outline-warning disabled" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Permintaan Anda sedang menunggu persetujuan admin">
                            <i class="fas fa-clock"></i> Request Pending
                        </span>
                    <?php else : ?>
                        <!-- Jika belum ada request -->
                        <a href="/authentification/request_pengurus.php" class="btn btn-outline-success">
                            <i class="fas fa-user-plus"></i> Request Pengurus
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- User Info & Logout/Login -->
                <?php if (isset($_SESSION['role'])) :
                    // Pilih warna background avatar berdasarkan role
                    $role = $_SESSION['role'];
                    $roleColor = 'bg-secondary text-white';
                    $roleLabel = ucfirst($role);
                    
                    if ($role === 'admin') {
                        $roleColor = 'bg-danger text-white';
                        $roleLabel = 'Admin';
                    } elseif ($role === 'pengurus') {
                        $roleColor = 'bg-success text-white';
                        $roleLabel = 'Pengurus';
                    } elseif ($role === 'user') {
                        $roleColor = 'bg-primary text-white';
                        $roleLabel = 'User';
                        // Tambahkan indikator jika ada pending request
                        if ($has_pending_request) {
                            $roleLabel = 'User (Pending)';
                        }
                    }
                ?>
                <div class="navbar-text d-flex align-items-center gap-2">
                    <div class="admin-avatar <?= $roleColor ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:bold;" 
                         data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= $roleLabel ?>">
                        <?= strtoupper(substr($role,0,1)) ?>
                        <?php if ($has_pending_request) : ?>
                            <span style="position:absolute;top:-2px;right:-2px;width:8px;height:8px;background:#ffc107;border-radius:50%;"></span>
                        <?php endif; ?>
                    </div>
                    <span><?= $roleLabel ?></span>
                </div>
                    <a href="/authentification/logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else : ?>
                    <a href="/authentification/login.php" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="/authentification/register.php" class="btn-register">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Script untuk tooltip Bootstrap -->
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>