<?php
session_start();
include '../../../config.php';
include '../../../partials/head.php';
include '../../../koneksi.php';

// Cek role
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isPengurus = isset($_SESSION['role']) && $_SESSION['role'] === 'pengurus';
$user_sekolah_id = $isPengurus && isset($_SESSION['sekolah_id']) ? $_SESSION['sekolah_id'] : null;
?>
<div id="background">
    <?php include '../../../partials/sidebar.php'; ?>

    <!-- Header Section -->
    <div class="header-section">
        <div class="header-container">
            <div class="header-title">
                <h1 class="page-title">Data Sekolah Inklusi</h1>
                <p class="page-subtitle">Kelola informasi sekolah inklusi dengan mudah</p>
            </div>
        </div>
    </div>

    <main>
        <div class="container-fluid px-4">
            <div class="search-section">
                <div class="row align-items-center g-3 flex-wrap">

                    <div class="col-md-3">
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="Cari nama sekolah..." id="searchInput">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="filter-section">
                            <select class="filter-select" id="jenjangFilter">
                                <option value="">Semua Jenjang</option>
                                <option value="PAUD">PAUD (Pendidikan Anak Usia Dini)</option>
                                <option value="TK">TK (Taman Kanak-Kanak)</option>
                                <option value="SD">SD (Sekolah Dasar)</option>
                                <option value="SMP">SMP (Sekolah Menengah Pertama)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 text-md-center text-start">
                        <?php if ($isAdmin): ?>
                            <a href="tambah_sekolah_inklusi.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Data
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <div class="filter-section justify-content-md-end justify-content-start d-flex gap-2">
                            <select class="filter-select" id="sortFilter" style="max-width: 200px;">
                                <option value="">Urutkan berdasarkan</option>
                                <option value="name">Nama A-Z</option>
                                <option value="jenjang">Jenjang</option>
                            </select>
                            <button class="view-toggle" id="viewToggle" title="Toggle View">
                                <i class="fas fa-th-large" id="viewIcon"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show alert-custom" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php
                    switch ($_GET['success']) {
                        case 'add':
                            echo 'Data sekolah berhasil ditambahkan!';
                            break;
                        case 'edit':
                            echo 'Data sekolah berhasil diperbarui!';
                            break;
                        case 'delete':
                            echo 'Data sekolah berhasil dihapus!';
                            break;
                        default:
                            echo 'Operasi berhasil dilakukan!';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show alert-custom" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Terjadi kesalahan. Silakan coba lagi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Cards Grid -->
            <div class="cards-grid" id="schoolGrid">
                <?php
                $query = mysqli_query($conn, "
                    SELECT * FROM data_sekolah_inklusi 
                    ORDER BY id DESC
                ");

                if (!$query) {
                    echo "<div class='alert alert-danger'>Error dalam mengambil data: " . mysqli_error($conn) . "</div>";
                } else {
                    $data_found = false;

                    while ($row = mysqli_fetch_assoc($query)) :
                        $data_found = true;

                        $image_path = "../upload/" . htmlspecialchars($row['logo_sekolah']);
                        $image_exists = file_exists($image_path) && !empty($row['logo_sekolah']);

                        // Normalize case for comparison but keep original for display
                        $jenjang_upper = strtoupper(trim($row['jenjang_sekolah'])); 
                        $jenjang_class = match ($jenjang_upper) {
                            'PAUD' => 'badge-info',
                            'TK' => 'badge-primary', 
                            'SD' => 'badge-success',
                            'SMP' => 'badge-warning',
                            default => 'badge-secondary'
                        };                        // Cek hak edit/hapus
                        $canEdit = false;
                        if ($isAdmin) {
                            $canEdit = true;
                        } elseif ($isPengurus && intval($user_sekolah_id) === intval($row['id'])) {
                            $canEdit = true;
                        }
                ?>
                        <div class="school-card"
                            data-name="<?= strtolower(htmlspecialchars($row['nama_sekolah'])); ?>"
                            data-desc="<?= strtolower(htmlspecialchars($row['deskripsi'])); ?>"
                            data-jenjang="<?= htmlspecialchars($row['jenjang_sekolah']); ?>">

                            <!-- School Image -->
                            <div class="school-image-container">
                                <?php if ($image_exists): ?>
                                    <img src="<?= $image_path; ?>" class="school-image"
                                        alt="<?= htmlspecialchars($row['nama_sekolah']); ?>"
                                        onerror="this.parentElement.innerHTML='<div class=\'school-image-placeholder\'><i class=\'fas fa-school\'></i></div>'">
                                <?php else: ?>
                                    <div class="school-image-placeholder">
                                        <i class="fas fa-school"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="school-badge <?= $jenjang_class ?>"><?= htmlspecialchars(trim($row['jenjang_sekolah'])); ?></div>

<style>
/* Badge styles */
.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-primary {
    background-color: #007bff;
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: black;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.school-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    z-index: 1;
}
</style>
                            </div>

                            <!-- School Content -->
                            <div class="school-content">
                                <div class="school-meta">
                                    <span class="school-type">SEKOLAH INKLUSI</span>
                                    <span class="school-level"><?= strtoupper(htmlspecialchars($row['jenjang_sekolah'])); ?></span>
                                </div>

                                <h3 class="school-title"><?= htmlspecialchars($row['nama_sekolah']); ?></h3>

                                <p class="school-excerpt">
                                    <?= htmlspecialchars(mb_strimwidth($row['deskripsi'], 0, 100, '...')); ?>
                                </p>

                                <?php if (!empty($row['alamat'])): ?>
                                    <div class="school-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?= htmlspecialchars(mb_strimwidth($row['alamat'], 0, 50, '...')); ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="school-actions">
                                    <a href="detail_sekolahh_inklusi.php?id=<?= $row['id']; ?>" class="view-detail-btn">
                                        Lihat Detail <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <?php if ($canEdit): ?>
                                        <div class="admin-actions">
                                            <a href="edit_sekolah_inklusi.php?id=<?= $row['id']; ?>" class="btn-edit" title="Edit"
                                                onclick="return confirm('Anda yakin ingin mengedit data ini?')">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <?php if ($isAdmin): ?>
                                                <a href="hapus_sekolah_inklusi.php?id=<?= $row['id']; ?>" class="btn-delete" title="Hapus"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                <?php endwhile;
                } ?>

                <?php if (!isset($data_found) || !$data_found): ?>
                    <div class="empty-state">
                        <i class="fas fa-school"></i>
                        <h5>Belum ada data sekolah</h5>
                        <p>Klik tombol "Tambah Data" untuk mulai menambahkan sekolah inklusi</p>
                        <?php if ($isAdmin): ?>
                            <a href="tambah_sekolah_inklusi.php" class="btn-add">
                                <i class="fas fa-plus"></i>Tambah Data Pertama
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results d-none" id="noResults">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada hasil ditemukan</h5>
                        <p class="text-muted">Coba ubah kata kunci pencarian Anda</p>
                    </div>
                <?php endif; ?>
            </div>
    </main>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const cards = document.querySelectorAll('.school-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const desc = card.getAttribute('data-desc');

            if (name.includes(searchTerm) || desc.includes(searchTerm)) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        const noResults = document.getElementById('noResults');
        if (visibleCount === 0 && searchTerm !== '') {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    });

    document.getElementById('jenjangFilter').addEventListener('change', function() {
        const filterValue = this.value;
        const cards = document.querySelectorAll('.school-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const jenjang = card.getAttribute('data-jenjang');

            if (filterValue === '' || jenjang === filterValue) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        const noResults = document.getElementById('noResults');
        if (visibleCount === 0 && filterValue !== '') {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    });

    document.getElementById('sortFilter').addEventListener('change', function() {
        const sortValue = this.value;
        const grid = document.getElementById('schoolGrid');
        const cards = Array.from(document.querySelectorAll('.school-card'));

        if (sortValue === 'name') {
            cards.sort((a, b) => {
                const nameA = a.getAttribute('data-name');
                const nameB = b.getAttribute('data-name');
                return nameA.localeCompare(nameB);
            });
        } else if (sortValue === 'jenjang') {
            cards.sort((a, b) => {
                const jenjangA = a.getAttribute('data-jenjang');
                const jenjangB = b.getAttribute('data-jenjang');
                return jenjangA.localeCompare(jenjangB);
            });
        }

        // Re-append sorted cards
        cards.forEach(card => grid.appendChild(card));
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('show')) {
                alert.classList.remove('show');
                alert.classList.add('fade');
            }
        });
    }, 5000);
</script>

<?php include '../../../partials/footer.php'; ?>

</html>