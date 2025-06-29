<?php
session_start();
include 'koneksi.php';
include 'partials/head.php';

// Ambil 4 kegiatan terbaru dari info_sekolah_inklusi
$qKegiatan = mysqli_query($conn, "
    SELECT info.*, sekolah.nama_sekolah 
    FROM info_sekolah_inklusi AS info
    LEFT JOIN data_sekolah_inklusi AS sekolah ON info.sekolah_id = sekolah.id
    ORDER BY info.tanggal DESC
    LIMIT 4
");
?>
<div id="background">
    <?php include 'sidebar.php'; ?>

    <main>
        <div class="container-fluid px-4">
            <!-- Header Section -->
            <div class="page-header">
                <h1 class="page-title">Beranda</h1>
                <p class="page-subtitle">Info terbaru mengenai sekolah inklusi</p>
            </div>

            <!-- Search & Filter Section -->
            <div class="search-filter-section">
                <div class="search-filter-container">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Cari judul/isi berita..." id="searchBeritaInput">
                    </div>

                    <select class="filter-select" id="kategoriBeritaFilter">
                        <option value="">Semua Kategori</option>
                        <option value="dinas">Berita Dinas</option>
                        <option value="sekolah">Berita Sekolah</option>
                    </select>

                    <select class="filter-select" id="sortBeritaFilter">
                        <option value="">Urutkan berdasarkan</option>
                        <option value="judul">Judul A-Z</option>
                        <option value="baru">Tanggal Terbaru</option>
                        <option value="lama">Tanggal Terlama</option>
                    </select>

                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                        <a href="tambah_berita.php" class="btn-add">
                            <i class="fas fa-plus"></i>
                            Tambah Berita
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Berita Sekolah Section -->
            <div class="news-section" id="beritaSekolahSection">
                <div class="section-header">
                    <div class="section-icon-title">
                        <i class="fas fa-graduation-cap section-icon"></i>
                        <h2 class="section-title">Berita Sekolah</h2>
                    </div>
                </div>

                <div class="news-grid berita-list">
                    <?php
                    $qSekolah = mysqli_query($conn, "SELECT * FROM berita WHERE kategori='sekolah' ORDER BY created_at DESC LIMIT 3");
                    $adaSekolah = false;
                    while ($b = mysqli_fetch_assoc($qSekolah)): $adaSekolah = true; ?>
                        <div class="news-card berita-card"
                            data-judul="<?= strtolower(htmlspecialchars($b['judul'])) ?>"
                            data-isi="<?= strtolower(strip_tags($b['isi'])) ?>"
                            data-kategori="sekolah"
                            data-tanggal="<?= $b['created_at'] ?>">

                            <!-- News Image -->
                            <div class="news-image-container">
                                <?php if (!empty($b['gambar']) && file_exists("upload/berita/" . $b['gambar'])): ?>
                                    <img src="upload/berita/<?= htmlspecialchars($b['gambar']) ?>" class="news-image" alt="<?= htmlspecialchars($b['judul']) ?>">
                                <?php else: ?>
                                    <div class="news-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="news-badge sekolah">BERITA</div>
                            </div>

                            <!-- News Content -->
                            <div class="news-content">
                                <div class="news-meta">
                                    <span class="news-date"><?= date('M d, Y', strtotime($b['created_at'])) ?></span>
                                    <span class="news-category">BERITA SEKOLAH</span>
                                </div>

                                <h3 class="news-title"><?= htmlspecialchars($b['judul']) ?></h3>

                                <p class="news-excerpt"><?= htmlspecialchars(mb_strimwidth(strip_tags($b['isi']), 0, 100, '...')) ?></p>

                                <div class="news-actions">
                                    <a href="detail_berita.php?id=<?= $b['id'] ?>" class="read-more-btn">
                                        Read more <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                                        <div class="admin-actions">
                                            <a href="edit_berita.php?id=<?= $b['id'] ?>" class="btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="hapus_berita.php?id=<?= $b['id'] ?>" class="btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus berita ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile;
                    if (!$adaSekolah): ?>
                        <div class="empty-state">
                            <i class="fas fa-newspaper empty-icon"></i>
                            <p>Belum ada berita sekolah</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Berita Dinas Section -->
            <div class="news-section" id="beritaDinasSection">
                <div class="section-header">
                    <div class="section-icon-title">
                        <i class="fas fa-bullhorn section-icon"></i>
                        <h2 class="section-title">Berita Dinas</h2>
                    </div>
                </div>

                <div class="news-grid berita-list">
                    <?php
                    $qDinas = mysqli_query($conn, "SELECT * FROM berita WHERE kategori='dinas' ORDER BY created_at DESC LIMIT 3");
                    $adaDinas = false;
                    while ($b = mysqli_fetch_assoc($qDinas)): $adaDinas = true; ?>
                        <div class="news-card berita-card"
                            data-judul="<?= strtolower(htmlspecialchars($b['judul'])) ?>"
                            data-isi="<?= strtolower(strip_tags($b['isi'])) ?>"
                            data-kategori="dinas"
                            data-tanggal="<?= $b['created_at'] ?>">

                            <!-- News Image -->
                            <div class="news-image-container">
                                <?php if (!empty($b['gambar']) && file_exists("upload/berita/" . $b['gambar'])): ?>
                                    <img src="upload/berita/<?= htmlspecialchars($b['gambar']) ?>" class="news-image" alt="<?= htmlspecialchars($b['judul']) ?>">
                                <?php else: ?>
                                    <div class="news-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="news-badge dinas">BERITA</div>
                            </div>

                            <!-- News Content -->
                            <div class="news-content">
                                <div class="news-meta">
                                    <span class="news-date"><?= date('M d, Y', strtotime($b['created_at'])) ?></span>
                                    <span class="news-category">BERITA DINAS</span>
                                </div>

                                <h3 class="news-title"><?= htmlspecialchars($b['judul']) ?></h3>

                                <p class="news-excerpt"><?= htmlspecialchars(mb_strimwidth(strip_tags($b['isi']), 0, 100, '...')) ?></p>

                                <div class="news-actions">
                                    <a href="detail_berita.php?id=<?= $b['id'] ?>" class="read-more-btn">
                                        Read more <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                                        <div class="admin-actions">
                                            <a href="edit_berita.php?id=<?= $b['id'] ?>" class="btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="hapus_berita.php?id=<?= $b['id'] ?>" class="btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus berita ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile;
                    if (!$adaDinas): ?>
                        <div class="empty-state">
                            <i class="fas fa-newspaper empty-icon"></i>
                            <p>Belum ada berita dinas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kegiatan Sekolah Inklusi Section -->
            <div class="news-section" id="kegiatanSekolahSection">
                <div class="section-header">
                    <div class="section-icon-title">
                        <i class="fas fa-calendar-alt section-icon"></i>
                        <h2 class="section-title">Kegiatan Sekolah Inklusi</h2>
                    </div>
                </div>
                <div class="news-grid">
                    <?php
                    $adaKegiatan = false;
                    while ($k = mysqli_fetch_assoc($qKegiatan)): $adaKegiatan = true; ?>
                        <div class="news-card">
                            <?php if (!empty($k['foto']) && file_exists("uploads/" . $k['foto'])): ?>
                                <img src="uploads/<?= htmlspecialchars($k['foto']) ?>" class="news-image" alt="<?= htmlspecialchars($k['nama_kegiatan']) ?>" style="height:200px;object-fit:cover;">
                            <?php else: ?>
                                <div class="news-image-placeholder" style="height:200px;">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="news-content">
                                <div class="news-meta">
                                    <span class="news-date"><?= date('d M Y', strtotime($k['tanggal'])) ?></span>
                                    <span class="news-category"><?= htmlspecialchars($k['nama_sekolah']) ?></span>
                                </div>
                                <h3 class="news-title"><?= htmlspecialchars($k['nama_kegiatan']) ?></h3>
                            </div>
                        </div>
                    <?php endwhile;
                    if (!$adaKegiatan): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt empty-icon"></i>
                            <p>Belum ada kegiatan sekolah inklusi</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchBeritaInput');
        const kategoriFilter = document.getElementById('kategoriBeritaFilter');
        const sortFilter = document.getElementById('sortBeritaFilter');
        const beritaCards = document.querySelectorAll('.berita-card');
        const beritaSections = document.querySelectorAll('.news-section');

        function filterBerita() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const kategori = kategoriFilter.value;
            const sortBy = sortFilter.value;

            beritaSections.forEach(section => {
                let cards = Array.from(section.querySelectorAll('.berita-card'));

                // Filter cards
                let visibleCards = cards.filter(card => {
                    const judul = card.getAttribute('data-judul');
                    const isi = card.getAttribute('data-isi');
                    const kat = card.getAttribute('data-kategori');

                    const matchSearch = !searchTerm || judul.includes(searchTerm) || isi.includes(searchTerm);
                    const matchKategori = !kategori || kat === kategori;

                    return matchSearch && matchKategori;
                });

                // Hide all cards first
                cards.forEach(card => {
                    card.style.display = 'none';
                });

                // Show filtered cards
                visibleCards.forEach(card => {
                    card.style.display = 'block';
                });

                // Sort visible cards
                if (sortBy && visibleCards.length > 1) {
                    visibleCards.sort((a, b) => {
                        if (sortBy === 'judul') {
                            return a.getAttribute('data-judul').localeCompare(b.getAttribute('data-judul'));
                        } else if (sortBy === 'baru') {
                            return new Date(b.getAttribute('data-tanggal')) - new Date(a.getAttribute('data-tanggal'));
                        } else if (sortBy === 'lama') {
                            return new Date(a.getAttribute('data-tanggal')) - new Date(b.getAttribute('data-tanggal'));
                        }
                        return 0;
                    });

                    const parent = visibleCards[0].parentNode;
                    visibleCards.forEach(card => parent.appendChild(card));
                }
            });
        }

        searchInput.addEventListener('input', filterBerita);
        kategoriFilter.addEventListener('change', filterBerita);
        sortFilter.addEventListener('change', filterBerita);
    });
</script>

<style>
    /* Global Styles */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background-color: #f8f9fa;
    }

    #background {
        background-color: #f8f9fa;
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
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
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
    }

    .filter-select:focus {
        outline: none;
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
    }

    .btn-add:hover {
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

    .section-icon-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        width: 24px;
        height: 24px;
        color: #495057;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    /* News Grid */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }

    /* News Card */
    .news-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .news-image-container {
        position: relative;
        height: 200px;
        overflow: hidden;
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
    }

    .news-date {
        color: #6c757d;
    }

    .news-category {
        color: #495057;
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
    }

    .news-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    }

    .read-more-btn:hover {
        color: #357abd;
        text-decoration: none;
    }

    .read-more-btn i {
        font-size: 0.8rem;
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
    }

    .btn-edit {
        background: #ffc107;
        color: white;
    }

    .btn-edit:hover {
        background: #e0a800;
        color: white;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background: #c82333;
        color: white;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
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
    }
</style>
<?php include 'partials/footer.php'; ?>
</body>
</html>