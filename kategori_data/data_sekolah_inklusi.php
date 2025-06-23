<?php
session_start();
include '../config.php';
include '../partials/head.php';
include '../koneksi.php';
?>

<body class="sb-nav-fixed">
    <!-- navbar.php -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="../index.php">
            <img src="/assets/logo_dinas.png" alt="Logo" width="50" height="40"> SILANDIK
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
                    <li><a class="dropdown-item" href="../authentification/logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <style>
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        }
        .btn-group-custom .btn {
            margin: 0 2px;
        }
        .date-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            z-index: 2;
        }
        .card-img-overlay-custom {
            position: relative;
        }
        .image-error {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            height: 200px;
        }
        .search-highlight {
            background-color: yellow;
            padding: 1px 3px;
            border-radius: 2px;
        }
    </style>

    <div id="layoutSidenav">
        <?php include '../sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                        <div>
                            <h1 class="mb-1">Data Sekolah Inklusi</h1>
                            <p class="text-muted mb-0">Kelola informasi sekolah inklusi dengan mudah</p>
                        </div>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                        <div class="admin-controls">
                            <a href="tambah_sekolah_inklusi.php" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Tambah Data
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari nama sekolah..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <select class="form-select" id="sortFilter">
                                    <option value="">Urutkan berdasarkan</option>
                                    <option value="name">Nama A-Z</option>
                                    <option value="date">Tanggal Terbaru</option>
                                </select>
                                <button class="btn btn-outline-secondary" id="viewToggle" title="Toggle View">
                                    <i class="fas fa-th-large" id="viewIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cards Grid -->
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="schoolGrid">
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM data_sekolah_inklusi ORDER BY id DESC");
                        $data_found = false;
                        
                        while ($row = mysqli_fetch_assoc($query)) :
                            $data_found = true;
                            
                            // Format tanggal 
                            $date_display = date('d M Y');
                            
                            // Handle gambar yang hilang
                            $image_path = "../upload/" . htmlspecialchars($row['logo_sekolah']);
                            $image_exists = file_exists($image_path) && !empty($row['logo_sekolah']);
                        ?>
                            <div class="col school-card" data-name="<?= strtolower(htmlspecialchars($row['nama_sekolah'])); ?>" data-desc="<?= strtolower(htmlspecialchars($row['deskripsi'])); ?>">
                                <div class="card shadow-sm h-100 border-0 rounded-3 card-hover">
                                    <div class="card-img-overlay-custom">
                                        <?php if ($image_exists): ?>
                                            <img src="<?= $image_path; ?>" 
                                                 class="card-img-top rounded-top-3" 
                                                 alt="<?= htmlspecialchars($row['nama_sekolah']); ?>"
                                                 style="height: 200px; object-fit: cover;"
                                                 onerror="this.parentElement.innerHTML='<div class=\'image-error\'><i class=\'fas fa-school\'></i></div>'">
                                        <?php else: ?>
                                            <div class="image-error">
                                                <i class="fas fa-school"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="date-badge"><?= $date_display; ?></div>
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-primary fw-bold mb-2 school-title">
                                            <?= htmlspecialchars($row['nama_sekolah']); ?>
                                        </h5>
                                        <p class="card-text text-muted mb-3 school-desc" style="display: -webkit-box;  -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                            <?= nl2br(htmlspecialchars($row['deskripsi'])); ?>
                                        </p>
                                        
                                        <!-- Info badges -->
                                        <div class="mb-3">
                                            <span class="badge bg-light text-dark me-1">
                                                <i class="fas fa-graduation-cap me-1"></i>Sekolah Inklusi
                                            </span>
                                            <?php if (isset($row['alamat']) && !empty($row['alamat'])): ?>
                                            <span class="badge bg-light text-dark me-1">
                                                <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars(substr($row['alamat'], 0, 15)); ?>...
                                            </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Admin Controls -->
                                        <div class="mt-auto">
                                            <div class="btn-group-custom d-flex justify-content-between">
                                                <a href="detail_sekolahh_inklusi.php?id=<?= $row['id']; ?>" 
                                                   class="btn btn-outline-primary btn-sm rounded-pill flex-fill me-1">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </a>
                                                
                                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                                                <a href="edit_sekolah_inklusi.php?id=<?= $row['id']; ?>" 
                                                   class="btn btn-warning btn-sm rounded-pill flex-fill me-1"
                                                   onclick="return confirm('Anda yakin ingin mengedit data ini?')">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <a href="hapus_sekolah_inklusi.php?id=<?= $row['id']; ?>" 
                                                   class="btn btn-danger btn-sm rounded-pill flex-fill"
                                                   onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Empty State -->
                    <?php if (!$data_found): ?>
                    <div class="text-center py-5" id="emptyState">
                        <i class="fas fa-school fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada data sekolah</h5>
                        <p class="text-muted">Klik tombol "Tambah Data" untuk mulai menambahkan sekolah inklusi</p>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                        <a href="tambah_sekolah_inklusi.php" class="btn btn-success mt-3">
                            <i class="fas fa-plus me-2"></i>Tambah Data Pertama
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <!-- No Results Found (for search) -->
                    <div class="text-center py-5 d-none" id="noResults">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada hasil ditemukan</h5>
                        <p class="text-muted">Coba ubah kata kunci pencarian Anda</p>
                    </div>
                    <?php endif; ?>

                    <!-- Success/Error Messages -->
                    <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php
                        switch($_GET['success']) {
                            case 'add': echo 'Data sekolah berhasil ditambahkan!'; break;
                            case 'edit': echo 'Data sekolah berhasil diperbarui!'; break;
                            case 'delete': echo 'Data sekolah berhasil dihapus!'; break;
                            default: echo 'Operasi berhasil dilakukan!';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Terjadi kesalahan. Silakan coba lagi.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Enhanced JavaScript -->
    <script>
        // Search functionality with highlighting
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.school-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.dataset.name;
                const desc = card.dataset.desc;
                const titleEl = card.querySelector('.school-title');
                const descEl = card.querySelector('.school-desc');
                
                // Remove previous highlights
                titleEl.innerHTML = titleEl.textContent;
                descEl.innerHTML = descEl.textContent.replace(/\n/g, '<br>');
                
                if (searchTerm === '' || name.includes(searchTerm) || desc.includes(searchTerm)) {
                    card.style.display = 'block';
                    visibleCount++;
                    
                    // Add highlighting
                    if (searchTerm !== '') {
                        if (name.includes(searchTerm)) {
                            titleEl.innerHTML = highlightText(titleEl.textContent, searchTerm);
                        }
                        if (desc.includes(searchTerm)) {
                            descEl.innerHTML = highlightText(descEl.innerHTML, searchTerm);
                        }
                    }
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no results message
            const noResults = document.getElementById('noResults');
            if (noResults) {
                if (visibleCount === 0 && searchTerm !== '') {
                    noResults.classList.remove('d-none');
                } else {
                    noResults.classList.add('d-none');
                }
            }
        });

        // Highlight function
        function highlightText(text, term) {
            const regex = new RegExp(`(${term})`, 'gi');
            return text.replace(regex, '<span class="search-highlight">$1</span>');
        }

        // Sort functionality
        document.getElementById('sortFilter').addEventListener('change', function(e) {
            const sortType = e.target.value;
            const grid = document.getElementById('schoolGrid');
            const cards = Array.from(document.querySelectorAll('.school-card'));

            if (sortType === 'name') {
                cards.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
            } else if (sortType === 'date') {
                // Since we don't have actual dates, we'll sort by ID (reverse for newest first)
                cards.sort((a, b) => {
                    const aId = parseInt(a.querySelector('a[href*="id="]').href.split('id=')[1]);
                    const bId = parseInt(b.querySelector('a[href*="id="]').href.split('id=')[1]);
                    return bId - aId;
                });
            }

            // Re-append sorted cards
            cards.forEach(card => grid.appendChild(card));
        });

        // View toggle functionality
        document.getElementById('viewToggle').addEventListener('click', function() {
            const grid = document.getElementById('schoolGrid');
            const icon = document.getElementById('viewIcon');
            
            if (grid.classList.contains('row-cols-xl-3')) {
                grid.classList.remove('row-cols-xl-3');
                grid.classList.add('row-cols-xl-4');
                icon.classList.remove('fa-th-large');
                icon.classList.add('fa-th');
                this.title = 'View Large';
            } else {
                grid.classList.remove('row-cols-xl-4');
                grid.classList.add('row-cols-xl-3');
                icon.classList.remove('fa-th');
                icon.classList.add('fa-th-large');
                this.title = 'View Compact';
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>

    <?php include '../partials/footer.php'; ?>
</body>
</html>