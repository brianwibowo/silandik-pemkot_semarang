<?php
session_start();
include '../../koneksi.php';

// Penyesuaian: pengurus juga boleh akses edit berita
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header('Location: ../../authentification/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ../../index.php');
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM berita WHERE id = $id");
$berita = mysqli_fetch_assoc($data);

if (!$berita) {
    header('Location: ../../index.php');
    exit;
}

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $kategori = $_POST['kategori'];

    // Validasi input
    if (empty($judul) || empty($isi) || empty($kategori)) {
        $error = "Semua field wajib diisi.";
    } else {
        $gambarBaru = $berita['gambar'];
        
        // Handle upload gambar baru
        if (!empty($_FILES['gambar']['name'])) {
            $gambar = $_FILES['gambar']['name'];
            $tmp = $_FILES['gambar']['tmp_name'];
            $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
            $allowed = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($ext, $allowed)) {
                // Create unique filename
                $newName = time() . '_' . uniqid() . '.' . $ext;
                $target = '../../upload/berita/' . $newName;
                
                // Create directory if not exists
                if (!is_dir('../../upload/berita')) {
                    mkdir('../../upload/berita', 0755, true);
                }
                
                if (move_uploaded_file($tmp, $target)) {
                    // Delete old image if exists and different
                    if (!empty($berita['gambar']) && $berita['gambar'] != $newName && file_exists('../../upload/berita/' . $berita['gambar'])) {
                        unlink('../../upload/berita/' . $berita['gambar']);
                    }
                    $gambarBaru = $newName;
                } else {
                    $error = "Gagal upload gambar.";
                }
            } else {
                $error = "Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.";
            }
        }
        
        if (empty($error)) {
            // Update berita with updated_at timestamp
            $stmt = $conn->prepare("UPDATE berita SET judul=?, isi=?, kategori=?, gambar=?, created_at=CURRENT_TIMESTAMP WHERE id=?");
            $stmt->bind_param("ssssi", $judul, $isi, $kategori, $gambarBaru, $id);
            
            if ($stmt->execute()) {
                $success = "Berita berhasil diperbarui.";
                // Redirect to detail page or beranda after 2 seconds
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'detail_berita.php?id=$id';
                    }, 2000);
                </script>";
            } else {
                $error = "Gagal memperbarui berita: " . $conn->error;
            }
        }
    }
}
?>

<?php include '../../partials/head.php'; ?>

<div id="background">
    <?php include '../../partials/sidebar.php'; ?>

    <main>
        <div class="container-fluid px-4">
            <!-- Navigation Header -->
            <div class="navigation-header">
                <a href="detail_berita.php?id=<?= $berita['id'] ?>" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Detail</span>
                </a>
                <a href="index.php" class="back-btn">
                    <i class="fas fa-home"></i>
                    <span>Ke Beranda</span>
                </a>
            </div>

            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Edit Berita</h1>
                <p class="page-subtitle">Perbarui informasi berita</p>
            </div>

            <!-- Alert Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= $success; ?>
                    <small class="d-block mt-1">Anda akan diarahkan ke halaman detail...</small>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $error; ?>
                </div>
            <?php endif; ?>

            <!-- Edit Form -->
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data" class="edit-form">
                    <div class="form-group">
                        <label for="judul" class="form-label">
                            <i class="fas fa-heading"></i>
                            Judul Berita
                        </label>
                        <input type="text" 
                               name="judul" 
                               id="judul"
                               class="form-control" 
                               value="<?= htmlspecialchars($berita['judul']); ?>" 
                               placeholder="Masukkan judul berita..."
                               required>
                    </div>

                    <div class="form-group">
                        <label for="kategori" class="form-label">
                            <i class="fas fa-tags"></i>
                            Kategori
                        </label>
                        <select name="kategori" id="kategori" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <option value="dinas" <?= $berita['kategori'] == 'dinas' ? 'selected' : ''; ?>>Berita Dinas</option>
                            <option value="sekolah" <?= $berita['kategori'] == 'sekolah' ? 'selected' : ''; ?>>Berita Sekolah</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="isi" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Isi Berita
                        </label>
                        <textarea name="isi" 
                                  id="isi"
                                  rows="8" 
                                  class="form-control" 
                                  placeholder="Tulis isi berita di sini..."
                                  required><?= htmlspecialchars($berita['isi']); ?></textarea>
                        <small class="form-text">Minimum 50 karakter</small>
                    </div>

                    <div class="form-group">
                        <label for="gambar" class="form-label">
                            <i class="fas fa-image"></i>
                            Gambar Berita
                        </label>
                        <input type="file" 
                               name="gambar" 
                               id="gambar"
                               class="form-control" 
                               accept="image/*">
                        <small class="form-text">Format: JPG, JPEG, PNG, GIF. Maksimal 5MB. Kosongkan jika tidak ingin mengganti.</small>
                        
                        <?php if (!empty($berita['gambar']) && file_exists('../../upload/berita/' . $berita['gambar'])): ?>
                            <div class="current-image">
                                <label class="current-image-label">Gambar Saat Ini:</label>
                                <div class="image-preview">
                                    <img src="../../upload/berita/<?= htmlspecialchars($berita['gambar']); ?>" 
                                         alt="Gambar berita saat ini" 
                                         class="preview-img">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Perbarui Berita
                        </button>
                        <a href="detail_berita.php?id=<?= $berita['id'] ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<style>
    /* Global Styles */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background-color: #f8f9fa;
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

    /* Page Header */
    .page-header {
        text-align: center;
        margin-bottom: 2rem;
        padding: 1rem 0;
    }

    .page-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        font-size: 1.1rem;
        color: #6c757d;
        margin: 0;
    }

    /* Alert Styles */
    .alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: none;
    }

    .alert-success {
        background-color: #d1edff;
        color: #0c5460;
        border-left: 4px solid #28a745;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    /* Form Container */
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .edit-form {
        max-width: 800px;
        margin: 0 auto;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: white;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
        line-height: 1.6;
    }

    .form-text {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Current Image */
    .current-image {
        margin-top: 1rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .current-image-label {
        display: block;
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .image-preview {
        text-align: center;
    }

    .preview-img {
        max-width: 200px;
        max-height: 150px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        object-fit: cover;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: #4a90e2;
        color: white;
    }

    .btn-primary:hover {
        background: #357abd;
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .navigation-header {
            flex-direction: column;
            gap: 0.5rem;
            align-items: stretch;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .form-container {
            padding: 1.5rem;
            margin: 0 -0.5rem;
            border-radius: 8px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .form-container {
            padding: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
        }
    }
</style>

<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.edit-form');
        const isiTextarea = document.getElementById('isi');
        
        form.addEventListener('submit', function(e) {
            if (isiTextarea.value.trim().length < 50) {
                e.preventDefault();
                alert('Isi berita minimal 50 karakter');
                isiTextarea.focus();
            }
        });
        
        // File size validation
        const fileInput = document.getElementById('gambar');
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.size > 5 * 1024 * 1024) { // 5MB
                alert('Ukuran file terlalu besar. Maksimal 5MB');
                this.value = '';
            }
        });
    });
</script>

<?php include '../../partials/footer.php'; ?>
</body>
</html>