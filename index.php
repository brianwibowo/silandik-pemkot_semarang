<?php
session_start();
include 'koneksi.php';
include 'partials/head.php';

// Fungsi ambil data berita dengan pagination yang lebih aman
function getPaginationData($conn, $kategori, $limit, $page)
{
    // Validasi input
    $kategori = mysqli_real_escape_string($conn, $kategori);
    $limit = (int) $limit;
    $page = (int) $page;
    $offset = ($page - 1) * $limit;
    
    // Query dengan prepared statement
    $stmt = mysqli_prepare($conn, "SELECT * FROM berita WHERE kategori = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "sii", $kategori, $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Count query dengan prepared statement
    $countStmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM berita WHERE kategori = ?");
    mysqli_stmt_bind_param($countStmt, "s", $kategori);
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $totalRows = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($totalRows / $limit);
    
    return [
        'result' => $result,
        'totalPages' => $totalPages,
        'totalRows' => $totalRows
    ];
}

// Validasi dan sanitasi parameter
$limit = 4;
$pageSekolah = isset($_GET['page_sekolah']) ? max(1, (int) $_GET['page_sekolah']) : 1;
$pageDinas = isset($_GET['page_dinas']) ? max(1, (int) $_GET['page_dinas']) : 1;

$dataSekolah = getPaginationData($conn, 'sekolah', $limit, $pageSekolah);
$dataDinas = getPaginationData($conn, 'dinas', $limit, $pageDinas);

// Query kegiatan sekolah inklusi terbaru (4 data) dengan prepared statement
$stmt = mysqli_prepare($conn, "
    SELECT info.*, sekolah.nama_sekolah 
    FROM info_sekolah_inklusi AS info
    LEFT JOIN data_sekolah_inklusi AS sekolah ON info.sekolah_id = sekolah.id
    ORDER BY info.tanggal DESC
    LIMIT 4
");
mysqli_stmt_execute($stmt);
$qKegiatan = mysqli_stmt_get_result($stmt);

// Fungsi helper untuk sanitasi output
function sanitizeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Fungsi untuk format tanggal
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

// Fungsi untuk truncate text
function truncateText($text, $length = 100, $suffix = '...') {
    $text = strip_tags($text);
    return mb_strimwidth($text, 0, $length, $suffix, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Sistem Informasi Sekolah Inklusi</title>
    <meta name="description" content="Portal berita dan informasi sekolah inklusi">
</head>
<body>
<div id="background">
    <?php include 'partials/sidebar.php'; ?>

    <main>
        <div class="container-fluid px-4">
            <div class="page-header">
                <h1 class="page-title">Beranda</h1>
                <p class="page-subtitle">Portal informasi sekolah inklusi Kota Semarang</p>
            </div>

            <!-- Search & Filter Section -->
            <div class="search-filter-section">
                <div class="search-filter-container">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Cari judul/isi berita..." id="searchBeritaInput" aria-label="Pencarian berita">
                    </div>

                    <select class="filter-select" id="kategoriBeritaFilter" aria-label="Filter kategori berita">
                        <option value="">Semua Kategori</option>
                        <option value="dinas">Berita Dinas</option>
                        <option value="sekolah">Berita Sekolah</option>
                    </select>

                    <select class="filter-select" id="sortBeritaFilter" aria-label="Urutkan berita">
                        <option value="">Urutkan berdasarkan</option>
                        <option value="judul">Judul A-Z</option>
                        <option value="baru">Tanggal Terbaru</option>
                        <option value="lama">Tanggal Terlama</option>
                    </select>

                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                        <a href="features/berita/tambah_berita.php" class="btn-add" aria-label="Tambah berita baru">
                            <i class="fas fa-plus"></i>
                            Tambah Berita
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Berita Sekolah Section -->
            <section class="news-section" id="beritaSekolahSection">
                <div class="section-header">
                    <h2>Berita Sekolah</h2>
                </div>
                <div class="news-grid berita-list" role="region" aria-label="Daftar berita sekolah">
                    <?php
                    $adaSekolah = false;
                    while ($b = mysqli_fetch_assoc($dataSekolah['result'])): 
                        $adaSekolah = true; 
                        $judulSanitized = sanitizeOutput($b['judul']);
                        $isiSanitized = sanitizeOutput($b['isi']);
                    ?>
                        <article class="news-card berita-card"
                            data-judul="<?= strtolower($judulSanitized) ?>"
                            data-isi="<?= strtolower(strip_tags($b['isi'])) ?>"
                            data-kategori="sekolah"
                            data-tanggal="<?= $b['created_at'] ?>">

                            <div class="news-image-container">
                                <?php if (!empty($b['gambar']) && file_exists("upload/berita/" . $b['gambar'])): ?>
                                    <img src="upload/berita/<?= sanitizeOutput($b['gambar']) ?>" 
                                         loading="lazy" 
                                         class="news-image" 
                                         alt="<?= $judulSanitized ?>"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <?php else: ?>
                                    <div class="news-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="news-badge sekolah">SEKOLAH</div>
                            </div>

                            <div class="news-content">
                                <div class="news-meta">
                                    <time class="news-date" datetime="<?= $b['created_at'] ?>">
                                        <?= formatDate($b['created_at']) ?>
                                    </time>
                                    <span class="news-category">BERITA SEKOLAH</span>
                                </div>

                                <h3 class="news-title"><?= $judulSanitized ?></h3>

                                <p class="news-excerpt"><?= truncateText($b['isi']) ?></p>

                                <div class="news-actions">
                                    <a href="features/berita/detail_berita.php?id=<?= (int)$b['id'] ?>" 
                                       class="read-more-btn"
                                       aria-label="Baca selengkapnya: <?= $judulSanitized ?>">
                                        Baca selengkapnya <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                                        <div class="admin-actions">
                                            <a href="features/berita/edit_berita.php?id=<?= (int)$b['id'] ?>" 
                                               class="btn-edit" 
                                               title="Edit berita"
                                               aria-label="Edit berita: <?= $judulSanitized ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="features/berita/hapus_berita.php?id=<?= (int)$b['id'] ?>" 
                                               class="btn-delete"
                                               onclick="return confirm('Yakin ingin menghapus berita ini?')" 
                                               title="Hapus berita"
                                               aria-label="Hapus berita: <?= $judulSanitized ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;
                    if (!$adaSekolah): ?>
                        <div class="empty-state">
                            <i class="fas fa-newspaper empty-icon" aria-hidden="true"></i>
                            <p>Belum ada berita sekolah</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination Berita Sekolah -->
                <?php if ($dataSekolah['totalPages'] > 1): ?>
                <nav class="pagination mt-3" aria-label="Navigasi halaman berita sekolah">
                    <?php for ($i = 1; $i <= $dataSekolah['totalPages']; $i++): ?>
                        <a href="?page_sekolah=<?= $i ?>&page_dinas=<?= $pageDinas ?>" 
                           class="btn <?= ($i == $pageSekolah) ? 'btn-primary' : 'btn-light' ?>"
                           <?= ($i == $pageSekolah) ? 'aria-current="page"' : '' ?>>
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
                <?php endif; ?>
            </section>

            <!-- Berita Dinas Section -->
            <section class="news-section mt-5" id="beritaDinasSection">
                <div class="section-header">
                    <h2>Berita Dinas</h2>
                </div>
                <div class="news-grid berita-list" role="region" aria-label="Daftar berita dinas">
                    <?php
                    $adaDinas = false;
                    while ($b = mysqli_fetch_assoc($dataDinas['result'])): 
                        $adaDinas = true;
                        $judulSanitized = sanitizeOutput($b['judul']);
                        $isiSanitized = sanitizeOutput($b['isi']);
                    ?>
                        <article class="news-card berita-card"
                            data-judul="<?= strtolower($judulSanitized) ?>"
                            data-isi="<?= strtolower(strip_tags($b['isi'])) ?>"
                            data-kategori="dinas"
                            data-tanggal="<?= $b['created_at'] ?>">

                            <div class="news-image-container">
                                <?php if (!empty($b['gambar']) && file_exists("upload/berita/" . $b['gambar'])): ?>
                                    <img src="upload/berita/<?= sanitizeOutput($b['gambar']) ?>" 
                                         loading="lazy" 
                                         class="news-image" 
                                         alt="<?= $judulSanitized ?>"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <?php else: ?>
                                    <div class="news-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="news-badge dinas">DINAS</div>
                            </div>

                            <div class="news-content">
                                <div class="news-meta">
                                    <time class="news-date" datetime="<?= $b['created_at'] ?>">
                                        <?= formatDate($b['created_at']) ?>
                                    </time>
                                    <span class="news-category">BERITA DINAS</span>
                                </div>

                                <h3 class="news-title"><?= $judulSanitized ?></h3>

                                <p class="news-excerpt"><?= truncateText($b['isi']) ?></p>

                                <div class="news-actions">
                                    <a href="features/berita/detail_berita.php?id=<?= (int)$b['id'] ?>" 
                                       class="read-more-btn"
                                       aria-label="Baca selengkapnya: <?= $judulSanitized ?>">
                                        Baca selengkapnya <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                                        <div class="admin-actions">
                                            <a href="features/berita/edit_berita.php?id=<?= (int)$b['id'] ?>" 
                                               class="btn-edit" 
                                               title="Edit berita"
                                               aria-label="Edit berita: <?= $judulSanitized ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="features/berita/hapus_berita.php?id=<?= (int)$b['id'] ?>" 
                                               class="btn-delete"
                                               onclick="return confirm('Yakin ingin menghapus berita ini?')" 
                                               title="Hapus berita"
                                               aria-label="Hapus berita: <?= $judulSanitized ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;
                    if (!$adaDinas): ?>
                        <div class="empty-state">
                            <i class="fas fa-newspaper empty-icon" aria-hidden="true"></i>
                            <p>Belum ada berita dinas</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination Berita Dinas -->
                <?php if ($dataDinas['totalPages'] > 1): ?>
                <nav class="pagination mt-3" aria-label="Navigasi halaman berita dinas">
                    <?php for ($i = 1; $i <= $dataDinas['totalPages']; $i++): ?>
                        <a href="?page_dinas=<?= $i ?>&page_sekolah=<?= $pageSekolah ?>" 
                           class="btn <?= ($i == $pageDinas) ? 'btn-primary' : 'btn-light' ?>"
                           <?= ($i == $pageDinas) ? 'aria-current="page"' : '' ?>>
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
                <?php endif; ?>
            </section>

            <!-- Kegiatan Sekolah Inklusi Section -->
            <section class="news-section mt-5" id="kegiatanSekolahSection">
                <div class="section-header">
                    <h2>Kegiatan Sekolah Inklusi</h2>
                </div>
                <div class="news-grid" role="region" aria-label="Daftar kegiatan sekolah inklusi">
                    <?php
                    $adaKegiatan = false;
                    while ($k = mysqli_fetch_assoc($qKegiatan)): 
                        $adaKegiatan = true;
                        $namaKegiatanSanitized = sanitizeOutput($k['nama_kegiatan']);
                        $namaSekolahSanitized = sanitizeOutput($k['nama_sekolah'] ?? 'Tidak diketahui');
                    ?>
                        <article class="news-card">
                            <div class="news-image-container">
                                <?php if (!empty($k['foto']) && file_exists("upload/info_sekolah/" . $k['foto'])): ?>
                                    <img src="upload/info_sekolah/<?= sanitizeOutput($k['foto']) ?>" 
                                         loading="lazy" 
                                         class="news-image" 
                                         alt="<?= $namaKegiatanSanitized ?>" 
                                         style="height:200px;object-fit:cover;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <?php else: ?>
                                    <div class="news-image-placeholder" style="height:200px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="news-content">
                                <div class="news-meta">
                                    <time class="news-date" datetime="<?= $k['tanggal'] ?>">
                                        <?= formatDate($k['tanggal'], 'd M Y') ?>
                                    </time>
                                    <span class="news-category"><?= $namaSekolahSanitized ?></span>
                                </div>
                                <h3 class="news-title"><?= $namaKegiatanSanitized ?></h3>
                            </div>
                        </article>
                    <?php endwhile;
                    if (!$adaKegiatan): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt empty-icon" aria-hidden="true"></i>
                            <p>Belum ada kegiatan sekolah inklusi</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</div>

<style>
    /* Global Styles */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    #background {
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    /* Focus styles for accessibility */
    *:focus {
        outline: 2px solid #4a90e2;
        outline-offset: 2px;
    }

    /* Page Header */
    .page-header {
        text-align: center;
        margin-bottom: 3rem;
        padding: 2rem 0;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        font-size: 1.1rem;
        color: #6c757d;
        margin: 0;
    }

    /* Search & Filter Section */
    .search-filter-section {
        margin-bottom: 3rem;
    }

    .search-filter-container {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 300px;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }

    .search-input:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .filter-select {
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: white;
        color: #495057;
        font-size: 0.95rem;
        min-width: 160px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .btn-add {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #4a90e2;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-add:hover,
    .btn-add:focus {
        background: #357abd;
        color: white;
        text-decoration: none;
    }

    /* News Section */
    .news-section {
        margin-bottom: 4rem;
    }

    .section-header {
        margin-bottom: 2rem;
    }

    .section-header h2 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        border-bottom: 3px solid #4a90e2;
        padding-bottom: 0.5rem;
        display: inline-block;
    }

    /* News Grid */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
    }

    /* News Card */
    .news-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
    }

    .news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .news-image-container {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: #f8f9fa;
    }

    .news-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-card:hover .news-image {
        transform: scale(1.05);
    }

    .news-image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #e9ecef, #dee2e6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 2rem;
    }

    .news-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: #dc3545;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 1;
    }

    .news-badge.sekolah {
        background: #28a745;
    }

    .news-badge.dinas {
        background: #dc3545;
    }

    /* News Content */
    .news-content {
        padding: 1.5rem;
    }

    .news-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.5px;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .news-date {
        color: #6c757d;
    }

    .news-category {
        color: #495057;
        background: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .news-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.4;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-excerpt {
        color: #6c757d;
        line-height: 1.5;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .news-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .read-more-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #4a90e2;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        padding: 0.5rem 0;
    }

    .read-more-btn:hover,
    .read-more-btn:focus {
        color: #357abd;
        text-decoration: none;
    }

    .read-more-btn i {
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }

    .read-more-btn:hover i {
        transform: translateX(2px);
    }

    .admin-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-edit,
    .btn-delete {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 0.8rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-edit {
        background: #ffc107;
        color: white;
    }

    .btn-edit:hover,
    .btn-edit:focus {
        background: #e0a800;
        color: white;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .btn-delete:hover,
    .btn-delete:focus {
        background: #c82333;
        color: white;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: #6c757d;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Pagination */
    .pagination {
        margin-top: 2rem;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination .btn {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        text-decoration: none;
        border: 1px solid #dee2e6;
        color: #495057;
        background: white;
        font-weight: 600;
        transition: all 0.3s ease;
        min-width: 40px;
        text-align: center;
    }

    .pagination .btn-primary {
        background-color: #4a90e2;
        color: white;
        border-color: #4a90e2;
    }

    .pagination .btn:hover,
    .pagination .btn:focus {
        background-color: #e9ecef;
        color: #495057;
        text-decoration: none;
    }

    .pagination .btn-primary:hover,
    .pagination .btn-primary:focus {
        background-color: #357abd;
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .search-filter-container {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            min-width: auto;
        }

        .filter-select,
        .btn-add {
            min-width: auto;
        }

        .news-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .news-actions {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .news-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }

        .section-header h2 {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .news-content {
            padding: 1rem;
        }

        .search-filter-container {
            padding: 1rem;
        }

        .news-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .news-title {
            font-size: 1.1rem;
        }

        .pagination {
            gap: 0.25rem;
        }

        .pagination .btn {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
    }

    /* Loading and error states */
    .loading-state {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .error-state {
        text-align: center;
        padding: 2rem;
        color: #dc3545;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        margin: 1rem 0;
    }

    /* Skip to content for accessibility */
    .skip-to-content {
        position: absolute;
        top: -40px;
        left: 6px;
        background: #4a90e2;
        color: white;
        padding: 8px;
        text-decoration: none;
        border-radius: 4px;
        z-index: 1000;
    }

    .skip-to-content:focus {
        top: 6px;
    }

    /* Print styles */
    @media print {
        .search-filter-section,
        .admin-actions,
        .pagination {
            display: none;
        }
        
        .news-card {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .news-image {
            max-height: 150px;
        }
    }
</style>

<!-- JavaScript untuk filtering dan search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchBeritaInput');
    const kategoriFilter = document.getElementById('kategoriBeritaFilter');
    const sortFilter = document.getElementById('sortBeritaFilter');
    const beritaCards = document.querySelectorAll('.berita-card');

    // Debounce function untuk search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Function untuk filter dan search
    function filterBerita() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedKategori = kategoriFilter.value;
        const selectedSort = sortFilter.value;

        // Dapatkan semua section berita
        const sekolahSection = document.getElementById('beritaSekolahSection');
        const dinasSection = document.getElementById('beritaDinasSection');
        
        // Dapatkan kartu untuk setiap section
        const sekolahCards = sekolahSection.querySelectorAll('.berita-card');
        const dinasCards = dinasSection.querySelectorAll('.berita-card');

        // Function untuk mengecek apakah kartu sesuai dengan filter
        function matchesFilter(card) {
            const judul = (card.dataset.judul || '').toLowerCase();
            const isi = (card.dataset.isi || '').toLowerCase();
            const kategori = card.dataset.kategori || '';

            const matchesSearch = searchTerm === '' || 
                                judul.includes(searchTerm) || 
                                isi.includes(searchTerm);

            const matchesKategori = selectedKategori === '' || 
                                  kategori === selectedKategori;

            return matchesSearch && matchesKategori;
        }

        // Function untuk sorting
        function sortCards(cards) {
            return Array.from(cards).sort((a, b) => {
                if (selectedSort === 'judul') {
                    const judulA = (a.dataset.judul || '').toLowerCase();
                    const judulB = (b.dataset.judul || '').toLowerCase();
                    return judulA.localeCompare(judulB);
                } else if (selectedSort === 'baru') {
                    return new Date(b.dataset.tanggal) - new Date(a.dataset.tanggal);
                } else if (selectedSort === 'lama') {
                    return new Date(a.dataset.tanggal) - new Date(b.dataset.tanggal);
                }
                return 0;
            });
        }

        // Filter dan sort untuk setiap section
        function updateSection(section, cards) {
            const newsGrid = section.querySelector('.news-grid');
            let visibleCount = 0;

            // Filter dan sort cards
            const sortedCards = sortCards(Array.from(cards).filter(matchesFilter));
            
            // Update visibility dan order
            cards.forEach(card => {
                const isVisible = sortedCards.includes(card);
                card.style.display = isVisible ? 'block' : 'none';
                if (isVisible) {
                    card.style.order = sortedCards.indexOf(card);
                    visibleCount++;
                }
            });

            // Update empty state
            const emptyState = section.querySelector('.empty-state');
            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            return visibleCount;
        }

        // Update kedua section
        const sekolahVisible = updateSection(sekolahSection, sekolahCards);
        const dinasVisible = updateSection(dinasSection, dinasCards);

        // Tampilkan pesan jika tidak ada hasil sama sekali
        const noResults = !sekolahVisible && !dinasVisible;
        if (noResults) {
            if (!document.querySelector('.no-results-message')) {
                const message = document.createElement('div');
                message.className = 'no-results-message empty-state';
                message.innerHTML = '<i class="fas fa-search empty-icon" aria-hidden="true"></i><p>Tidak ada berita yang sesuai dengan filter</p>';
                document.querySelector('.container-fluid').insertBefore(message, sekolahSection);
            }
        } else {
            const message = document.querySelector('.no-results-message');
            if (message) message.remove();
        }
    }

    // Tambahkan empty state elements saat inisialisasi
    function initializeEmptyStates() {
        const sections = ['beritaSekolahSection', 'beritaDinasSection'];
        sections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            const newsGrid = section.querySelector('.news-grid');
            
            // Tambahkan empty state jika belum ada
            if (!section.querySelector('.empty-state')) {
                const emptyState = document.createElement('div');
                emptyState.className = 'empty-state';
                emptyState.style.display = 'none';
                emptyState.innerHTML = `
                    <i class="fas fa-search empty-icon" aria-hidden="true"></i>
                    <p>Tidak ada berita ${sectionId === 'beritaSekolahSection' ? 'sekolah' : 'dinas'} yang sesuai dengan filter</p>
                `;
                newsGrid.appendChild(emptyState);
            }
        });
    }

    // Panggil inisialisasi empty states
    initializeEmptyStates();

    // Event listeners dengan debounce untuk search
    const debouncedFilter = debounce(() => {
        filterBerita();
        // Simpan state filter ke sessionStorage
        sessionStorage.setItem('filterState', JSON.stringify({
            search: searchInput.value,
            kategori: kategoriFilter.value,
            sort: sortFilter.value
        }));
    }, 300);

    // Attach event listeners
    searchInput.addEventListener('input', debouncedFilter);
    kategoriFilter.addEventListener('change', debouncedFilter);
    sortFilter.addEventListener('change', debouncedFilter);

    // Restore filter state jika ada
    const savedState = sessionStorage.getItem('filterState');
    if (savedState) {
        const state = JSON.parse(savedState);
        searchInput.value = state.search || '';
        kategoriFilter.value = state.kategori || '';
        sortFilter.value = state.sort || '';
        filterBerita(); // Apply filter
    }

    // Error handling untuk gambar yang gagal dimuat
    document.querySelectorAll('.news-image').forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const placeholder = this.nextElementSibling;
            if (placeholder && placeholder.classList.contains('news-image-placeholder')) {
                placeholder.style.display = 'flex';
            }
        });
    });

    // Lazy loading untuk gambar (fallback jika browser tidak support)
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Keyboard navigation untuk cards
    document.querySelectorAll('.news-card').forEach(card => {
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const readMoreBtn = this.querySelector('.read-more-btn');
                if (readMoreBtn) {
                    e.preventDefault();
                    readMoreBtn.click();
                }
            }
        });
    });
});

// Function untuk konfirmasi hapus yang lebih accessible
function confirmDelete(title) {
    return confirm(`Apakah Anda yakin ingin menghapus berita "${title}"? Tindakan ini tidak dapat dibatalkan.`);
}
</script>

<?php include 'partials/footer.php'; ?>
</body>
</html>