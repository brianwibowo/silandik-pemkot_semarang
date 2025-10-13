<?php
session_start();
include '../../koneksi.php';

// Cek role
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header('Location: ../../authentification/login.php');
    exit;
}

// Buat CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";

// Proses tambah berita
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token tidak valid. Silakan muat ulang halaman.";
    } else {
        $judul = trim($_POST['judul']);
        $isi = trim($_POST['isi']);
        $kategori = $_POST['kategori'];
        $penulis = $_SESSION['username'] ?? 'Admin';

        $isi = strip_tags($isi, '<p><br><b><i><strong><em><ul><ol><li><a>');

        if (empty($judul) || empty($isi)) {
            $error = "Judul dan isi berita harus diisi.";
        } else {
            $gambar_name = "";

            // Proses upload gambar
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $gambar = $_FILES['gambar']['name'];
                $tmp = $_FILES['gambar']['tmp_name'];
                $file_size = $_FILES['gambar']['size'];
                $file_type = $_FILES['gambar']['type'];

                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!in_array($file_type, $allowed_types)) {
                    $error = "Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF.";
                } elseif ($file_size > 5 * 1024 * 1024) {
                    $error = "Ukuran file terlalu besar. Maksimal 5MB.";
                } else {
                    $file_extension = pathinfo($gambar, PATHINFO_EXTENSION);
                    $gambar_name = md5(uniqid(mt_rand(), true)) . '.' . $file_extension;

                    $upload_dir = '../../upload/berita/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $target = $upload_dir . $gambar_name;

                    if (!move_uploaded_file($tmp, $target)) {
                        $error = "Gagal mengunggah gambar.";
                        $gambar_name = "";
                    }
                }
            }

            // Simpan ke database jika tidak ada error
            if (empty($error)) {
                $stmt = $conn->prepare("INSERT INTO berita (judul, isi, gambar, penulis, kategori, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sssss", $judul, $isi, $gambar_name, $penulis, $kategori);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Berita berhasil ditambahkan.";
                    header("Location: ../../index.php");
                    exit;
                } else {
                    $error = "Gagal menyimpan berita ke database: " . $conn->error;
                    if (!empty($gambar_name) && file_exists($target)) {
                        unlink($target);
                    }
                }

                if ($stmt) $stmt->close();
            }
        }
    }
}

include '../../partials/head.php';
include '../../partials/sidebar.php';
?>

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
</style>

<script src="../../assets/js/sweetalert2.all.min.js"></script>
<?php if ($error): ?>
<script>
    Swal.fire({
        title: 'Gagal!',
        text: <?= json_encode($error); ?>,
        icon: 'error',
        confirmButtonColor: '#d33'
    });
</script>
<?php endif; ?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Tambah Berita</h1>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

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

<?php include '../../partials/footer.php'; ?>
