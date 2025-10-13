<?php
session_start();
include '../../config.php';
include '../../koneksi.php';

$isAdmin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus']);

// Pagination
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = "WHERE info.nama_kegiatan LIKE '%$search%' OR sekolah.nama_sekolah LIKE '%$search%'";
}

// Hitung total data
$countResult = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM info_sekolah_inklusi AS info
    LEFT JOIN data_sekolah_inklusi AS sekolah ON info.sekolah_id = sekolah.id
    $searchCondition
");
$totalItems = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalItems / $limit);

// Ambil data kegiatan
$query = mysqli_query($conn, "
    SELECT info.*, sekolah.nama_sekolah 
    FROM info_sekolah_inklusi AS info
    LEFT JOIN data_sekolah_inklusi AS sekolah ON info.sekolah_id = sekolah.id
    $searchCondition
    ORDER BY info.tanggal DESC
    LIMIT $limit OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../../partials/head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Informasi Sekolah Inklusi</title>
    
    <?php
    // Notifikasi handling
    if (isset($_SESSION['flash_message'])) {
        $message = '';
        $icon = '';
        
        switch ($_SESSION['flash_message']) {
            case 'success_add':
                $message = 'Data berhasil ditambahkan!';
                $icon = 'success';
                break;
            case 'success_edit':
                $message = 'Data berhasil diperbarui!';
                $icon = 'success';
                break;
            case 'success_delete':
                $message = 'Data berhasil dihapus!';
                $icon = 'success';
                break;
            case 'error_add':
                $message = 'Gagal menambahkan data!';
                $icon = 'error';
                break;
            case 'error_edit':
                $message = 'Gagal memperbarui data!';
                $icon = 'error';
                break;
            case 'error_delete':
                $message = 'Gagal menghapus data!';
                $icon = 'error';
                break;
        }
        
        if ($message && $icon) {
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Notifikasi',
                        text: '$message',
                        icon: '$icon',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                });
            </script>";
        }
        
        unset($_SESSION['flash_message']);
    }
    ?>
    <style>
        body{
            background-color: whitesmoke;
        }
        .activity-card {
            border: none;
            border-radius: 1rem;
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .activity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .activity-card .card-img-top {
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .activity-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .activity-card .card-body {
            padding: 1.5rem;
        }

        .activity-card .card-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .date-badge {
            background: #f7fafc;
            color: #4a5568;
            padding: 0.3rem 0.8rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            border: 1px solid #e2e8f0;
            display: inline-block;
        }

        .admin-section {
            background: white;
            border-radius: 12px;
            padding: 0;
            margin-top: 3rem;
            overflow: hidden;
            box-shadow: 0 0 25px rgba(0,0,0,.08);
        }

        .admin-section .section-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,.05);
            padding: 1.2rem 1.5rem;
        }

        .table-responsive {
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0,0,0,.08);
            overflow: hidden;
            margin: 0;
        }

        .admin-table {
            margin-bottom: 0;
            background: white;
        }

        .admin-table > thead {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .admin-table > thead th {
            border: none !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 1.2rem 1rem;
            color: #495057;
            letter-spacing: 0.5px;
            background: linear-gradient(to bottom, #f8f9fa, #f1f3f5);
            vertical-align: middle;
        }

        .admin-table > tbody > tr {
            border-bottom: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .admin-table > tbody > tr:hover {
            background-color: rgba(70, 128, 255, 0.05) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,.02);
        }

        .admin-table > tbody > tr > td {
            padding: 1rem;
            vertical-align: middle;
            border: none;
            color: #495057;
        }

        /* Action Buttons */
        .action-buttons .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
            margin: 0 0.15rem;
            transition: all 0.2s ease;
            position: relative;
            border: none;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
        }

        .action-buttons .btn-warning {
            background: #fbbf24;
            color: #fff;
        }

        .action-buttons .btn-warning:hover {
            background: #f59e0b;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
        }

        .action-buttons .btn-danger {
            background: #ef4444;
            color: #fff;
        }

        .action-buttons .btn-danger:hover {
            background: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* Add Button */
        .btn-success {
            background: #10b981;
            border-color: #10b981;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-success:hover {
            background: #059669;
            border-color: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(16,185,129,.15);
        }

        .pagination-custom {
            margin-top: 2rem;
        }

        .pagination-custom .page-link {
            border: none;
            border-radius: 0.5rem;
            margin: 0 0.2rem;
            color: #667eea;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .pagination-custom .page-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-1px);
        }

        .pagination-custom .page-item.active .page-link {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
        }

        .no-data {
            text-align: center;
            color: #718096;
            padding: 3rem;
            background: #f7fafc;
            border-radius: 1rem;
            margin: 2rem 0;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .filter-section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .activity-card .card-img-top {
                height: 180px;
            }

            .admin-section {
                padding: 1rem;
            }

            .admin-table {
                font-size: 0.85rem;
            }

            .school-badge {
                font-size: 0.8rem;
                padding: 0.2rem 0.5rem;
            }
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        /* Spacing styles */
        main {
            min-height: calc(100vh - 60px); /* Memastikan konten minimal setinggi viewport */
            padding-bottom: 2rem;
        }

        .bottom-spacing {
            height: 2rem;
        }

        /* Memastikan admin section memiliki margin bottom yang cukup */
        .admin-section {
            margin-bottom: 3rem;
        }

        /* Memastikan pagination memiliki margin bottom yang cukup */
        .pagination-custom {
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    <?php include '../../partials/sidebar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">

                    <div class="page-header">
                        <h1 class="page-title">Informasi Sekolah Inklusi</h1>
                        <p class="page-subtitle">Daftar kegiatan dari berbagai sekolah inklusi</p>
                    </div>

                    <!-- Form Pencarian -->
                    <form class="mb-4 filter-section" method="GET">
                        <div class="input-group">
                            <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari kegiatan atau nama sekolah..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>

                    <!-- Kartu Kegiatan (Umum) -->
                    <div class="row">
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <div class="col-lg-3 col-md-4 mb-4">
                                    <div class="activity-card h-100">
                                        <?php if ($row['foto']): ?>
                                            <img src="../../upload/info_sekolah/<?= htmlspecialchars($row['foto']) ?>" class="card-img-top" alt="Foto">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($row['nama_kegiatan']) ?></h5>
                                            <div class="school-badge" title="<?= htmlspecialchars($row['nama_sekolah']) ?>">
                                                <?= htmlspecialchars($row['nama_sekolah']) ?>
                                            </div>
                                            <div class="date-badge mt-2"><i class="fas fa-calendar-alt me-1"></i><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="no-data">
                                    <i class="fas fa-search mb-3" style="font-size: 3rem;"></i>
                                    <h4>Data tidak ditemukan</h4>
                                    <?php if (!empty($search)): ?>
                                        <p>Tidak ditemukan kegiatan yang sesuai dengan pencarian "<?= htmlspecialchars($search) ?>"</p>
                                        <a href="?" class="btn btn-primary">Lihat Semua</a>
                                    <?php else: ?>
                                        <p>Belum ada data kegiatan sekolah inklusi</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Pagination">
                            <ul class="pagination justify-content-center pagination-custom">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                    <!-- Tabel Admin (CRUD) -->
                    <?php
                    // Ambil ulang data untuk tabel admin (karena while di atas sudah habis)
                    if ($isAdmin) {
                        $queryAdmin = mysqli_query($conn, "
                        SELECT info.*, sekolah.nama_sekolah 
                        FROM info_sekolah_inklusi AS info
                        LEFT JOIN data_sekolah_inklusi AS sekolah ON info.sekolah_id = sekolah.id
                        $searchCondition
                        ORDER BY info.tanggal DESC
                        LIMIT $limit OFFSET $offset
                    ");
                    }
                    ?>
                    <?php if ($isAdmin): ?>
                        <div class="admin-section">
                            <div class="section-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0"><i class="fas fa-cog me-2" style="color: #4680ff;"></i> Manajemen Data</h4>
                                <a href="tambah_info.php" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i> Tambah Data
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle admin-table">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Nama Sekolah</th>
                                            <th>Nama Kegiatan</th>
                                            <th>Foto</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = $offset + 1;
                                        while ($row = mysqli_fetch_assoc($queryAdmin)):
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($row['nama_sekolah']) ?></td>
                                                <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                                <td class="text-center">
                                                    <?php if ($row['foto']): ?>
                                                        <img src="../../upload/info_sekolah/<?= htmlspecialchars($row['foto']) ?>" 
                                                             alt="foto" 
                                                             width="60" 
                                                             height="60" 
                                                             style="object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted">
                                                            <i class="fas fa-image me-1"></i>Tidak ada
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge" style="background: #4680ff; font-weight: 500;">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="action-buttons d-flex justify-content-center gap-1">
                                                        <a href="edit_info.php?id=<?= $row['id'] ?>" 
                                                           class="btn btn-warning btn-sm"
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top" 
                                                           title="Edit Informasi">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="top" 
                                                                title="Hapus Informasi"
                                                                onclick="confirmDelete(<?= $row['id'] ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                        
                                        <script>
                                        function confirmDelete(id) {
                                            Swal.fire({
                                                title: 'Konfirmasi Hapus',
                                                text: 'Apakah Anda yakin ingin menghapus data ini?',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#ef4444',
                                                cancelButtonColor: '#6B7280',
                                                confirmButtonText: 'Ya, Hapus!',
                                                cancelButtonText: 'Batal',
                                                reverseButtons: true
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = 'hapus_info.php?id=' + id;
                                                }
                                            });
                                        }
                                        </script>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Spacing di bagian bawah -->
                <div class="bottom-spacing mb-6"></div>
            </main>
        </div>
    </div>
    <?php include '../../partials/footer.php'; ?>
    
    <script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Sweet Alert confirmation for delete
    function confirmDelete(id) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus informasi ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hapus_info.php?id=' + id;
            }
        });
    }
    </script>
</body>

</html>