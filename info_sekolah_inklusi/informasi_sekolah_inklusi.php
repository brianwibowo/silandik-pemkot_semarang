<?php
session_start();
include '../config.php';
include '../koneksi.php';

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
    <?php include '../partials/head.php'; ?>
    <title>Informasi Sekolah Inklusi</title>
    <style>
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
            background: #f8f9fa;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 3rem;
            border: 1px solid #e9ecef;
        }

        .admin-table {
            background: white;
            border-radius: 0.8rem;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .admin-table thead {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .admin-table th {
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .admin-table td {
            padding: 1rem;
            border-color: #f1f5f9;
            vertical-align: middle;
        }

        .btn-action {
            margin: 0 0.2rem;
            border-radius: 0.5rem;
            padding: 0.4rem 0.8rem;
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
    </style>
</head>

<body>
    <?php include '../sidebar.php'; ?>
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
                                            <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" class="card-img-top" alt="Foto">
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4><i class="fas fa-cog me-2 text-primary"></i> Manajemen Data</h4>
                                <a href="tambah_info.php" class="btn btn-success"><i class="fas fa-plus me-1"></i> Tambah Data</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table admin-table">
                                    <thead>
                                        <tr>
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
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($row['nama_sekolah']) ?></td>
                                                <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                                <td>
                                                    <?php if ($row['foto']): ?>
                                                        <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" alt="foto" width="60" height="60" style="object-fit: cover; border-radius: 0.5rem;">
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted"><i class="fas fa-image me-1"></i>Tidak ada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="badge bg-primary"><?= date('d M Y', strtotime($row['tanggal'])) ?></span></td>
                                                <td>
                                                    <a href="edit_info.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning btn-action" title="Edit"><i class="fas fa-edit"></i></a>
                                                    <a href="hapus_info.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>

</html>