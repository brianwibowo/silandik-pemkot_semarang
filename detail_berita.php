<?php
session_start();
include 'koneksi.php';
include 'partials/head.php';

// Get news ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: beranda.php');
    exit;
}

// Fetch news data
$query = mysqli_query($conn, "SELECT * FROM berita WHERE id = $id");
if (!$query || mysqli_num_rows($query) == 0) {
    header('Location: beranda.php');
    exit;
}

$berita = mysqli_fetch_assoc($query);
?>

<div id="background">
    <?php include 'sidebar.php'; ?>

    <main>
        <div class="container-fluid px-4">
            <!-- Navigation Header -->
            <div class="navigation-header">
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Beranda</span>
                </a>
                
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                    <div class="admin-controls">
                        <a href="edit_berita.php?id=<?= $berita['id'] ?>" class="btn-edit-detail">
                            <i class="fas fa-edit"></i>
                            Edit Berita
                        </a>
                        <a href="hapus_berita.php?id=<?= $berita['id'] ?>" class="btn-delete-detail"
                           onclick="return confirm('Yakin ingin menghapus berita ini?')">
                            <i class="fas fa-trash"></i>
                            Hapus Berita
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Article Content -->
            <article class="article-container">
                <!-- Article Header -->
                <header class="article-header">
                    <div class="article-meta">
                        <span class="article-category <?= $berita['kategori'] ?>">
                            <?= $berita['kategori'] == 'sekolah' ? 'BERITA SEKOLAH' : 'BERITA DINAS' ?>
                        </span>
                        <span class="article-date">
                            <i class="fas fa-calendar-alt"></i>
                            <?= date('l, d F Y', strtotime($berita['created_at'])) ?>
                        </span>
                    </div>
                    
                    <h1 class="article-title"><?= htmlspecialchars($berita['judul']) ?></h1>
                </header>

                <!-- Article Image -->
                <?php if (!empty($berita['gambar']) && file_exists("upload/berita/" . $berita['gambar'])): ?>
                    <div class="article-image-container">
                        <img src="upload/berita/<?= htmlspecialchars($berita['gambar']) ?>" 
                             class="article-image" 
                             alt="<?= htmlspecialchars($berita['judul']) ?>">
                        <div class="image-overlay"></div>
                    </div>
                <?php endif; ?>

                <!-- Article Body -->
                <div class="article-body">
                    <div class="article-content">
                        <?= nl2br(htmlspecialchars($berita['isi'])) ?>
                    </div>
                </div>

                <!-- Article Footer -->
                <footer class="article-footer">
                    <div class="article-info">
                        <div class="publish-info">
                            <i class="fas fa-clock"></i>
                            <span>Dipublikasikan pada <?= date('d F Y \p\u\k\u\l H:i', strtotime($berita['created_at'])) ?> WIB</span>
                        </div>
                        
                        <?php if (!empty($berita['updated_at']) && $berita['updated_at'] != $berita['created_at']): ?>
                            <div class="update-info">
                                <i class="fas fa-edit"></i>
                                <span>Terakhir diperbarui <?= date('d F Y \p\u\k\u\l H:i', strtotime($berita['updated_at'])) ?> WIB</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </footer>
            </article>

            <!-- Related News Section -->
            <section class="related-news-section">
                <h2 class="related-title">Berita Terkait</h2>
                <div class="related-news-grid">
                    <?php
                    $relatedQuery = mysqli_query($conn, "SELECT * FROM berita WHERE kategori = '{$berita['kategori']}' AND id != {$berita['id']} ORDER BY created_at DESC LIMIT 3");
                    $hasRelated = false;
                    
                    while ($related = mysqli_fetch_assoc($relatedQuery)): 
                        $hasRelated = true;
                    ?>
                        <div class="related-news-card">
                            <div class="related-image-container">
                                <?php if (!empty($related['gambar']) && file_exists("upload/berita/" . $related['gambar'])): ?>
                                    <img src="upload/berita/<?= htmlspecialchars($related['gambar']) ?>" 
                                         class="related-image" 
                                         alt="<?= htmlspecialchars($related['judul']) ?>">
                                <?php else: ?>
                                    <div class="related-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="related-content">
                                <div class="related-meta">
                                    <span class="related-date"><?= date('M d, Y', strtotime($related['created_at'])) ?></span>
                                </div>
                                <h3 class="related-title-text">
                                    <a href="detail_berita.php?id=<?= $related['id'] ?>">
                                        <?= htmlspecialchars($related['judul']) ?>
                                    </a>
                                </h3>
                                <p class="related-excerpt">
                                    <?= htmlspecialchars(mb_strimwidth(strip_tags($related['isi']), 0, 80, '...')) ?>
                                </p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php if (!$hasRelated): ?>
                        <div class="no-related">
                            <i class="fas fa-newspaper"></i>
                            <p>Tidak ada berita terkait lainnya</p>
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
        line-height: 1.6;
    }

    #background {
        background-color: #f8f9fa;
    }

    /* Navigation Header */
    .navigation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1rem 0;
    }

    .back-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }

    .back-btn:hover {
        color: #4a90e2;
        background-color: rgba(74, 144, 226, 0.1);
        text-decoration: none;
    }

    .admin-controls {
        display: flex;
        gap: 0.75rem;
    }

    .btn-edit-detail,
    .btn-delete-detail {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-edit-detail {
        background: #ffc107;
        color: white;
    }

    .btn-edit-detail:hover {
        background: #e0a800;
        color: white;
        text-decoration: none;
    }

    .btn-delete-detail {
        background: #dc3545;
        color: white;
    }

    .btn-delete-detail:hover {
        background: #c82333;
        color: white;
        text-decoration: none;
    }

    /* Article Container */
    .article-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 3rem;
    }

    /* Article Header */
    .article-header {
        padding: 2rem 2rem 1rem;
    }

    .article-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .article-category {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: white;
    }

    .article-category.sekolah {
        background: #28a745;
    }

    .article-category.dinas {
        background: #dc3545;
    }

    .article-date {
        color: #6c757d;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .article-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.3;
        margin: 0;
    }

    /* Article Image */
    .article-image-container {
        position: relative;
        height: 400px;
        overflow: hidden;
        margin: 0 2rem;
        border-radius: 8px;
    }

    .article-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.1) 100%);
    }

    /* Article Body */
    .article-body {
        padding: 2rem;
    }

    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #374151;
        max-width: none;
    }

    .article-content p {
        margin-bottom: 1.5rem;
    }

    /* Article Footer */
    .article-footer {
        padding: 1.5rem 2rem 2rem;
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .article-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .publish-info,
    .update-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    /* Related News Section */
    .related-news-section {
        margin-bottom: 3rem;
    }

    .related-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .related-title::before {
        content: '';
        width: 4px;
        height: 24px;
        background: #4a90e2;
        border-radius: 2px;
    }

    .related-news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .related-news-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .related-news-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .related-image-container {
        height: 150px;
        overflow: hidden;
    }

    .related-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .related-news-card:hover .related-image {
        transform: scale(1.05);
    }

    .related-image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #e9ecef, #dee2e6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 1.5rem;
    }

    .related-content {
        padding: 1rem;
    }

    .related-meta {
        margin-bottom: 0.5rem;
    }

    .related-date {
        color: #6c757d;
        font-size: 0.8rem;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .related-title-text {
        margin: 0 0 0.5rem 0;
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.4;
    }

    .related-title-text a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .related-title-text a:hover {
        color: #4a90e2;
    }

    .related-excerpt {
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }

    .no-related {
        grid-column: 1 / -1;
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .no-related i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .navigation-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .admin-controls {
            justify-content: center;
        }

        .article-header {
            padding: 1.5rem 1rem 1rem;
        }

        .article-title {
            font-size: 1.75rem;
        }

        .article-image-container {
            margin: 0 1rem;
            height: 250px;
        }

        .article-body {
            padding: 1.5rem 1rem;
        }

        .article-footer {
            padding: 1rem;
        }

        .article-content {
            font-size: 1rem;
        }

        .related-news-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    @media (max-width: 480px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .article-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .article-title {
            font-size: 1.5rem;
        }

        .article-image-container {
            margin: 0;
            border-radius: 0;
        }

        .related-content {
            padding: 0.75rem;
        }

        .btn-edit-detail,
        .btn-delete-detail {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
    }
</style>

<?php include 'partials/footer.php'; ?>
</body>
</html>