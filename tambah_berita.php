<?php
session_start();
include 'koneksi.php';

// Penyesuaian: pengurus juga boleh tambah berita
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header('Location: login.php');
    exit;
}

$success = $error = "";

// Proses Tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $kategori = $_POST['kategori'];
    $penulis = $_SESSION['username'] ?? 'Admin';

    // Validasi input
    if (empty($judul) || empty($isi)) {
        $error = "Judul dan isi berita harus diisi.";
    } else {
        $gambar_name = "";
        
        // Proses upload gambar jika ada
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $gambar = $_FILES['gambar']['name'];
            $tmp = $_FILES['gambar']['tmp_name'];
            $file_size = $_FILES['gambar']['size'];
            $file_type = $_FILES['gambar']['type'];
            
            // Validasi tipe file
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file_type, $allowed_types)) {
                $error = "Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF.";
            }
            // Validasi ukuran file (max 5MB)
            elseif ($file_size > 5 * 1024 * 1024) {
                $error = "Ukuran file terlalu besar. Maksimal 5MB.";
            }
            else {
                // Buat nama file unik untuk menghindari konflik
                $file_extension = pathinfo($gambar, PATHINFO_EXTENSION);
                $gambar_name = uniqid() . '_' . time() . '.' . $file_extension;
                
                // Pastikan folder upload/berita/ ada
                $upload_dir = 'upload/berita/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $target = $upload_dir . $gambar_name;
                
                // Upload file
                if (!move_uploaded_file($tmp, $target)) {
                    $error = "Gagal mengunggah gambar.";
                    $gambar_name = ""; // Reset jika gagal
                }
            }
        }
        
        // Simpan ke database jika tidak ada error
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO berita (judul, isi, gambar, penulis, kategori, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $judul, $isi, $gambar_name, $penulis, $kategori);

            if ($stmt->execute()) {
                $success = "Berita berhasil ditambahkan.";
                // Reset form setelah berhasil
                $judul = $isi = "";
            } else {
                $error = "Gagal menyimpan berita ke database: " . $conn->error;
                // Hapus file yang sudah diupload jika gagal simpan ke DB
                if (!empty($gambar_name) && file_exists($target)) {
                    unlink($target);
                }
            }
            
            if ($stmt) $stmt->close();
        }
    }
}

include 'partials/head.php';
include 'sidebar.php';
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Tambah Berita</h1>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required 
                                   value="<?= isset($judul) ? htmlspecialchars($judul) : '' ?>"
                                   placeholder="Masukkan judul berita">
                        </div>
                        
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="dinas" <?= (isset($kategori) && $kategori === 'dinas') ? 'selected' : '' ?>>Berita Dinas</option>
                                <option value="sekolah" <?= (isset($kategori) && $kategori === 'sekolah') ? 'selected' : '' ?>>Berita Sekolah</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="isi" class="form-label">Isi Berita <span class="text-danger">*</span></label>
                            <textarea name="isi" rows="8" class="form-control" required 
                                      placeholder="Tulis isi berita di sini..."><?= isset($isi) ? htmlspecialchars($isi) : '' ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <div class="form-text">
                                Format yang diizinkan: JPG, PNG, GIF. Maksimal 5MB.
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Berita
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: none;
}

.form-label {
    font-weight: 600;
    color: #5a5c69;
}

.text-danger {
    color: #e74a3b !important;
}

.btn {
    border-radius: 0.35rem;
}

.alert {
    border-radius: 0.35rem;
}
</style>

<?php include 'partials/footer.php'; ?>








